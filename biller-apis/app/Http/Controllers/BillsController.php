<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Customer;

class BillsController extends Controller
{
    /**
     * Display a listing of all bills.
     */
    public function index()
    {
        $bills = Bill::with('customer')->get();
        return response()->json($bills, 200);
    }

    /**
     * Display the specified bill.
     */
    public function show($id)
    {
        $bill = Bill::with('customer')->find($id);
        
        if (!$bill) {
            return response()->json(['message' => 'Bill not found'], 404);
        }
        
        return response()->json($bill, 200);
    }

    /**
     * Assign a new bill to a customer.
     */
    public function assignBill(Request $request)
    {
        // Validation
        $validatedData = $request->validate([
            'customer_id' => 'required|integer|exists:customers,id',
            'bill_number' => 'required|string|max:100|unique:bills,bill_number',
            'amount' => 'required|numeric|min:0.01',
            'due_date' => 'required|date|after_or_equal:today',
        ]);

        // Create the bill
        $bill = Bill::create($validatedData);

        // Load the customer relationship
        $bill->load('customer');

        return response()->json([
            'message' => 'Bill assigned successfully',
            'data' => $bill
        ], 201);
    }

    /**
     * Update the specified bill.
     */
    public function update(Request $request, $id)
    {
        $bill = Bill::find($id);
        
        if (!$bill) {
            return response()->json(['message' => 'Bill not found'], 404);
        }

        // Validation for partial updates
        $validatedData = $request->validate([
            'customer_id' => 'sometimes|required|integer|exists:customers,id',
            'bill_number' => 'sometimes|required|string|max:100|unique:bills,bill_number,' . $id,
            'amount' => 'sometimes|required|numeric|min:0.01',
            'due_date' => 'sometimes|required|date|after_or_equal:today',
            'status' => 'sometimes|required|in:pending,paid,overdue',
        ]);

        // Update the bill
        $bill->update($validatedData);

        // Load the customer relationship
        $bill->load('customer');

        return response()->json([
            'message' => 'Bill updated successfully',
            'data' => $bill
        ], 200);
    }

    /**
     * Remove the specified bill.
     */
    public function destroy($id)
    {
        $bill = Bill::find($id);
        
        if (!$bill) {
            return response()->json(['message' => 'Bill not found'], 404);
        }

        $bill->delete();

        return response()->json([
            'message' => 'Bill deleted successfully'
        ], 200);
    }

    /**
     * Get bills for a specific customer.
     */
    public function getCustomerBills($customerId)
    {
        $customer = Customer::find($customerId);
        
        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        $bills = Bill::where('customer_id', $customerId)->get();

        return response()->json([
            'customer' => $customer,
            'bills' => $bills
        ], 200);
    }

    /**
     * Update bill status (mark as paid, overdue, etc.).
     */
    public function updateStatus(Request $request, $id)
    {
        $bill = Bill::find($id);
        
        if (!$bill) {
            return response()->json(['message' => 'Bill not found'], 404);
        }

        $validatedData = $request->validate([
            'status' => 'required|in:pending,paid,overdue',
        ]);

        $bill->status = $validatedData['status'];
        $bill->save();

        $bill->load('customer');

        return response()->json([
            'message' => 'Bill status updated successfully',
            'data' => $bill
        ], 200);
    }
}