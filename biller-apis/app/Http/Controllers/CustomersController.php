<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Bill;

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

    $file = $request->file('csv_file');
    $path = $file->getRealPath();
    
    // Initialize counters
    $successCount = 0;
    $errorCount = 0;
    $errors = [];
    $importedCustomers = [];

    try {
        // Open and read CSV file
        if (($handle = fopen($path, 'r')) !== FALSE) {
            $header = fgetcsv($handle); // Read header row
            
            // Expected CSV headers (case insensitive): FullName,AccountID,Email,Phone
            $expectedHeaders = ['fullname', 'accountid', 'email', 'phone'];
            $normalizedHeaders = array_map('strtolower', $header);
            
            // Validate CSV headers
            if (!$header || $normalizedHeaders !== $expectedHeaders) {
                return response()->json([
                    'message' => 'Invalid CSV format',
                    'error' => 'CSV must have headers: FullName,AccountID,Email,Phone',
                    'found_headers' => $header,
                    'expected_headers' => ['FullName', 'AccountID', 'Email', 'Phone']
                ], 400);
            }

            $rowNumber = 1; // Start from 1 (excluding header)
            
            while (($data = fgetcsv($handle)) !== FALSE) {
                $rowNumber++;
                
                // Skip empty rows
                if (empty(array_filter($data))) {
                    continue;
                }
                
                // Map CSV data to database fields
                $customerData = [
                    'full_name' => trim($data[0] ?? ''),      // FullName -> full_name
                    'account_id' => trim($data[1] ?? ''),     // AccountID -> account_id
                    'email' => trim($data[2] ?? ''),          // Email -> email
                    'phone' => trim($data[3] ?? '') ?: null,  // Phone -> phone
                ];

                try {
                    // Validate each row
                    $validator = \Validator::make($customerData, [
                        'full_name' => 'required|string|max:255',
                        'account_id' => 'required|string|max:100|unique:customers,account_id',
                        'email' => 'required|email|max:255|unique:customers,email',
                        'phone' => 'nullable|string|max:20',
                    ]);

                    if ($validator->fails()) {
                        $errorCount++;
                        $errors[] = [
                            'row' => $rowNumber,
                            'data' => [
                                'FullName' => $data[0] ?? '',
                                'AccountID' => $data[1] ?? '',
                                'Email' => $data[2] ?? '',
                                'Phone' => $data[3] ?? '',
                            ],
                            'errors' => $validator->errors()->all()
                        ];
                        continue;
                    }

                    // Create customer if validation passes
                    $customer = Customer::create($customerData);
                    $successCount++;
                    $importedCustomers[] = $customer;

                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = [
                        'row' => $rowNumber,
                        'data' => [
                            'FullName' => $data[0] ?? '',
                            'AccountID' => $data[1] ?? '',
                            'Email' => $data[2] ?? '',
                            'Phone' => $data[3] ?? '',
                        ],
                        'errors' => ['Database error: ' . $e->getMessage()]
                    ];
                }
            }
            
            fclose($handle);
        } else {
            return response()->json([
                'message' => 'Unable to read CSV file'
            ], 400);
        }

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Error processing CSV file',
            'error' => $e->getMessage()
        ], 500);
    }

    // Return detailed results
    return response()->json([
        'message' => 'CSV bulk import completed',
        'summary' => [
            'total_processed' => $successCount + $errorCount,
            'successful_imports' => $successCount,
            'failed_imports' => $errorCount,
        ],
        'imported_customers' => $importedCustomers,
        'errors' => $errors
    ], 200);
}

public function assignBill(Request $request, $id)
{
    $customer = Customer::find($id);
    
    if (!$customer) {
        return response()->json(['message' => 'Customer not found'], 404);
    }

    // Validation
    $validatedData = $request->validate([
        'bill_number' => 'required|string|max:100|unique:bills,bill_number',
        'amount' => 'required|numeric|min:0.01',
        'due_date' => 'required|date|after_or_equal:today',
    ]);

    // Create the bill
    $bill = \App\Models\Bill::create([
        'customer_id' => $id,
        'bill_number' => $validatedData['bill_number'],
        'amount' => $validatedData['amount'],
        'due_date' => $validatedData['due_date'],
        'status' => 'pending',
    ]);

    // Load the customer relationship
    $bill->load('customer');

    return response()->json([
        'message' => "Bill assigned to {$customer->full_name} successfully",
        'data' => $bill
    ], 201);
}

}
