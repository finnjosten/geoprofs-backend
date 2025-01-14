<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Year;
use App\Models\Week;
use App\Models\Day;
use App\Models\Attendance;
use App\Models\AttendanceStatus;
use App\Models\Subdepartment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @group Agenda
 * @authenticated
 *
 * APIs for managing the agenda
 */
class AgendaController extends Controller
{

    /**
     * Retrieve the current agenda
     */
    public function show($slug = null) {

        try {

            // Get the department or subdepartment if the slug is filled
            $department = null;

            if (!empty($slug)) {
                $department = Department::where('slug', $slug)->first();
                if (!$department) $department = Subdepartment::where('slug', $slug)->first();
            }

            // Create an empty array to store the agenda
            $agenda = [];

            // Get all the years
            $years = Year::with(['weeks.days.attendance.user'])->get(); // Eager load related data

            // Go over all the years we have
            foreach ($years as $year) {

                $yearData = [];
                // Get all the weeks of that year
                $year->weeks = $year->weeks->sortBy('week_number');

                foreach ($year->weeks as $week) {

                    $weekData = [];
                    // Get all the days/dates of that week
                    $week->days = $week->days->sortBy('date');
                    foreach ($week->days as $day) {

                        $dayData = [];
                        // Go over all the atendances of that day
                        foreach ($day->attendance as $attendance) {

                            $attendance_status = AttendanceStatus::where('slug', $attendance->attendance_status)->first();
                            if ($attendance_status->show_in_agenda === false) {
                                continue;
                            }

                            // if the department is set, check if the user is in that department if not continue
                            if ( $department && (
                                    $attendance->user->department_slug !== $department->slug ||
                                    $attendance->user->sub_department_slug !== $department->sub_department_slug
                                )) {
                                continue;
                            }

                            // save the data to the agenda
                            $dayData[$attendance->user_id] = [
                                'morning' => $attendance->morning,
                                'afternoon' => $attendance->afternoon,
                            ];
                        }
                        $weekData[$day->date] = $dayData;

                    }
                    $yearData[$week->week_number] = $weekData;

                }
                $agenda[$year->year_number] = $yearData;

            }

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'code' => 'error',
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'agenda' => $agenda,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id) {
        $data = $request->only('user_id', 'week_number', 'date', 'morning', 'afternoon');

        $validator = Validator::make($data, [
            'user_id' => 'required|integer|exists:users,id',
            'date' => 'nullable|date',
            // 0 = aanwezig, 1 = ziek, 2 = vakantie, 3 = verlof, 4 = onbetaald verlof, 5 = feestdag
            'morning' => 'nullable|integer|min:0|max:5',
            // 0 = aanwezig, 1 = ziek, 2 = vakantie, 3 = verlof, 4 = onbetaald verlof, 5 = feestdag
            'afternoon' => 'nullable|integer|min:0|max:5',
        ]);

        // Check if the validation fails
        if ($validator->fails()) {
            // Return a JSON response with validation errors
            return response()->json([
                'error' => $validator->errors(),
                'code' => 'validation_error',
            ], 422);
        }

        $attendance = Attendance::find($id);

        if (!$attendance) {
            return response()->json([
                'error' => 'Attendance not found',
                'code' => 'not_found',
            ], 404);
        }

        try {

            $attendance->update([
                'morning' => $request->morning ?? 0, // Default to 0 if not provided
                'afternoon' => $request->afternoon ?? 0, // Default to 0 if not provided
            ]);

            // update the date if needed

            if ($request->date) {
                $weekNumber = date('W', strtotime($request->date));

                $year = Year::firstOrCreate(['year_number' => date('Y', strtotime($request->date))]);
                $week = Week::firstOrCreate(['week_number' => $weekNumber, 'year_id' => $year->id]);
                $day = Day::firstOrCreate(['date' => $request->date, 'week_id' => $week->id]);

                $attendance->update([
                    'day_id' => $day->id,
                ]);
            }

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'code' => 'error',
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Attendance updated successfully'
        ]);


    }

    public function generate() {
        // get the last 3 years,
        // loop over every week
        // loop over every day
        // and save all those years, weeks and days in he db

        try {

            $years = [];
            for ($i = 0; $i < 3; $i++) {
                $years[] = date('Y', strtotime("-$i year"));
            }

            foreach ($years as $year) {

                $db_year = Year::firstOrCreate(['year_number' => $year]);

                // loop over all the months in that year
                for ($month = 1; $month <= 12; $month++) {

                    // get the total amount of days for that month
                    $days = cal_days_in_month(CAL_GREGORIAN, $month, $year);

                    // loop over all the days in that month
                    for ($day = 1; $day <= $days; $day++) {
                        $date = date('Y-m-d', strtotime("$year-$month-$day"));

                        // check if date is already present in the db
                        if (Day::where('date', $date)->exists()) {
                            continue;
                        }

                        $week = date('W', strtotime($date));

                        $db_week = Week::firstOrCreate(['week_number' => $week, 'year_id' => $db_year->id]);
                        $db_day = Day::firstOrCreate(['date' => $date, 'week_id' => $db_week->id]);
                    }
                }

            }

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'code' => 'error',
            ], 500);
        }

    }
}
