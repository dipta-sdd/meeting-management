<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware('auth:api', ['except' => ['login']]);
    // }

    /**
     * Get a JWT token via given credentials.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed', // Ensure password confirmation
            'role' => 'required|string',
            'time_zone' => 'required|string',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
            'time_zone' => $request->time_zone,
        ]);

        $token = Auth::login($user);

        return response()->json([
            'status' => 'success',
            'message' => 'Registration successful',
            'token' => $token,
        ], 201);
    }

    public function login(Request $request)
    {
        // Validate the credentials (email and password)
        $credentials = $request->only('email', 'password');

        // Check if the credentials are valid and issue a token
        if ($token = auth()->attempt($credentials)) {
            // Return the JWT token and token type
            return response()->json([
                'access_token' => $token,
                'user' => auth()->user(),
                'token_type' => 'Bearer',
            ]);
        }

        // Return an error response if login fails
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function changePassword(Request $request)
    {
        // Validate the request data
        $request->validate([
            'current_password' => 'required', // Ensure the current password is provided
            'new_password' => 'required|string|min:6|confirmed', // Ensure confirmation matches
        ]);

        // Get the authenticated user
        $user = Auth::user();

        // Handle the case where no user is authenticated
        if (!$user) {
            return response()->json(['error' => 'Unauthorized. User not authenticated.'], 401);
        }

        // Check if the provided current password is correct
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['error' => 'Current password is incorrect.'], 400);
        }

        // Update the password and save the user
        $user->password = Hash::make($request->new_password);
        $user->save();

        // Return a success message
        return response()->json(['message' => 'Password changed successfully.'], 200);
    }

    /**
     * Get the authenticated User
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(Request $request)
    {
        return response()->json(auth()->user());
    }


    /**
     * Log the user out (Invalidate the token)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $this->guard()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken($this->guard()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->guard()->factory()->getTTL() * 60
        ]);
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    public function guard()
    {
        return Auth::guard();
    }
    public function getProfile(Request $request)
    {
        $userId = auth()->id(); // Get the logged-in user's ID
        $user = User::find($userId); // Fetch the user data

        return response()->json([
            'name' => $user->name,
            'email' => $user->email,
            'created_at' => $user->created_at->format('Y-m-d H:i:s'),
            'timezone' => $user->timezone,
        ]);
    }

    // Update user profile
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'timezone' => 'required|string',
        ]);

        $userId = auth()->id(); // Get the logged-in user's ID
        $user = User::find($userId); // Fetch the user data

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'timezone' => $request->timezone,
        ]);

        return response()->json(['message' => 'Profile updated successfully.']);
    }
}
