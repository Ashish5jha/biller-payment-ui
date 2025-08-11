<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Module;
use Illuminate\Support\Facades\Gate;

class ModulesController extends Controller
{
    /**
     * Display a listing of all modules.
     */
    public function index()
    {
        $modules = Module::all();
        return response()->json($modules, 200);
    }

    /**
     * Display the specified module.
     */
    public function show($id)
    {
        $module = Module::find($id);
        
        if (!$module) {
            return response()->json(['message' => 'Module not found'], 404);
        }
        
        return response()->json($module, 200);
    }

    /**
     * Store a newly created module.
     */
    public function store(Request $request)
    {
        // --- Step 2: Data Safety - Validation ---
        $validatedData = $request->validate([
            'name' => 'required|string|max:100|unique:modules,name',
            'description' => 'nullable|string',
            'sort_order' => 'required|integer',
        ]);

        // --- Step 3: Creating the Record ---
        $module = Module::create($validatedData);

        // --- Step 4: The Successful Response ---
        return response()->json([
            'message' => 'Module created successfully',
            'data' => $module
        ], 201);
    }

    /**
     * Update the specified module.
     */
    public function update(Request $request, $id)
    {
        $module = Module::find($id);
        
        if (!$module) {
            return response()->json(['message' => 'Module not found'], 404);
        }

        // Validation (excluding current module from unique check)
        $validatedData = $request->validate([
            'name' => 'required|string|max:100|unique:modules,name,' . $id,
            'description' => 'nullable|string',
            'sort_order' => 'required|integer',
        ]);

        // Update the module
        $module->update($validatedData);

        return response()->json([
            'message' => 'Module updated successfully',
            'data' => $module
        ], 200);
    }

    /**
     * Remove the specified module.
     */
    public function destroy($id)
    {
        $module = Module::find($id);
        
        if (!$module) {
            return response()->json(['message' => 'Module not found'], 404);
        }

        $module->delete();

        return response()->json([
            'message' => 'Module deleted successfully'
        ], 200);
    }
}