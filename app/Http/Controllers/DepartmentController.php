<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Support\Facades\Request;

/**
 * @group Department management
 * @authenticated
 *
 * APIs for managing users
 */
class DepartmentController extends Controller {

    /**
     * Return a all the departments.
     */
    public function index() {

        $departments = Department::all();

        if (!$departments) {
            return response()->json([
                'error' => 'No departments found',
                'message' => 'No departments found!',
                'code' => 'no_departments_found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            "departments" => $departments,
        ]);
    }

}
