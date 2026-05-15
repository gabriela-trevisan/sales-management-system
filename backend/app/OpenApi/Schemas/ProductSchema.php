<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Product",
    title: "Product",
    description: "Modelo de Produto",
    type: "object",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "name", type: "string", example: "Hora de Desenvolvimento Senior"),
        new OA\Property(property: "sku", type: "string", example: "DEV-SENIOR-HOUR"),
        new OA\Property(property: "description", type: "string", nullable: true, example: "Hora de desenvolvimento com profissional sênior"),
        new OA\Property(
            property: "category",
            properties: [
                new OA\Property(property: "id", type: "integer", example: 1),
                new OA\Property(property: "name", type: "string", example: "Desenvolvimento")
            ],
            type: "object",
            nullable: true
        ),
        new OA\Property(property: "base_price", type: "number", format: "float", example: 250.00),
        new OA\Property(property: "cost_price", type: "number", format: "float", nullable: true, example: 150.00),
        new OA\Property(
            property: "unit",
            type: "string",
            enum: ["unit", "kg", "liter", "meter", "hour", "month"],
            example: "hour"
        ),
        new OA\Property(property: "is_active", type: "boolean", example: true),
        new OA\Property(property: "requires_approval", type: "boolean", example: false),
        new OA\Property(
            property: "specifications",
            type: "object",
            nullable: true,
            example: ["skill_level" => "senior", "technologies" => ["PHP", "Laravel", "React"]]
        ),
        new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-01-23T10:00:00.000000Z"),
        new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2026-01-23T10:00:00.000000Z")
    ]
)]
class ProductSchema
{
    // este arquivo só existe para definir o schema OpenAPI
}
