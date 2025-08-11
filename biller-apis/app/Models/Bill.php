<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'bill_number',
        'customer_id',
        'amount',
        'due_date',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
    ];

    // Default values
    protected $attributes = [
        'status' => 'pending', // pending, paid, overdue
    ];

    // Relationship with Customer
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}