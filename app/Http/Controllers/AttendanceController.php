<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Attendance;
use App\Models\AttendanceStatus;
use App\Models\AttendanceReason;
use App\Models\User;
use App\Models\Year;
use App\Models\Week;
use App\Models\Day;

/**
 * @group Attendance management
 * @authenticated
 *
 * APIs for managing users
 */
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
     * Store a newly created attendance.
     * @bodyParam date                 date of the attendance              Example: 2025-01-01
     * @bodyParam morning              the status for the monring          Example: 1 | max: 5
     * @bodyParam afternoon            the status for the monring          Example: 1 | max: 5
     */
    public function store(Request $request) {

        $data = $request->only('date', 'morning', 'afternoon', 'description');

        $validator = Validator::make($data, [
            'date' => 'required|date',
            'morning' => 'required|string|exists:attendance_reasons,slug',
            'afternoon' => 'required|string|exists:attendance_reasons,slug',
            'description' => 'nullable|string',
        ]);

        // Check if the validation fails
        if ($validator->fails()) {
            // Return a JSON response with validation errors
            return response()->json([
                'error' => 'Validation error',
                'errors' => $validator->errors(),
                'code' => 'validation_error',
            ], 422);
        }

        // Get the status that is marked to be used as default
        $status = AttendanceStatus::where('default_after_create', true)->first();

        // strip the time of the date
        $request->merge([
            'user_id' => $request->user()->id,
            'date' => date('Y-m-d', strtotime($request->date)),
            'status' => $status->slug,
        ]);

        // Check if the date is the current date or later
        if (strtotime($request->date) < strtotime(date('Y-m-d'))) {
            return response()->json([
                'error' => 'Invalid date',
                'code' => 'invalid_date',
                'message' => 'You can not add attendance for a future date',
            ], 422);
        }

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
                    'morning' => $request->morning ?? 0,
                    'afternoon' => $request->afternoon ?? 0,
                    'attendance_status' => $request->status,
                    'description' => $request->description ?? null,
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
     * @urlParam id required The ID of the attendance. Example: 2
     * @bodyParam date                 date of the attendance              Example: 2025-01-01
     * @bodyParam morning              the status for the monring          Example: 1 | max: 5
     * @bodyParam afternoon            the status for the monring          Example: 1 | max: 5
     * @bodyParam status               the attednace_status slug of the attendance          Example: pending | approved | rejected
     */
    public function update(Request $request, $attendance_id) {

        $attendance = Attendance::whereId($attendance_id)->first();
        $attendance_user = User::whereId($attendance->user_id)->first();
        $current_user = $request->user();

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
            !in_array($current_user->role_slug, ['manager', 'sub-manager', 'staff', 'ceo'])
        ) {
            return response()->json([
                'error' => "Unauthorized",
                'code' => 'unauthorized',
                'message' => 'You are not authorized to view this attendance',
            ], 401);
        }


        // validate
        $data = $request->only('date', 'morning', 'afternoon', 'attendance_status', 'description');

        $validator = Validator::make($data, [
            'date' => 'nullable|date',
            'morning' => 'nullable|string|exists:attendance_reasons,slug',
            'afternoon' => 'nullable|string|exists:attendance_reasons,slug',
            'description' => 'nullable|string',
            'attendance_status' => 'nullable|string|exists:attendance_statuses,slug',
        ]);

        // Check if the validation fails
        if ($validator->fails()) {
            // Return a JSON response with validation errors
            return response()->json([
                'error' => "Validation error",
                'errors' => $validator->errors(),
                'code' => 'validation_error',
            ], 422);
        }

        // Parse the date value
        if (isset($data['date'])) {
            $data['date'] = date('Y-m-d', strtotime($data['date']));
        }

        try {

            $attendance->update([
                'date' => $data['date'] ?? $attendance->date,
                'morning' => $data['morning'] ?? $attendance->morning,
                'afternoon' => $data['afternoon'] ?? $attendance->afternoon,
                'attendance_status' => $data['attendance_status'] ?? $attendance->attendance_status,
                'description' => $data['description'] ?? $attendance->description,
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
     * @urlParam id required The ID of the attendance. Example: 2
     */
    public function destroy(Request $request, $attendance_id) {

        $attendance = Attendance::whereId($attendance_id)->first();
        $attendance_user = User::whereId($attendance->user_id)->first();
        $current_user = $request->user();

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
            !in_array($current_user->role_slug, ['manager', 'sub-manager', 'staff', 'ceo'])
        ) {
            return response()->json([
                'error' => "Unauthorized",
                'code' => 'unauthorized',
                'message' => 'You are not authorized to view this attendance',
            ], 401);
        }

        $status = AttendanceStatus::where('default_after_create', true)->first();
        $reason = AttendanceReason::where('default', true)->first();

        // We can not delete an attendance so we will change it back to a default state
        $attendance->update([
            'morning' => $reason->slug,
            'afternoon' => $reason->slug,
            'status' => $status->slug,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Attendance can not be deleted so it has been changed back to a default state',
        ]);
    }






    /**
     * Approve the specified attendance.
     */
    public function approve(Request $request, $attendance_id) {

        $attendance = Attendance::whereId($attendance_id)->first();
        $attendance_user = User::whereId($attendance->user_id)->first();
        $current_user = $request->user();

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
            !in_array($current_user->role_slug, ['manager', 'sub-manager', 'staff', 'ceo'])
        ) {
            return response()->json([
                'error' => "Unauthorized",
                'code' => 'unauthorized',
                'message' => 'You are not authorized to view this attendance',
            ], 401);
        }


        // validate
        $data = $request->only('count_to_total');

        if (!isset($data['count_to_total'])) {
            $data['count_to_total'] = true;
        } else {
            $data['count_to_total'] = boolval($data['count_to_total']);
        }

        $validator = Validator::make($data, [
            'count_to_total' => 'nullable|boolean',
        ]);

        // Check if the validation fails
        if ($validator->fails()) {
            // Return a JSON response with validation errors
            return response()->json([
                'error' => "Validation error",
                'errors' => $validator->errors(),
                'code' => 'validation_error',
            ], 422);
        }

        try {

            $status = AttendanceStatus::where('default_approve', true)->first();

            $attendance->update([
                'attendance_status' => $status->slug,
                'count_to_total' => $data['count_to_total'] ?? $attendance->count_to_total,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'code' => 'error',
            ], 500);
        }

        // If the attendance is approved we will count it to the total
        if ($attendance->count_to_total) {

            $default_reason = AttendanceReason::where('default', true)->first();

            // Check if morning or afternoon or both is set
            if ($attendance->morning != $default_reason->slug && $attendance->afternoon != $default_reason->slug) {
                $attendance_user->used_attendance += 1;
                $attendance_user->save();
            } else if ($attendance->morning != $default_reason->slug || $attendance->afternoon != $default_reason->slug) {
                $attendance_user->used_attendance += 0.5;
                $attendance_user->save();
            }

        }

        return response()->json([
            'status' => 'success',
            'message' => 'Attendance approved successfully',
            'attendance' => $attendance,
        ]);



    }


    /**
     * Deny the specified attendance.
     */
    public function deny(Request $request, $attendance_id) {

        $attendance = Attendance::whereId($attendance_id)->first();
        $attendance_user = User::whereId($attendance->user_id)->first();
        $current_user = $request->user();

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
            !in_array($current_user->role_slug, ['manager', 'sub-manager', 'staff', 'ceo'])
        ) {
            return response()->json([
                'error' => "Unauthorized",
                'code' => 'unauthorized',
                'message' => 'You are not authorized to view this attendance',
            ], 401);
        }


        try {

            $status = AttendanceStatus::where('default_deny', true)->first();

            $attendance->update([
                'attendance_status' => $status->slug,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'code' => 'error',
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Attendance approved successfully',
            'attendance' => $attendance,
        ]);

    }








}
