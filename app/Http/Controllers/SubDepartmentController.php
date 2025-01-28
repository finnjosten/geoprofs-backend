<?php

namespace App\Http\Controllers;

use App\Models\Subdepartment;
use Illuminate\Support\Facades\Request;

class SubdepartmentController extends Controller {

    /**
     * Display a listing of the resource.
     */
    public function index() {
        return response()->json(["data" => Subdepartment::all()]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {
        //
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
    public function show(Subdepartment $Subdepartment) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Subdepartment $Subdepartment) {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Subdepartment $Subdepartment) {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subdepartment $Subdepartment) {
        //
    }
}
