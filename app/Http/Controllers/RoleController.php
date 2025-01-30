<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Support\Facades\Request;

class RoleController extends Controller {

    /**
     * Return all the roles.
     */
    public function index() {

        $roles = Role::all();

        if (!$roles) {
            return response()->json([
                'error' => 'No roles found',
                'message' => 'No roles found!',
                'code' => 'no_roles_found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            "roles" => $roles,
        ]);
    }

}
