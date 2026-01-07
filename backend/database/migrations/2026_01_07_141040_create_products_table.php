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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku')->unique();
            $table->text('description')->nullable();
            $table->foreignId('category_id')->nullable()->constrained('product_categories')->nullOnDelete();
            $table->decimal('base_price', 10, 2);
            $table->decimal('cost_price', 10, 2)->nullable();
            $table->enum('unit', ['unit', 'kg', 'liter', 'meter', 'hour', 'month'])->default('unit');
            $table->boolean('is_active')->default(true);
            $table->boolean('requires_approval')->default(false); // Produtos que precisam aprovação para venda
            $table->json('specifications')->nullable(); // Especificações técnicas
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['is_active', 'category_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
