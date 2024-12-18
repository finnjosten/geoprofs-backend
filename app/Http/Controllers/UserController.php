<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @group User management
 * @authenticated
 *
 * APIs for managing users
 */
class UserController extends Controller {

    private $request_fields = array(
        'email',
        'password',

        'role_slug',
        'department_slug',
        'subdepartment_slug',
        'supervisor_id',

        'blocked',
        'verified',

        'first_name',
        'sure_name',
        'bsn',
        'date_of_service',

        'sick_days',
        'vac_days',
        'personal_days',
        'max_vac_days'
    );

    private $validator_fields = array(
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:8|max:64',

        'role_slug' => 'nullable|string|exists:roles,slug',
        'department_slug' => 'nullable|string|exists:departments,slug',
        'subdepartment_slug' => 'nullable|string|exists:subdepartments,slug',
        'supervisor_id' => 'nullable|integer|exists:users,id',

        'blocked' => 'nullable|boolean',
        'verified' => 'nullable|boolean',

        'first_name' => 'required|string|max:64',
        'sure_name' => 'required|string|max:64',
        'bsn' => 'required|string|max:9|unique:users,bsn',
        'date_of_service' => 'required|date',

        'sick_days' => 'nullable|integer',
        'vac_days' => 'nullable|integer',
        'personal_days' => 'nullable|integer',
        'max_vac_days' => 'nullable|integer',
    );

    /**
     * Display all the users.
     */
    public function index() {
        $users = User::all();

        if (!$users) {
            return response()->json([
                'error' => 'No user found',
                'message' => 'No users found!',
                'code' => 'no_users_found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            "users" => $users,
        ]);
    }

    /**
     * Store a newly created user.
     * @bodyParam email                 email of the user.              Example: john@geoprofs.com
     * @bodyParam password              password of the user.           Example: password1
     * @bodyParam role_slug             slug of the role.               Example: medewerker
     * @bodyParam department_slug       slug of the department          Example: geoict
     * @bodyParam subdepartment_slug    slug of the subdepartment       Example: scanning
     * @bodyParam supervisor_id         user id of the supervisor       Example: 1
     * @bodyParam blocked               true/false if user is blocked   Example: false
     * @bodyParam verified              true/false if user is verified  Example: true
     * @bodyParam first_name            first name of the user          Example: John
     * @bodyParam sure_name             sure name of the user           Example: Doe
     * @bodyParam bsn                   BSN of the user                 Example: 123456789
     * @bodyParam date_of_service       Date of when the user started   Example: 2021-01-01
     * @bodyParam sick_days             ammount of sick days used       Example: 0
     * @bodyParam vac_days              ammount of vacation days used   Example: 0
     * @bodyParam personal_days         ammount of personal days used   Example: 0
     * @bodyParam max_vac_days          max ammount of leave days       Example: 30
     */
    public function store(Request $request) {

        $data = $request->only(...$this->request_fields);

        // Define validation rules
        $validator = Validator::make($data, $this->validator_fields);

        // Check if the validation fails
        if ($validator->fails()) {
            // Return a JSON response with validation errors
            return response()->json([
                "error" => "Validation error",
                "code" => "validation_error",
                'errors' => $validator->errors(),
                'code' => 'validation_error',
            ], 422);
        }

        // Create the user with the validated data
        $user = User::create([
            'email' => $data['email'],
            'password' => bcrypt($data['password']),

            'role_slug' => $data['role_slug'] ?? 1, // Default role ID to 1 if not provided
            'department_slug' => $data['department_slug'] ?? null,
            'subdepartment_slug' => $data['subdepartment_slug'] ?? null,
            'supervisor_id' => $data['supervisor_id'] ?? null,

            'blocked' => $data['blocked'] ?? false,
            'verified' => $data['verified'] ?? false,

            'first_name' => $data['first_name'],
            'sure_name' => $data['sure_name'],
            'bsn' => $data['bsn'],
            'date_of_service' => $data['date_of_service'],

            'sick_days' => $data['sick_days'] ?? 0,
            'vac_days' => $data['vac_days'] ?? 0,
            'personal_days' => $data['personal_days'] ?? 0,
            'max_vac_days' => $data['max_vac_days'] ?? 366,
        ]);


        // Return a success response
        return response()->json([
            'success' => true,
            'user' => $user,
        ]);
    }

    /**
     * Display the specified user.
     * @urlParam id required The ID of the user. Example: 2
     */
    public function show($user_id) {

        $user = User::whereId($user_id)->first();

        if (!$user) {
            return response()->json([
                'error' => "User not found",
                'code' => 'user_not_found',
                'message' => 'User not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'user' => $user,
        ]);
    }

    /**
     * Display the current user.
     */
    public function showCurrent(Request $request) {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'error' => "User not found",
                'code' => 'user_not_found',
                'message' => 'User not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'user' => $user,
        ]);
    }

