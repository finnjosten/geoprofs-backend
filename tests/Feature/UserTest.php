    <?php

    namespace Tests\Feature;

    use Illuminate\Foundation\Testing\RefreshDatabase;
    use Illuminate\Foundation\Testing\WithFaker;
    use Tests\TestCase;

    class UserTest extends TestCase
    {
        /**
         * A basic feature test example.
         */
        public function test_create_user(): void {

            $response = $this->post("/api/users/create", [
                'email' => 'test@testtest.com',
                'password' => 'TestingPassword',

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

        }
    }
