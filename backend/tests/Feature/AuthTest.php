<?php

namespace Tests\Feature;

use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_patient_can_login_with_valid_credentials(): void
    {
        $patient = Patient::create([
            'name' => 'Piotr',
            'surname' => 'Kowalski',
            'sex' => 'male',
            'birth_date' => '1983-04-12',
            'password' => Hash::make('1983-04-12'),
        ]);

        $response = $this->postJson('/api/login', [
            'login' => 'PiotrKowalski',
            'password' => '1983-04-12',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
                'token_type',
                'expires_in'
            ]);
    }

    public function test_patient_cannot_login_with_invalid_credentials(): void
    {
        Patient::create([
            'name' => 'Piotr',
            'surname' => 'Kowalski',
            'sex' => 'male',
            'birth_date' => '1983-04-12',
            'password' => Hash::make('1983-04-12'),
        ]);

        $response = $this->postJson('/api/login', [
            'login' => 'PiotrKowalski',
            'password' => '1990-01-01',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'error' => 'Invalid credentials'
            ]);
    }

    public function test_login_validation_fails_with_invalid_format(): void
    {
        $response = $this->postJson('/api/login', [
            'login' => 'invalid_login',
            'password' => 'not-a-date',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['login', 'password']);
    }
}
