<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Module;

class PermissionsController extends Controller
{
    /**
     * Display a listing of all permissions.
     */
    public function index()
    {
        $permissions = Permission::with('module')->get();
        return response()->json($permissions, 200);
    }

    /**
     * Display the specified permission.
     */
    public function show($id)
    {
        $permission = Permission::with('module')->find($id);
        
        if (!$permission) {
            return response()->json(['message' => 'Permission not found'], 404);
        }
        
        return response()->json($permission, 200);
    }

    /**
     * Store a newly created permission.
     */
    public function store(Request $request)
    {
        // Validation
        $validatedData = $request->validate([
            'display_name' => 'required|string|max:100',
            'permission_key' => 'required|string|max:100|unique:permissions,permission_key',
            'description' => 'nullable|string',
            'module_id' => 'required|integer|exists:modules,id',
        ]);

        // Create the permission
        $permission = Permission::create($validatedData);

        // Load the module relationship
        $permission->load('module');

        return response()->json([
            'message' => 'Permission created successfully',
            'data' => $permission
        ], 201);
    }

    /**
     * Update the specified permission.
     */
    public function update(Request $request, $id)
    {
        $permission = Permission::find($id);
        
        if (!$permission) {
            return response()->json(['message' => 'Permission not found'], 404);
        }

        // Validation for partial updates
        $validatedData = $request->validate([
            'display_name' => 'sometimes|required|string|max:100',
            'permission_key' => 'sometimes|required|string|max:100|unique:permissions,permission_key,' . $id,
            'description' => 'sometimes|nullable|string',
            'module_id' => 'sometimes|required|integer|exists:modules,id',
        ]);

        // Update the permission
        $permission->update($validatedData);

        // Load the module relationship
        $permission->load('module');

        return response()->json([
            'message' => 'Permission updated successfully',
            'data' => $permission
        ], 200);
    }

      public function destroy($id)
    {
        $permission = Permission::find($id);
        
        if (!$permission) {
            return response()->json(['message' => 'Permission not found'], 404);
        }

        $permission->delete();

        return response()->json([
            'message' => 'Permission deleted successfully'
        ], 200);
    }
}