<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Adiciona índices de performance para otimizar queries comuns:
     * - Filtros em customers (status, assigned_to)
     * - Buscas por documento e email
     * - Pipeline de oportunidades
     * - Relacionamentos (customer_id)
     */
    public function up(): void
    {
        // Customers table - Índices para filtros e buscas
        Schema::table('customers', function (Blueprint $table) {
            // Índice composto para filtros comuns (status + assigned_to + ordenação)
            $table->index(['status', 'assigned_to', 'created_at'], 'idx_customers_filters');
            
            // Índice para busca por documento (CPF/CNPJ)
            $table->index('document', 'idx_customers_document');
            
            // Índice para ordenação por data de criação
            $table->index('created_at', 'idx_customers_created_at');
            
            // Índice para busca por email
            $table->index('email', 'idx_customers_email');
        });

        // Opportunities table - Índices para pipeline e relatórios
        Schema::table('opportunities', function (Blueprint $table) {
            // Índice composto para pipeline (pipeline_stage_id + assigned_to + ordenação)
            $table->index(['pipeline_stage_id', 'assigned_to', 'created_at'], 'idx_opportunities_pipeline');
            
            // Índice para cálculos de valor
            $table->index('value', 'idx_opportunities_value');
            
            // Índice para ordenação por data de fechamento esperado
            $table->index('expected_close_date', 'idx_opportunities_expected_close');
            
            // Nota: customer_id, assigned_to e status já têm índices na migration original
        });

        // Customer addresses - Índice para relacionamento
        Schema::table('customer_addresses', function (Blueprint $table) {
            // Nota: customer_id já tem índice por ser FK
            $table->index('zip_code', 'idx_addresses_zipcode');
        });

        // Customer contacts - Índice para relacionamento
        Schema::table('customer_contacts', function (Blueprint $table) {
            // Nota: customer_id já tem índice por ser FK
            $table->index('email', 'idx_contacts_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Customers table
        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex('idx_customers_filters');
            $table->dropIndex('idx_customers_document');
            $table->dropIndex('idx_customers_created_at');
            $table->dropIndex('idx_customers_email');
        });

        // Opportunities table
        Schema::table('opportunities', function (Blueprint $table) {
            $table->dropIndex('idx_opportunities_pipeline');
            $table->dropIndex('idx_opportunities_value');
            $table->dropIndex('idx_opportunities_expected_close');
        });

        // Customer addresses
        Schema::table('customer_addresses', function (Blueprint $table) {
            $table->dropIndex('idx_addresses_zipcode');
        });

        // Customer contacts
        Schema::table('customer_contacts', function (Blueprint $table) {
            $table->dropIndex('idx_contacts_email');
        });
    }
};
