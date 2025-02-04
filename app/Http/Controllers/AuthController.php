<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'min:4', 'max:30'],
        ]);
        $user = User::where('email', $validated['email'])->first();
        if (!$user || !Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages(['The credentials are incorrect.']);
        }

        return response()->json([
            'token' => $user->createToken($user->name . $user->created_at)->plainTextToken
        ], 200);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'min:4', 'max:30'],
        ]);

        $user = User::create(
            $validated
        );

        return response()->json([
            'user' => $user,
            'token' => $user->createToken($user->name . $user->created_at)->plainTextToken
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([], 204);
    }
}
