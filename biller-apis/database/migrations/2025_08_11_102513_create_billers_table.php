<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('billers', function (Blueprint $table) {
            $table->id();
            
            // Business Details
            $table->string('business_name');
            $table->string('trn', 50)->unique(); // Tax Registration Number
            $table->text('business_address');
            $table->string('city', 100);
            $table->string('parish', 100);
            
            // Primary Contact
            $table->string('contact_full_name');
            $table->string('contact_email');
            $table->string('contact_phone', 20);
            $table->string('contact_job_title', 100);
            
            // Banking Information
            $table->string('bank_name');
            $table->string('bank_branch');
            $table->string('account_holder_name');
            $table->string('account_number', 50);
            
            // Status and documents
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->json('documents')->nullable(); // Store document file paths
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('billers');
    }
};