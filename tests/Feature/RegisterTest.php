<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a role for user_cust
        Role::create([
            'name' => config("constants.role.user_cust"),
        ]);
    }

    /** @test */
    public function it_registers_a_user_with_valid_name_data()
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'password' => 'Password@123',
            'password_confirmation' => 'Password@123',
            'phone' => '1234567890',
            'street_address' => '123 Main St',
            'district_address' => 'Central District',
            'city' => 'Anytown',
        ];

        $response = $this->postJson('/api/v1/auth/register', $data);

        $response->assertStatus(201)
                 ->assertJsonStructure(['success_messages', 'user']);

        $this->assertDatabaseHas('users', [
            'email' => 'johndoe@example.com',
        ]);
    }

    /** @test */
    // public function it_fails_to_register_a_user_with_invalid_data()
    // {
    //     $data = [
    //         'name' => 'J',
    //         'email' => 'luan@gmail.com',
    //         'password' => 'Admin@123',
    //         'password_confirmation' => 'Admin@123',
    //         'phone' => '0360735240',
    //         'street_address' => '123',
    //         'district_address' => 'Central',
    //         'city' => 'City',
    //     ];

    //     $response = $this->postJson('/api/v1/auth/register', $data);

    //     $response->assertStatus(400)
    //              ->assertJsonStructure(['name', 'email', 'password', 'phone', 'street_address', 'district_address', 'city']);
    // }

    /** @test */
    public function it_fails_to_register_a_user_with_existing_email()
    {
        User::create([
            'name' => 'Existing User',
            'email' => 'hiexisting@example.com',
            'password' => Hash::make('Password@123'),
        ]);

        $data = [
            'name' => 'John Doe',
            'email' => 'hiexisting@example.com',
            'password' => 'Password@123',
            'password_confirmation' => 'Password@123',
        ];

        $response = $this->postJson('/api/v1/auth/register', $data);

        $response->assertStatus(400)
                 ->assertJsonStructure(['email']);
    }
}
