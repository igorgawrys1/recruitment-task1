<?php

namespace Tests\Feature;

use App\Models\Patient;
use App\Models\Order;
use App\Models\TestResult;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class PatientResultsTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_patient_can_get_results(): void
    {
        $patient = Patient::create([
            'name' => 'Test',
            'surname' => 'Patient',
            'sex' => 'male',
            'birth_date' => '1990-01-01',
            'password' => Hash::make('1990-01-01'),
        ]);

        $order = Order::create([
            'order_id' => 'TEST001',
            'patient_id' => $patient->id,
        ]);

        TestResult::create([
            'order_id' => $order->id,
            'name' => 'Test Result',
            'value' => '10',
            'reference' => '5-15',
        ]);

        $token = JWTAuth::fromUser($patient);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/results');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'patient' => ['id', 'name', 'surname', 'sex', 'birthDate'],
                'orders' => [
                    '*' => [
                        'orderId',
                        'results' => [
                            '*' => ['name', 'value', 'reference']
                        ]
                    ]
                ]
            ]);
    }

    public function test_unauthenticated_request_returns_401(): void
    {
        $response = $this->getJson('/api/results');

        $response->assertStatus(401);
    }
}
