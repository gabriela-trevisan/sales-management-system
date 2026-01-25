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
        Schema::create('proposals', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique(); // Número da proposta
            $table->unsignedBigInteger('opportunity_id')->nullable(); // Sem FK constraint - tabela ainda não existe
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->date('issue_date');
            $table->date('expiration_date');
            $table->text('notes')->nullable();
            $table->enum('status', ['draft', 'sent', 'approved', 'rejected', 'expired'])->default('draft');
            $table->decimal('subtotal', 12, 2);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('total', 12, 2);
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('opportunity_id'); // Índice para pesquisas
            $table->index(['customer_id', 'status']);
            $table->index('issue_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proposals');
    }
};
