<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    title: "Sales Management System API",
    description: "API completa para gerenciamento de vendas e CRM",
    contact: new OA\Contact(
        name: "Gabriela Trevisan",
        email: "gabriela@salesmanagement.com"
    )
)]
#[OA\Server(
    url: "http://localhost:8000",
    description: "Servidor de Desenvolvimento"
)]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    scheme: "bearer",
    bearerFormat: "JWT"
)]
#[OA\Tag(
    name: "Authentication",
    description: "Endpoints de autenticação"
)]
#[OA\Tag(
    name: "Customers",
    description: "Gerenciamento de clientes"
)]
#[OA\Tag(
    name: "Dashboard",
    description: "Métricas e dados do dashboard"
)]
abstract class Controller
{
    //
}
