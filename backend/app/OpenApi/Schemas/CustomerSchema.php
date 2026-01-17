<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Customer",
    title: "Customer",
    description: "Modelo de Cliente",
    type: "object",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "name", type: "string", example: "João Silva"),
        new OA\Property(property: "document", type: "string", example: "123.456.789-00"),
        new OA\Property(property: "email", type: "string", format: "email", example: "joao@example.com"),
        new OA\Property(property: "phone", type: "string", nullable: true, example: "(11) 98765-4321"),
        new OA\Property(
            property: "status",
            type: "string",
            enum: ["active", "inactive", "prospect", "churned"],
            example: "active"
        ),
        new OA\Property(property: "rfm_score", type: "integer", nullable: true, example: 450),
        new OA\Property(
            property: "segment",
            properties: [
                new OA\Property(property: "id", type: "integer", example: 1),
                new OA\Property(property: "name", type: "string", example: "Premium")
            ],
            type: "object",
            nullable: true
        ),
        new OA\Property(
            property: "assigned_to",
            properties: [
                new OA\Property(property: "id", type: "integer", example: 1),
                new OA\Property(property: "name", type: "string", example: "Vendedor Silva")
            ],
            type: "object",
            nullable: true
        ),
        new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-01-07T10:00:00.000000Z"),
        new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2026-01-07T10:00:00.000000Z")
    ]
)]
class CustomerSchema
{
    // Este arquivo só existe para definir o schema OpenAPI
}
