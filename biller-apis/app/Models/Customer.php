<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'full_name',
        'account_id',
        'email',
        'phone',
    ];

    // Relationship with Bills
    public function bills()
    {
        return $this->hasMany(Bill::class);
    }
}