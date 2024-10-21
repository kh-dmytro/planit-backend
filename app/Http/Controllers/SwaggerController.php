<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SwaggerController extends Controller
{
    /**
     * @OA\Info(
     *     version="1.0.0",
     *     title="My API",
     *     description="API Documentation for my Laravel application.",
     *     @OA\Contact(
     *         email="support@example.com"
     *     )
     * )
     */
    public function index()
    {
        return response()->json(['message' => 'Swagger setup complete']);
    }
}