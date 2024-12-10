<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest_Demi extends TestCase
{
    /**
     * A basic feature test example.
     */
    private $api_token;
    private $user_id;

    public function set_api_token()
    {
        // Get api token to do the tests with
        $response = $this->post('/api/auth/testing', ['testing_key' => 'MKfUKBND9s901CkR2aj5MIagDlM7jXAl']);
        $this->api_token = json_decode($response->getContent())->access_token;
        $this->user_id = json_decode($response->getContent())->user->id;
    }



    public function test_create_user(): void
    {

        $this->set_api_token();


        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->api_token}",
        ])->post('/api/users/create', [
            'email' => 'test@user2.com',
            'password' => 'supersecretpassword',

            'role_slug' => 'medewerker',
            'department_slug' => null,
            'subdepartment_slug' => null,
            'supervisor_id' => null,

            'blocked' => false,
            'verified' => true,

            'first_name' => 'Test',
            'sure_name' => 'User',
            'bsn' => '24681013',
            'date_of_service' => '2000-01-01',

            'sick_days' => 1,
            'vac_days' => 10,
            'personal_days' => 2,
            'max_vac_days' => 15,
        ]);



        $response->assertStatus(200);

        $content = json_decode($response->getContent());
        $user_id = null;

        if (!isset($content->error)) {
            $user_id = $content->user->id;
        }


        if ($user_id && $this->api_token) {
            $response = $this->withHeaders([
                'Authorization' => "Bearer {$this->api_token}",
            ])->delete("/api/users/{$user_id}/delete");
        }


        $this->withHeaders([
            'Authorization' => "Bearer {$this->api_token}",
        ])->get('/api/auth/testing-logout/');
    }

    public function test_delete_user(): void
    {


        $this->set_api_token();


        //create a test user 
        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->api_token}",
        ])->post('/api/users/create', [
            'email' => 'test@delete.com',
            'password' => 'supersecretpassword',

            'role_slug' => 'medewerker',
            'department_slug' => null,
            'subdepartment_slug' => null,
            'supervisor_id' => null,

            'blocked' => false,
            'verified' => true,

            'first_name' => 'Test',
            'sure_name' => 'User',
            'bsn' => '55667788',
            'date_of_service' => '2000-01-01',

            'sick_days' => 1,
            'vac_days' => 10,
            'personal_days' => 2,
            'max_vac_days' => 15,
        ]);


        $content = json_decode($response->getContent());
        $user_id = null;

        if (!isset($content->error)) {
            $user_id = $content->user->id;
        }


        if ($user_id && $this->api_token) {
            $response = $this->withHeaders([
                'Authorization' => "Bearer {$this->api_token}",
            ])->delete("/api/users/{$user_id}/delete");
            $response->assertStatus(200);
        }

        // Logout the user
        $this->withHeaders([
            'Authorization' => "Bearer {$this->api_token}",
        ])->get('/api/auth/testing-logout/');
    }

    public function test_show_user(): void
    {
        $this->set_api_token();


        // Create a test user
        $response = $this->withHeaders([

            'Authorization' => "Bearer {$this->api_token}",
        ])->post('/api/users/create', [
            'email' => 'test@show.com',
            'password' => 'supersecretpassword',

            'role_slug' => 'medewerker',
            'department_slug' => null,
            'subdepartment_slug' => null,
            'supervisor_id' => null,

            'blocked' => false,
            'verified' => true,

            'first_name' => 'Test',
            'sure_name' => 'User',
            'bsn' => '11223344',
            'date_of_service' => '2000-01-01',

            'sick_days' => 1,
            'vac_days' => 10,
            'personal_days' => 2,
            'max_vac_days' => 15,
        ]);


        $content = json_decode($response->getContent());
        $user_id = null;
        // Checks if content is declared and not NULL

        if (!isset($content->error)) {
            $user_id = $content->user->id;
        }

        $response = $this->get("/api/users/{$user_id}/");

        $response->assertStatus(200);

        // Delete test user from database 
        if ($user_id && $this->api_token) {
            $response = $this->withHeaders([
                'Authorization' => "Bearer {$this->api_token}",
            ])->delete("/api/users/{$user_id}/delete");
        }

        // Logout User 
        $this->withHeaders([
            'Authorization' => "Bearer {$this->api_token}",
        ])->get('/api/auth/testing-logout/');
    }
}
