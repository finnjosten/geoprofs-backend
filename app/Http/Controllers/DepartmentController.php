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

        return response()->json(["departments" => $departments]);
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
