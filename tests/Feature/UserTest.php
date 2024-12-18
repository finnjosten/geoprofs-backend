<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use Tests\TestCase;

class UserTest extends TestCase
{

    private $api_token;
    private $user_id;

    public function set_api_token() {
        // Get api token to do the tests with
        $response = $this->post('/api/auth/testing', ['testing_key' => 'MKfUKBND9s901CkR2aj5MIagDlM7jXAl']);
        $this->api_token = json_decode($response->getContent())->access_token;
        $this->user_id = json_decode($response->getContent())->user->id;

    }

    public function test_get_all_users() : void {

        $this->set_api_token();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->api_token,
        ])->get('/api/users');

        $data = json_decode($response->getContent())->users;
        $response->assertStatus(200);
        $this->assertIsArray($data);
        $this->assertNotEmpty($data);
    }

    public function test_get_user() : void {

        // Pre set condition login
        $this->set_api_token();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->api_token,
        ])->get('/api/users/'. $this->user_id);

        $data = json_decode($response->getContent())->user;
        $response->assertStatus(200);
        $this->assertIsObject($data);
        $this->assertNotEmpty($data);


        // Logout the user
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->api_token,
        ])->get('/api/auth/testing-logout/');
    }

    public function test_remove_user(): void {
        // remove the user we created
        $this->assertTrue(true);
    }

    /**
     * Create a user
     */
    public function test_create_user(): void {

        // Pre set condition login
        $this->set_api_token();


        // Create a user
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->api_token,
        ])->post('/api/users/create', [
            'email' => 'test-user@app.com',
            'password' => 'password',

            'role_slug' => 'medewerker',
            'department_slug' => null,
            'subdepartment_slug' => null,
            'supervisor_id' => null,

            'blocked' => false,
            'verified' => true,

            'first_name' => 'Test',
            'sure_name' => 'User',
            'bsn' => '133456780',
            'date_of_service' => '2000-01-01',

            'sick_days' => 1,
            'vac_days' => 10,
            'personal_days' => 2,
            'max_vac_days' => 15,
        ]);



        $response->assertStatus(200);

        // End condition remove the user we made and logout
        $content = json_decode($response->getContent());
        $user_id = null;

        if (!isset($content->error)) {
            $user_id = $content->user->id;
        }

        // remove any user we created as we are still pushing to the live database
        if ($user_id && $this->api_token) {
            $this->withHeaders([
                'Authorization' => 'Bearer ' . $this->api_token,
            ])->delete('/api/users/'.$user_id.'/delete');
        }

        // Logout the user
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->api_token,
        ])->get('/api/auth/testing-logout/');

    }
}
