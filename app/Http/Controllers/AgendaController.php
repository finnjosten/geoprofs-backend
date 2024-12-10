<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Year;
use App\Models\Week;
use App\Models\Day;
use App\Models\Attendance;

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
     * Store the new attendance
     */
    public function store(Request $request) {

        $data = $request->only('user_id', 'week_number', 'date', 'morning', 'afternoon');

        $validator = Validator::make($data, [
            'user_id' => 'required|integer|exists:users,id',
            'date' => 'required|date',
            // 0 = aanwezig, 1 = ziek, 2 = vakantie, 3 = verlof, 4 = onbetaald verlof, 5 = feestdag
            'morning' => 'nullable|integer|min:0|max:5',
            // 0 = aanwezig, 1 = ziek, 2 = vakantie, 3 = verlof, 4 = onbetaald verlof, 5 = feestdag
            'afternoon' => 'nullable|integer|min:0|max:5',
        ]);

        // strip the time of the date
        $request->merge(['date' => date('Y-m-d', strtotime($request->date))]);

        // Check if the validation fails
        if ($validator->fails()) {
            // Return a JSON response with validation errors
            return response()->json([
                'error' => $validator->errors(),
                'code' => 'validation_error',
            ], 422);
        }

        // get the week number from the date()
        $weekNumber = date('W', strtotime($request->date));

        $year = Year::firstOrCreate(['year_number' => date('Y', strtotime($request->date))]);
        $week = Week::firstOrCreate(['week_number' => $weekNumber, 'year_id' => $year->id]);
        $day = Day::firstOrCreate(['date' => $request->date, 'week_id' => $week->id]);

        Attendance::updateOrCreate(
            [
                'day_id' => $day->id,
                'user_id' => $request->user_id
            ],
            [
                'morning' => $request->morning ?? 0, // Default to 0 if not provided
                'afternoon' => $request->afternoon ?? 0, // Default to 0 if not provided
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Attendance updated successfully'
        ]);
    }

    /**
     * Retrieve the current agenda
     */
    public function show($department = null)
    {

        $department = Department::where('slug', $department)->first();

        $agenda = [];

        $years = Year::with(['weeks.days.attendance.user'])->get(); // Eager load related data

        foreach ($years as $year) {

            $yearData = [];
            $year->weeks = $year->weeks->sortBy('week_number');

            foreach ($year->weeks as $week) {

                $weekData = [];
                $week->days = $week->days->sortBy('date');
                foreach ($week->days as $day) {

                    $dayData = [];
                    foreach ($day->attendance as $attendance) {

                        if ($department && $attendance->user->department_slug !== $department->slug) {
                            continue;
                        }

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

        return response()->json($agenda);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function generate() {
        // get the last 3 years,
        // loop over every week
        // loop over every day
        // and save all those years, weeks and days in he db


        $years = [];
        for ($i = 0; $i < 3; $i++) {
            $years[] = date('Y', strtotime("-$i year"));
        }

        foreach ($years as $year) {
            for ($week = 1; $week <= 52; $week++) {
                for ($day = 1; $day <= 7; $day++) {
                    dd($year, $week, $day);
                    $date = date('Y-m-d', strtotime("$year-W$week-$day"));

                    dd($date);

                    $db_year = Year::firstOrCreate(['year_number' => $year]);
                    $db_week = Week::firstOrCreate(['week_number' => $week, 'year_id' => $db_year->id]);
                    $db_day = Day::firstOrCreate(['date' => $date, 'week_id' => $db_week->id]);
                }
            }
        }
    }
}
