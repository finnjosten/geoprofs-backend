<?php

namespace App\Http\Controllers;

use App\Models\Subdepartment;
use Illuminate\Support\Facades\Request;

class SubdepartmentController extends Controller {

    /**
     * Return all the subdepartments.
     */
    public function index() {

        $Subdepartments = Subdepartment::all();

        if (!$Subdepartments) {
            return response()->json([
                'error' => 'No Subdepartments found',
                'message' => 'No Subdepartments found!',
                'code' => 'no_Subdepartments_found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            "subdepartments" => $Subdepartments,
        ]);
    }

}
