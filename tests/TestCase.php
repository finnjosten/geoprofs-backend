<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public $api_token;
    public $user_id;

    public function set_api_token() {
        // Get api token to do the tests with
        $response = $this->post('/api/auth/testing', ['testing_key' => 'MKfUKBND9s901CkR2aj5MIagDlM7jXAl']);
        $this->api_token = json_decode($response->getContent())->access_token;
        $this->user_id = json_decode($response->getContent())->user->id;
    }

    public function end_session() {
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->api_token,
        ])->get('/api/auth/testing-logout/');
    }
}
