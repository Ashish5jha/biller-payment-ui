<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Biller extends Model
{
    use HasFactory;
    
    protected $fillable = [
        // Business Details
        'business_name',
        'trn',
        'business_address',
        'city',
        'parish',
        
        // Primary Contact
        'contact_full_name',
        'contact_email',
        'contact_phone',
        'contact_job_title',
        
        // Banking Information
        'bank_name',
        'bank_branch',
        'account_holder_name',
        'account_number',
        
        // Status and metadata
        'status',
        'documents',
    ];

    protected $casts = [
        'documents' => 'array', // Store multiple document paths as JSON
        'status' => 'string',
    ];

    // Default values
    protected $attributes = [
        'status' => 'pending', // pending, approved, rejected
    ];
}