<?php

namespace App\Http\Controllers;

use App\Models\Subdepartment;
use App\Http\Requests\StoreSubdepartmentRequest;
use App\Http\Requests\UpdateSubdepartmentRequest;

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
    public function store(StoreSubdepartmentRequest $request) {
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
    public function update(UpdateSubdepartmentRequest $request, Subdepartment $Subdepartment) {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subdepartment $Subdepartment) {
        //
    }
}
