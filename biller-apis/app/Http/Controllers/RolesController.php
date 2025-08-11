<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;

class RolesController extends Controller
{
    /**
     * Display a listing of all roles.
     */
    public function index()
    {
        $roles = Role::with('permissions.module')->get();
        return response()->json($roles, 200);
    }

    /**
     * Display the specified role.
     */
    public function show($id)
    {
        $role = Role::with('permissions.module')->find($id);
        
        if (!$role) {
            return response()->json(['message' => 'Role not found'], 404);
        }
        
        return response()->json($role, 200);
    }

    /**
     * Store a newly created role.
     */
    public function store(Request $request)
    {
        // Validation
        $validatedData = $request->validate([
            'name' => 'required|string|max:100|unique:roles,name',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'integer|exists:permissions,id',
        ]);

        // Create the role
        $role = Role::create([
            'name' => $validatedData['name'],
            'description' => $validatedData['description'] ?? null,
        ]);

        // Attach permissions if provided
        if (isset($validatedData['permissions'])) {
            $role->permissions()->attach($validatedData['permissions']);
        }

        // Load the permissions relationship
        $role->load('permissions.module');

        return response()->json([
            'message' => 'Role created successfully',
            'data' => $role
        ], 201);
    }

    /**
     * Update the specified role.
     */
    public function update(Request $request, $id)
    {
        $role = Role::find($id);
        
        if (!$role) {
            return response()->json(['message' => 'Role not found'], 404);
        }

        // Validation for partial updates
        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:100|unique:roles,name,' . $id,
            'description' => 'sometimes|nullable|string',
            'permissions' => 'sometimes|nullable|array',
            'permissions.*' => 'integer|exists:permissions,id',
        ]);

        // Update role basic info
        if (isset($validatedData['name'])) {
            $role->name = $validatedData['name'];
        }
        if (isset($validatedData['description'])) {
            $role->description = $validatedData['description'];
        }
        $role->save();

        // Update permissions if provided
        if (isset($validatedData['permissions'])) {
            $role->permissions()->sync($validatedData['permissions']);
        }

        // Load the permissions relationship
        $role->load('permissions.module');

        return response()->json([
            'message' => 'Role updated successfully',
            'data' => $role
        ], 200);
    }

    /**
     * Remove the specified role.
     */
    public function destroy($id)
    {
        $role = Role::find($id);
        
        if (!$role) {
            return response()->json(['message' => 'Role not found'], 404);
        }

        // Detach all permissions before deleting
        $role->permissions()->detach();
        
        $role->delete();

        return response()->json([
            'message' => 'Role deleted successfully'
        ], 200);
    }
}