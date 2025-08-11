<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UsersController extends Controller
{
    /**
     * Display a listing of all users.
     */
    public function index()
    {
        $users = User::with('role')->get();
        return response()->json($users, 200);
    }

    /**
     * Display the specified user.
     */
    public function show($id)
    {
        $user = User::with('role')->find($id);
        
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        
        return response()->json($user, 200);
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        // Validation
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'role_id' => 'required|integer|exists:roles,id',
            'status' => 'sometimes|boolean',
        ]);

        // Create the user
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'role_id' => $validatedData['role_id'],
            'status' => $validatedData['status'] ?? true, // Default to active
        ]);

        // Load the role relationship
        $user->load('role');

        return response()->json([
            'message' => 'User created successfully',
            'data' => $user
        ], 201);
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Validation for partial updates
        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => [
                'sometimes',
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'password' => 'sometimes|nullable|string|min:8',
            'role_id' => 'sometimes|required|integer|exists:roles,id',
            'status' => 'sometimes|boolean',
        ]);

        // Update user data
        if (isset($validatedData['name'])) {
            $user->name = $validatedData['name'];
        }
        if (isset($validatedData['email'])) {
            $user->email = $validatedData['email'];
        }
        if (isset($validatedData['password'])) {
            $user->password = Hash::make($validatedData['password']);
        }
        if (isset($validatedData['role_id'])) {
            $user->role_id = $validatedData['role_id'];
        }
        if (isset($validatedData['status'])) {
            $user->status = $validatedData['status'];
        }

        $user->save();

        // Load the role relationship
        $user->load('role');

        return response()->json([
            'message' => 'User updated successfully',
            'data' => $user
        ], 200);
    }

    /**
     * Toggle user status (activate/deactivate).
     */
    public function toggleStatus($id)
    {
        $user = User::find($id);
        
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->status = !$user->status;
        $user->save();

        $user->load('role');

        return response()->json([
            'message' => $user->status ? 'User activated successfully' : 'User deactivated successfully',
            'data' => $user
        ], 200);
    }

    /**
     * Remove the specified user.
     */
    public function destroy($id)
    {
        $user = User::find($id);
        
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully'
        ], 200);
    }
}