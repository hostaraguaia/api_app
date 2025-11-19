<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

/**
 * Base controller for API authentication
 * Provides reusable methods for login and registration
 */
abstract class BaseAuthController extends Controller
{
    /**
     * Get the model class for authentication
     */
    abstract protected function getModelClass(): string;

    /**
     * Get the token name for API authentication
     */
    abstract protected function getTokenName(): string;

    /**
     * Get additional validation rules for registration
     */
    protected function getAdditionalRegistrationRules(): array
    {
        return [];
    }

    /**
     * Get additional fields for user creation
     */
    protected function getAdditionalFields(Request $request): array
    {
        return [];
    }

    /**
     * Handle API login
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $modelClass = $this->getModelClass();
        $user = $modelClass::where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            $token = $user->createToken($this->getTokenName())->accessToken;
            return response()->json([
                'token' => $token,
                'user' => $user
            ], 200);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Handle API registration
     */
    public function register(Request $request)
    {
        $rules = array_merge([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:' . (new ($this->getModelClass()))->getTable(),
            'password' => 'required|min:6',
        ], $this->getAdditionalRegistrationRules());

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $modelClass = $this->getModelClass();
        $data = array_merge([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ], $this->getAdditionalFields($request));

        $user = $modelClass::create($data);
        $token = $user->createToken($this->getTokenName())->accessToken;

        return response()->json([
            'token' => $token,
            'user' => $user
        ], 200);
    }
}
