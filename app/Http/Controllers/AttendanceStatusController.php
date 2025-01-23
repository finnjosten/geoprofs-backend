<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceStatus;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;

class AttendanceStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() {
        $statuses = AttendanceStatus::all();

        if (empty($statuses)) {
            return response()->json([
                "error" => "No statuses",
                "code" => "no_statuses",
                "message" => "No statuses found"
            ], 404);
        }

        return response()->json([
            "success" => true,
            "statuses" => $statuses
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($slug) {
        $status = AttendanceStatus::where('slug', $slug)->first();

        if (empty($status)) {
            return response()->json([
                "error" => "No status",
                "code" => "no_status",
                "message" => "No status found"
            ], 404);
        }

        return response()->json([
            "success" => true,
            "status" => $status
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function store() {
        $data = Request::only('slug', 'name', 'description', 'show_in_agenda', 'default', 'default_after_create', 'default_approve', 'default_deny');

        // Check if the show_in_agenda is set if not set it to false other wise get the bool value
        if (!isset($data['show_in_agenda'])) {
            $data['show_in_agenda'] = false;
        } else {
            $data['show_in_agenda'] = boolval($data['show_in_agenda']);
        }

        // Check if the default is set if not set it to false other wise get the bool value
        if (!isset($data['default'])) {
            $data['default'] = false;
        } else {
            $data['default'] = boolval($data['default']);
        }

        // Check if the default_after_create is set if not set it to false other wise get the bool value
        if (!isset($data['default_after_create'])) {
            $data['default_after_create'] = false;
        } else {
            $data['default_after_create'] = boolval($data['default_after_create']);
        }

        // Check if the default_approve is set if not set it to false other wise get the bool value
        if (!isset($data['default_approve'])) {
            $data['default_approve'] = false;
        } else {
            $data['default_approve'] = boolval($data['default_approve']);
        }

        // Check if the default_deny is set if not set it to false other wise get the bool value
        if (!isset($data['default_deny'])) {
            $data['default_deny'] = false;
        } else {
            $data['default_deny'] = boolval($data['default_deny']);
        }

        $validator = Validator::make($data, [
            'slug' => 'required|string|unique:attendance_statuses',
            'name' => 'required|string',
            'description' => 'nullable|string',
            'show_in_agenda' => 'nullable|boolean',
            'default' => 'nullable|boolean',
            'default_after_create' => 'nullable|boolean',
            'default_approve' => 'nullable|boolean',
            'default_deny' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
                'code' => 'validation_error',
            ], 422);
        }

        $status = AttendanceStatus::create($data);

        return response()->json([
            "success" => true,
            "status" => $status
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($slug) {
        $data = Request::only('slug', 'name', 'description', 'show_in_agenda', 'default', 'default_after_create', 'default_approve', 'default_deny');

        // Check if the show_in_agenda is set if not set it to false other wise get the bool value
        if (!isset($data['show_in_agenda'])) {
            $data['show_in_agenda'] = false;
        } else {
            $data['show_in_agenda'] = boolval($data['show_in_agenda']);
        }

        // Check if the default is set if not set it to false other wise get the bool value
        if (!isset($data['default'])) {
            $data['default'] = false;
        } else {
            $data['default'] = boolval($data['default']);
        }

        // Check if the default_after_create is set if not set it to false other wise get the bool value
        if (!isset($data['default_after_create'])) {
            $data['default_after_create'] = false;
        } else {
            $data['default_after_create'] = boolval($data['default_after_create']);
        }

        // Check if the default_approve is set if not set it to false other wise get the bool value
        if (!isset($data['default_approve'])) {
            $data['default_approve'] = false;
        } else {
            $data['default_approve'] = boolval($data['default_approve']);
        }

        // Check if the default_deny is set if not set it to false other wise get the bool value
        if (!isset($data['default_deny'])) {
            $data['default_deny'] = false;
        } else {
            $data['default_deny'] = boolval($data['default_deny']);
        }

        $status = AttendanceStatus::where('slug', $slug)->first();

        if (empty($status)) {
            return response()->json([
                "error" => "No status",
                "code" => "no_status",
                "message" => "No status found"
            ], 404);
        }

        $validator = Validator::make($data, [
            'slug' => 'required|string|unique:attendance_statuses',
            'name' => 'required|string',
            'description' => 'nullable|string',
            'show_in_agenda' => 'nullable|boolean',
            'default' => 'nullable|boolean',
            'default_after_create' => 'nullable|boolean',
            'default_approve' => 'nullable|boolean',
            'default_deny' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
                'code' => 'validation_error',
            ], 422);
        }

        $status->update($data);

        return response()->json([
            "success" => true,
            "status" => $status
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($slug) {

        $status = AttendanceStatus::where('slug', $slug)->first();

        if (empty($status)) {
            return response()->json([
                "error" => "No status",
                "code" => "no_status",
                "message" => "No status found"
            ], 404);
        }

        $status->delete();

        return response()->json([
            "success" => true,
            "message" => "Status deleted successfully"
        ]);
    }
}
