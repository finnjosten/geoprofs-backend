<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

/**
 * @group Balance
 * @authenticated
 *
 * APIs for getting user attendance balance
 */
class BalanceController extends Controller {

    /**
     * Get the balance of a user
     * @urlParam id optional The ID of the user to get the balance of. Example: 2
     */
    public function balance(User $user = null) {

        if (!empty($user)) {
            $this->checkPermission(['manager', 'sub-manager', 'staff', 'ceo'], false);
        }

        if (empty($user)) {
            $user = Auth::user();
        }

        $used = $user->used_attendance;
        $max = $user->max_attendance;
        $balance = $user->max_attendance - $user->used_attendance;

        return response()->json([
            'success' => true,
            'used' => $used,
            'max' => $max,
            'balance' => $balance
        ]);

    }

}
