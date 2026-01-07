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
        Schema::create('pipeline_stages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('order')->default(0); // Ordem de exibição
            $table->decimal('probability', 5, 2)->default(0); // % de fechamento esperado
            $table->string('color', 7)->default('#3b82f6'); // Cor hexadecimal
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pipeline_stages');
    }
};
