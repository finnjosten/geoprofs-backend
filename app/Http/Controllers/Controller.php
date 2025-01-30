<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Request;

abstract class Controller {

    /**
     * Check if the user has the required permissions
     *
     * @param Array $roles
     * @param bool $use_response
     * @return bool
     */
    public function checkPermission(Array $roles, bool $use_response = true) {

        $user_role = Request::user()->role_slug;

        if (!in_array($user_role, $roles)) {

            if ($use_response) {
                response()->json([
                    "error" => "Unauthorized",
                    "code" => "unauthorized",
                    "message" => "You are not authorized to perform this action"
                ], 401)->send();
                exit;
            }

            return false;
        }

        return true;
    }

}
