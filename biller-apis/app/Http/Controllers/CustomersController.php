<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Customer;

class CustomersController extends Controller
{
    /**
     * Display a listing of all customers.
     */
    public function index()
    {
        $customers = Customer::all();
        return response()->json($customers, 200);
    }

    /**
     * Display the specified customer.
     */
    public function show($id)
    {
        $customer = Customer::find($id);
        
        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }
        
        return response()->json($customer, 200);
    }

    /**
     * Store a newly created customer.
     */
    public function store(Request $request)
    {
        // Validation
        $validatedData = $request->validate([
            'full_name' => 'required|string|max:255',
            'account_id' => 'required|string|max:100|unique:customers,account_id',
            'email' => 'required|email|max:255|unique:customers,email',
            'phone' => 'nullable|string|max:20',
        ]);

        // Create the customer
        $customer = Customer::create($validatedData);

        return response()->json([
            'message' => 'Customer created successfully',
            'data' => $customer
        ], 201);
    }

    /**
     * Update the specified customer.
     */
    public function update(Request $request, $id)
    {
        $customer = Customer::find($id);
        
        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        // Validation for partial updates
        $validatedData = $request->validate([
            'full_name' => 'sometimes|required|string|max:255',
            'account_id' => 'sometimes|required|string|max:100|unique:customers,account_id,' . $id,
            'email' => 'sometimes|required|email|max:255|unique:customers,email,' . $id,
            'phone' => 'sometimes|nullable|string|max:20',
        ]);

        // Update the customer
        $customer->update($validatedData);

        return response()->json([
            'message' => 'Customer updated successfully',
            'data' => $customer
        ], 200);
    }

    /**
     * Remove the specified customer.
     */
    public function destroy($id)
    {
        $customer = Customer::find($id);
        
        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        $customer->delete();

        return response()->json([
            'message' => 'Customer deleted successfully'
        ], 200);
    }

    /**
     * Bulk import customers from CSV file.
     * TODO: Implement CSV file processing logic
     */
    public function bulkImport(Request $request)
    {
        // Validation
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:5120', // 5MB max
        ]);

        // TODO: Process CSV file and import customers
        // For now, just return a placeholder response
        
        return response()->json([
            'message' => 'CSV bulk import endpoint ready (implementation pending)',
            'file_name' => $request->file('csv_file')->getClientOriginalName(),
            'file_size' => $request->file('csv_file')->getSize()
        ], 200);
    }
}