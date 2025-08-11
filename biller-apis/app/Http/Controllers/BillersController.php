<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Biller;
use Illuminate\Support\Facades\Storage;

class BillersController extends Controller
{
    /**
     * Display a listing of all billers.
     */
    public function index()
    {
        $billers = Biller::all();
        return response()->json($billers, 200);
    }

    /**
     * Display the specified biller.
     */
    public function show($id)
    {
        $biller = Biller::find($id);
        
        if (!$biller) {
            return response()->json(['message' => 'Biller not found'], 404);
        }
        
        return response()->json($biller, 200);
    }

    /**
     * Store a newly created biller (Onboard New Biller).
     */
    public function store(Request $request)
    {
        // Validation
        $validatedData = $request->validate([
            // Business Details
            'business_name' => 'required|string|max:255',
            'trn' => 'required|string|max:50|unique:billers,trn',
            'business_address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'parish' => 'required|string|max:100',
            
            // Primary Contact
            'contact_full_name' => 'required|string|max:255',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'required|string|max:20',
            'contact_job_title' => 'required|string|max:100',
            
            // Banking Information
            'bank_name' => 'required|string|max:255',
            'bank_branch' => 'required|string|max:255',
            'account_holder_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:50',
            
            // Documents (optional during onboarding)
            'documents' => 'nullable|array',
            'documents.*' => 'file|mimes:pdf,jpg,jpeg,png|max:2048', // 2MB max per file
        ]);

        // Handle document uploads
        $documentPaths = [];
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $path = $file->store('biller-documents', 'public');
                $documentPaths[] = $path;
            }
        }

        // Create the biller
        $biller = Biller::create([
            // Business Details
            'business_name' => $validatedData['business_name'],
            'trn' => $validatedData['trn'],
            'business_address' => $validatedData['business_address'],
            'city' => $validatedData['city'],
            'parish' => $validatedData['parish'],
            
            // Primary Contact
            'contact_full_name' => $validatedData['contact_full_name'],
            'contact_email' => $validatedData['contact_email'],
            'contact_phone' => $validatedData['contact_phone'],
            'contact_job_title' => $validatedData['contact_job_title'],
            
            // Banking Information
            'bank_name' => $validatedData['bank_name'],
            'bank_branch' => $validatedData['bank_branch'],
            'account_holder_name' => $validatedData['account_holder_name'],
            'account_number' => $validatedData['account_number'],
            
            // Documents and status
            'documents' => $documentPaths,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Biller onboarded successfully',
            'data' => $biller
        ], 201);
    }

    /**
     * Update the specified biller.
     */
    public function update(Request $request, $id)
    {
        $biller = Biller::find($id);
        
        if (!$biller) {
            return response()->json(['message' => 'Biller not found'], 404);
        }

        // Validation for partial updates
        $validatedData = $request->validate([
            // Business Details
            'business_name' => 'sometimes|required|string|max:255',
            'trn' => 'sometimes|required|string|max:50|unique:billers,trn,' . $id,
            'business_address' => 'sometimes|required|string|max:500',
            'city' => 'sometimes|required|string|max:100',
            'parish' => 'sometimes|required|string|max:100',
            
            // Primary Contact
            'contact_full_name' => 'sometimes|required|string|max:255',
            'contact_email' => 'sometimes|required|email|max:255',
            'contact_phone' => 'sometimes|required|string|max:20',
            'contact_job_title' => 'sometimes|required|string|max:100',
            
            // Banking Information
            'bank_name' => 'sometimes|required|string|max:255',
            'bank_branch' => 'sometimes|required|string|max:255',
            'account_holder_name' => 'sometimes|required|string|max:255',
            'account_number' => 'sometimes|required|string|max:50',
            
            // Status
            'status' => 'sometimes|required|in:pending,approved,rejected',
            
            // Documents
            'documents' => 'sometimes|nullable|array',
            'documents.*' => 'file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        // Handle document uploads if new documents are provided
        if ($request->hasFile('documents')) {
            $documentPaths = [];
            foreach ($request->file('documents') as $file) {
                $path = $file->store('biller-documents', 'public');
                $documentPaths[] = $path;
            }
            $validatedData['documents'] = $documentPaths;
        }

        // Update the biller
        $biller->update($validatedData);

        return response()->json([
            'message' => 'Biller updated successfully',
            'data' => $biller
        ], 200);
    }

    /**
     * Update biller status (approve/reject).
     */
    public function updateStatus(Request $request, $id)
    {
        $biller = Biller::find($id);
        
        if (!$biller) {
            return response()->json(['message' => 'Biller not found'], 404);
        }

        $validatedData = $request->validate([
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $biller->status = $validatedData['status'];
        $biller->save();

        return response()->json([
            'message' => 'Biller status updated successfully',
            'data' => $biller
        ], 200);
    }

    /**
     * Remove the specified biller.
     */
    public function destroy($id)
    {
        $biller = Biller::find($id);
        
        if (!$biller) {
            return response()->json(['message' => 'Biller not found'], 404);
        }

        // Delete associated documents
        if ($biller->documents) {
            foreach ($biller->documents as $documentPath) {
                Storage::disk('public')->delete($documentPath);
            }
        }

        $biller->delete();

        return response()->json([
            'message' => 'Biller deleted successfully'
        ], 200);
    }
}