    /**
     * Update the specified user.
     * @urlParam id required The ID of the user. Example: 2
     * @bodyParam email                 email of the user.              Example: john@geoprofs.com | null
     * @bodyParam role_slug             slug of the role.               Example: medewerker | null
     * @bodyParam department_slug       slug of the department          Example: geoict | null
     * @bodyParam subdepartment_slug    slug of the subdepartment       Example: scanning | null
     * @bodyParam supervisor_id         user id of the supervisor       Example: 1 | null
     * @bodyParam blocked               true/false if user is blocked   Example: false | null
     * @bodyParam verified              true/false if user is verified  Example: true | null
     * @bodyParam first_name            first name of the user          Example: John | null
     * @bodyParam sure_name             sure name of the user           Example: Doe | null
     * @bodyParam bsn                   BSN of the user                 Example: 123456789 | null
     * @bodyParam date_of_service       Date of when the user started   Example: 2021-01-01 | null
     * @bodyParam sick_days             ammount of sick days used       Example: 0 | null
     * @bodyParam vac_days              ammount of vacation days used   Example: 0 | null
     * @bodyParam personal_days         ammount of personal days used   Example: 0 | null
     * @bodyParam max_vac_days          max ammount of leave days       Example: 30 | null
     */
    public function update(Request $request, $user_id) {

        $data = $request->only(...$this->request_fields);

        // Define validation rules
        $validator = Validator::make($data, $this->validator_fields);

        // Check if the validation fails
        if ($validator->fails()) {
            // Return a JSON response with validation errors
            return response()->json([
                'error' => "Validation error",
                'code' => 'validation_error',
                'errors' => $validator->errors(),
                'code' => 'validation_error',
            ], 422);
        }

        // Create the user with the validated data
        $user = User::find()->whereId($user_id)->first();

        $user->fill([
            'email' => $data['email'] ?? $user->email,

            'role_slug' => $data['role_slug'] ?? $user->role_slug,
            'department_slug' => $data['department_slug'] ?? $user->department_slug,
            'subdepartment_slug' => $data['subdepartment_slug'] ?? $user->subdepartment_slug,
            'supervisor_id' => $data['supervisor_id'] ?? $user->supervisor_id,

            'blocked' => $data['blocked'] ?? $user->blocked,
            'verified' => $data['verified'] ?? $user->verified,

            'first_name' => $data['first_name'] ?? $user->first_name,
            'sure_name' => $data['sure_name'] ?? $user->sure_name,
            'bsn' => $data['bsn'] ?? $user->bsn,
            'date_of_service' => $data['date_of_service'] ?? $user->date_of_service,

            'sick_days' => $data['sick_days'] ?? $user->sick_days,
            'vac_days' => $data['vac_days'] ?? $user->vac_days,
            'personal_days' => $data['personal_days'] ?? $user->personal_days,
            'max_vac_days' => $data['max_vac_days'] ?? $user->max_vac_days,
        ]);

        $user->save();

        // Return a success response
        return response()->json([
            'success' => true,
            'user' => $user,
        ]);
    }

    /**
     * Remove the specified user.
     * @urlParam id required The ID of the user. Example: 2
     */
    public function destroy(Request $request, $user_id) {

        if ($user_id == $request->user()->id) {
            return response()->json([
                'error' => "Self delete",
                'code' => 'self_delete',
                'message' => 'You cannot delete yourself!',
            ], 403);
        }

        $user = User::whereId($user_id)->first();

        if (!$user) {
            return response()->json([
                'error' => "User not found",
                'code' => 'user_not_found',
                'message' => 'User not found',
            ], 404);
        }

        $user->tokens()->delete();
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully!',
        ]);

    }
}
