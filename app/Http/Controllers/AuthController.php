<?php

namespace App\Http\Controllers;

use App\Data\Requests\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Spatie\LaravelData\Optional;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        $remember = ! $request->remember instanceof Optional ? $request->remember : false;
        if (! Auth::attempt([
            'name' => $request->name,
            'password' => $request->password,
        ], $remember)
        ) {

            throw ValidationException::withMessages([
                'name' => trans('auth.failed'),
            ]);
        }

        session()->regenerate();

        return response()->json([]);
    }

    public function logout(): JsonResponse
    {
        Auth::guard('web')->logout();

        session()->invalidate();

        session()->regenerateToken();

        return response()->json([
            'message' => 'Successfully logged out',
        ]);
    }
}
