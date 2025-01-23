<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceReason;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;

class AttendanceReasonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() {
        $reasons = AttendanceReason::all();

        if (empty($reasons)) {
            return response()->json([
                "error" => "No reasons",
                "code" => "no_reasons",
                "message" => "No reasons found"
            ], 404);
        }

        return response()->json([
            "success" => true,
            "reasons" => $reasons
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($slug) {
        $reason = AttendanceReason::where('slug', $slug)->first();

        if (empty($reason)) {
            return response()->json([
                "error" => "No reason",
                "code" => "no_reason",
                "message" => "No reason found"
            ], 404);
        }

        return response()->json([
            "success" => true,
            "reason" => $reason
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function store() {
        $data = Request::only('slug', 'name', 'description', 'default');

        // Check if the default is set if not set it to false other wise get the bool value
        if (!isset($data['default'])) {
            $data['default'] = false;
        } else {
            $data['default'] = boolval($data['default']);
        }

        $validator = Validator::make($data, [
            'slug' => 'required|string|unique:attendance_reasons',
            'name' => 'required|string',
            'description' => 'nullable|string',
            'default' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
                'code' => 'validation_error',
            ], 422);
        }

        if ($data['slug'] == 'overig') {
            return response()->json([
                'error' => 'Slug is not allowed',
                'code' => 'slug_not_allowed',
                'message' => "The slug 'overig' is not allowed as its a reserved slug"
            ], 422);
        }

        $reason = AttendanceReason::create($data);

        return response()->json([
            "success" => true,
            "reason" => $reason
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($slug) {
        $data = Request::only('slug', 'name', 'description');

        // Check if the default is set if not set it to false other wise get the bool value
        if (!isset($data['default'])) {
            $data['default'] = false;
        } else {
            $data['default'] = boolval($data['default']);
        }

        $reason = AttendanceReason::where('slug', $slug)->first();

        if (empty($reason)) {
            return response()->json([
                "error" => "No reason",
                "code" => "no_reason",
                "message" => "No reason found"
            ], 404);
        }

        $validator = Validator::make($data, [
            'slug' => 'required|string|unique:attendance_reasons',
            'name' => 'required|string',
            'description' => 'nullable|string',
            'default' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
                'code' => 'validation_error',
            ], 422);
        }

        if ($data['slug'] == 'overig') {
            return response()->json([
                'error' => 'Slug is not allowed',
                'code' => 'slug_not_allowed',
                'message' => "The slug 'overig' is not allowed as its a reserved slug"
            ], 422);
        }

        $reason->update($data);

        return response()->json([
            "success" => true,
            "reason" => $reason,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($slug) {

        $reason = AttendanceReason::where('slug', $slug)->first();

        if (empty($reason)) {
            return response()->json([
                "error" => "No reason",
                "code" => "no_reason",
                "message" => "No reason found"
            ], 404);
        }

        if ($reason->slug == 'overig') {
            return response()->json([
                'error' => 'Reason cant be removed',
                'code' => 'reason_cant_be_removed',
                'message' => "The reason 'overig' is not allowed to be removed as its a reserved reason"
            ], 422);
        }

        $reason->delete();

        return response()->json([
            "success" => true,
            "message" => "Reason deleted successfully"
        ]);
    }
}
