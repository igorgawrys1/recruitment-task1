<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PatientResource;
use App\Http\Resources\OrderResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class PatientResultsController extends Controller
{

    public function index(): JsonResponse
    {
        $patient = Auth::guard('api')->user();
        
        $patient->load('orders.testResults');

        return response()->json([
            'patient' => new PatientResource($patient),
            'orders' => OrderResource::collection($patient->orders),
        ]);
    }
}
