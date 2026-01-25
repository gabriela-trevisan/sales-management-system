<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

/**
 * Proposal Schema for OpenAPI/Swagger documentation.
 */
#[OA\Schema(
    schema: 'Proposal',
    title: 'Proposal',
    description: 'Proposta comercial',
    required: ['id', 'number', 'customer_id', 'issue_date', 'expiration_date', 'status', 'subtotal', 'discount', 'total', 'created_by'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'number', type: 'string', example: 'PROP-2026-0001'),
        new OA\Property(property: 'customer_id', type: 'integer', example: 1),
        new OA\Property(
            property: 'customer',
            type: 'object',
            properties: [
                new OA\Property(property: 'id', type: 'integer', example: 1),
                new OA\Property(property: 'name', type: 'string', example: 'Empresa XYZ Ltda'),
                new OA\Property(property: 'document', type: 'string', example: '12345678000190'),
                new OA\Property(property: 'email', type: 'string', example: 'contato@empresa.com')
            ]
        ),
        new OA\Property(property: 'opportunity_id', type: 'integer', nullable: true, example: null),
        new OA\Property(property: 'issue_date', type: 'string', format: 'date', example: '2026-01-24'),
        new OA\Property(property: 'expiration_date', type: 'string', format: 'date', example: '2026-02-24'),
        new OA\Property(property: 'notes', type: 'string', nullable: true, example: 'Proposta para desenvolvimento de sistema customizado'),
        new OA\Property(property: 'status', type: 'string', enum: ['draft', 'sent', 'approved', 'rejected', 'expired'], example: 'draft'),
        new OA\Property(property: 'subtotal', type: 'number', format: 'float', example: 10000.00),
        new OA\Property(property: 'discount', type: 'number', format: 'float', example: 1000.00),
        new OA\Property(property: 'total', type: 'number', format: 'float', example: 9000.00),
        new OA\Property(property: 'created_by', type: 'integer', example: 1),
        new OA\Property(
            property: 'creator',
            type: 'object',
            properties: [
                new OA\Property(property: 'id', type: 'integer', example: 1),
                new OA\Property(property: 'name', type: 'string', example: 'Admin User'),
                new OA\Property(property: 'email', type: 'string', example: 'admin@salesmanagement.com')
            ]
        ),
        new OA\Property(
            property: 'items',
            type: 'array',
            items: new OA\Items(
                type: 'object',
                properties: [
                    new OA\Property(property: 'id', type: 'integer', example: 1),
                    new OA\Property(property: 'proposal_id', type: 'integer', example: 1),
                    new OA\Property(property: 'product_id', type: 'integer', example: 1),
                    new OA\Property(
                        property: 'product',
                        type: 'object',
                        properties: [
                            new OA\Property(property: 'id', type: 'integer', example: 1),
                            new OA\Property(property: 'name', type: 'string', example: 'Desenvolvedor Sênior'),
                            new OA\Property(property: 'sku', type: 'string', example: 'DEV-SENIOR')
                        ]
                    ),
                    new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Customização do módulo de vendas'),
                    new OA\Property(property: 'quantity', type: 'integer', example: 40),
                    new OA\Property(property: 'unit_price', type: 'number', format: 'float', example: 250.00),
                    new OA\Property(property: 'discount_percentage', type: 'number', format: 'float', example: 10.00),
                    new OA\Property(property: 'total', type: 'number', format: 'float', example: 9000.00)
                ]
            )
        ),
        new OA\Property(property: 'is_expired', type: 'boolean', example: false),
        new OA\Property(property: 'can_be_edited', type: 'boolean', example: true),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2026-01-24T10:30:00.000000Z'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2026-01-24T10:30:00.000000Z'),
        new OA\Property(property: 'deleted_at', type: 'string', format: 'date-time', nullable: true, example: null)
    ],
    type: 'object'
)]
class ProposalSchema
{
}
