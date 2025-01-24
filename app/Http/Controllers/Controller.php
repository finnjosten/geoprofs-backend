<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Request;

abstract class Controller {

    public function checkPermission(Array $roles, $return_type = 'response') {
        $user_role = Request::user()->role_slug;

        if (!in_array($user_role, $roles)) {

            if ($return_type == 'response') {
                return response()->json([
                    "error" => "Unauthorized",
                    "code" => "unauthorized",
                    "message" => "You are not authorized to perform this action"
                ], 401);
            }

            return false;
        }

        return true;
    }

}
