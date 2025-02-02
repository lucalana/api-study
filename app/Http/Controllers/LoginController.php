<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $user = User::where('email', $request->get('email'))->first();
        if (!$user || !Hash::check($request->get('password'), $user->password)) {
            throw ValidationException::withMessages(['credential' => 'The credentials are incorrect.']);
        }

        return [
            'token' => $user->createToken($user->name . $user->created_at)->plainTextToken
        ];
    }
}
