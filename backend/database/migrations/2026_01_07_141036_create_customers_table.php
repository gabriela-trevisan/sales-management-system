<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('document', 18)->unique(); // CPF ou CNPJ
            $table->string('email')->unique();
            $table->string('phone', 20)->nullable();
            $table->foreignId('segment_id')->nullable()->constrained('customer_segments')->nullOnDelete();
            $table->json('rfm_score')->nullable(); // Recency, Frequency, Monetary
            $table->enum('status', ['active', 'inactive', 'prospect'])->default('prospect');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['status', 'assigned_to']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
