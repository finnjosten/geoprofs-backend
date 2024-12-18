<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Attendance;
use App\Models\User;
use App\Models\Year;
use App\Models\Week;
use App\Models\Day;

class AttendanceController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) {
        $user_id = $request->user()->id;
        $attendances = Attendance::where('user_id', $user_id)->get();

        if (empty($attendances)) {
            return response()->json([
                "error" => "No attedance",
                "code" => "no_attendance",
                "message" => "No attendance found"
            ], 404);
        }

        return response()->json([
            "success" => true,
            "attendances" => $attendances
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {

        $data = $request->only('week_number', 'date', 'morning', 'afternoon');

        $validator = Validator::make($data, [
            'date' => 'required|date',
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

        // strip the time of the date
        $request->merge([
            'user_id' => $request->user()->id,
            'date' => date('Y-m-d', strtotime($request->date)),
            'status' => 'pending',
        ]);

        try {
            // get the week number from the date()
            $weekNumber = date('W', strtotime($request->date));

            $year = Year::firstOrCreate(['year_number' => date('Y', strtotime($request->date))]);
            $week = Week::firstOrCreate(['week_number' => $weekNumber, 'year_id' => $year->id]);
            $day = Day::firstOrCreate(['date' => $request->date, 'week_id' => $week->id]);

            $attendance = Attendance::updateOrCreate(
                [
                    'day_id' => $day->id,
                    'user_id' => $request->user_id
                ],
                [
                    'morning' => $request->morning ?? 0, // Default to 0 if not provided
                    'afternoon' => $request->afternoon ?? 0, // Default to 0 if not provided
                    'status' => $request->status,
                ]
            );
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'code' => 'error',
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Attendance stored successfully',
            'attendance' => $attendance,
        ]);

    }

     /**
     * Display the specified attendance.
     * @urlParam id required The ID of the attendance. Example: 2
     */
    public function show(Request $request, $attendance_id) {

        $attendance = Attendance::whereId($attendance_id)->first();
        $attendance_user = User::whereId($attendance->user_id)->first();

        if (!$attendance) {
            return response()->json([
                'error' => "Attendance not found",
                'code' => 'attendance_not_found',
                'message' => 'Attendance not found',
            ], 404);
        }

        if (!$attendance_user) {
            return response()->json([
                'error' => "User not found",
                'code' => 'user_not_found',
                'message' => 'User not found',
            ], 404);
        }

        if (
            $attendance_user->id != $request->user()->id &&
            $attendance_user->supervisor_id != $request->user()->id
        ) {
            return response()->json([
                'error' => "Unauthorized",
                'code' => 'unauthorized',
                'message' => 'You are not authorized to view this attendance',
            ], 401);
        }

        return response()->json([
            'success' => true,
            'attendance' => $attendance,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $attendance_id) {

        $attendance = Attendance::whereId($attendance_id)->first();
        $attendance_user = User::whereId($attendance->user_id)->first();

        if (!$attendance) {
            return response()->json([
                'error' => "Attendance not found",
                'code' => 'attendance_not_found',
                'message' => 'Attendance not found',
            ], 404);
        }

        if (!$attendance_user) {
            return response()->json([
                'error' => "User not found",
                'code' => 'user_not_found',
                'message' => 'User not found',
            ], 404);
        }


        if (
            $attendance_user->id != $request->user()->id &&
            $attendance_user->supervisor_id != $request->user()->id
        ) {
            return response()->json([
                'error' => "Unauthorized",
                'code' => 'unauthorized',
                'message' => 'You are not authorized to view this attendance',
            ], 401);
        }


        // validate
        $data = $request->only('date', 'morning', 'afternoon', 'status');

        $validator = Validator::make($data, [
            'date' => 'nullable|date',
            'morning' => 'nullable|integer|min:0|max:5',
            'afternoon' => 'nullable|integer|min:0|max:5',
            'status' => 'nullable|string|in:pending,approved,rejected',
        ]);

        // Check if the validation fails
        if ($validator->fails()) {
            // Return a JSON response with validation errors
            return response()->json([
                'error' => $validator->errors(),
                'code' => 'validation_error',
            ], 422);
        }


        try {

            $attendance->update([
                'date' => $data['date'] ?? $attendance->date,
                'morning' => $data['morning'] ?? $attendance->morning,
                'afternoon' => $data['afternoon'] ?? $attendance->afternoon,
                'status' => $data['status'] ?? $attendance->status,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'code' => 'error',
            ], 500);
        }


        return response()->json([
            'status' => 'success',
            'message' => 'Attendance updated successfully',
            'attendance' => $attendance,
        ]);


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $attendance_id) {

        $attendance = Attendance::whereId($attendance_id)->first();
        $attendance_user = User::whereId($attendance->user_id)->first();

        if (!$attendance) {
            return response()->json([
                'error' => "Attendance not found",
                'code' => 'attendance_not_found',
                'message' => 'Attendance not found',
            ], 404);
        }

        if (!$attendance_user) {
            return response()->json([
                'error' => "User not found",
                'code' => 'user_not_found',
                'message' => 'User not found',
            ], 404);
        }


        if (
            $attendance_user->id != $request->user()->id &&
            $attendance_user->supervisor_id != $request->user()->id
        ) {
            return response()->json([
                'error' => "Unauthorized",
                'code' => 'unauthorized',
                'message' => 'You are not authorized to view this attendance',
            ], 401);
        }

        $attendance->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Attendance deleted successfully',
        ]);
    }
}
