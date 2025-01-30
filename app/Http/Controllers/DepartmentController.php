<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Support\Facades\Request;

class DepartmentController extends Controller {

    /**
     * Display a listing of the resource.
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

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Department $department) {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Department $department) {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department) {
        //
    }
}
