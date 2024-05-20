<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Mail\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Firebase\JWT\JWT;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()
            ], 400);
        }

        $credentials = $validator->validated();

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $jwt = $this->generateJwtToken($user);

            return response()->json([
                'token' => $jwt
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid email or password',
        ], 401);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()
            ], 400);
        }

        $input = $validator->validated();
        $input['password'] = Hash::make($input['password']);
        $user = User::create($input);

        $jwt = $this->generateJwtToken($user);

        return response()->json([
            'token' => $jwt
        ], 200);
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = ::driver('google')->user();
            $findUser = User::where('email', $googleUser->email)->first();

            if ($findUser) {
                Auth::login($findUser);
                $jwt = $this->generateJwtToken($findUser);

                return response()->json([
                    'token' => $jwt,
                    'message' => 'Login successful',
                ], 200);
            }

            $newUser = User::create([
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'password' => Hash::make($googleUser->email),
            ]);

            Auth::login($newUser);
            $jwt = $this->generateJwtToken($newUser);

            return response()->json([
                'token' => $jwt,
                'message' => 'Registration and login successful',
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to authenticate with Google'], 500);
        }
    }

    private function generateJwtToken($user)
    {
        $payload = [
            'iss' => 'Laravel Application',
            'role' => $user->role,
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'iat' => now()->timestamp,
            'exp' => now()->timestamp + 3600
        ];
        return JWT::encode($payload, env('JWT_SECRET_KEY'), 'HS256');
    }
}

