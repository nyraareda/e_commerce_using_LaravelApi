<?php

namespace App\Http\Controllers\Api;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\UserRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

use App\Models\User;

class AuthController extends Controller
{
    //
        /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request) {
        
    
        $credentials = $request->only('username', 'password');
    
        if (! $token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    
        // Retrieve the authenticated user
        $user = auth()->user();
    
        // Additional data to be sent with the token
        $additionalData = [
            'id' => $user->id,
            'email' => $user->email,
            'role'=>$user->role,
            // Add more data as needed
        ];
    
        // Generate a token with additional data
        $tokenWithAdditionalData = JWTAuth::claims($additionalData)->attempt($credentials);
    
        return response()->json(['token' => $tokenWithAdditionalData,'user'=>$additionalData], 200);
    }

 /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(UserRequest $request) {
        $user = User::create(array_merge(
            $request->validated(),
            ['password' => bcrypt($request->password)],
        ));

        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user
        ], 201);
    }
    

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout() {
        auth()->logout();
        return response()->json(['message' => 'User successfully signed out']);
    }
    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh() {
        return $this->createNewToken(auth()->refresh());
    }
    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile() {
        return response()->json(auth()->user());
    }
    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token){
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }


    public function updateProfile(Request $request) {

        $validator = Validator::make($request->all(), [
            'username' => 'string|between:2,100',
        ]);
    
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
    
    
        $user = auth()->user();
    
        if ($request->has('username')) {
            $user->username = $request->username;
        }
    
        $user->save();
    
        // Return a JSON response for successful profile update
        return response()->json(['message' => 'Profile updated successfully', 'user' => $user]);
    }
    
    
}