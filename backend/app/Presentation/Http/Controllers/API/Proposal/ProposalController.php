<?php

namespace App\Presentation\Http\Controllers\API\Proposal;

use App\Application\Proposal\CreateProposal\CreateProposalCommand;
use App\Application\Proposal\CreateProposal\CreateProposalHandler;
use App\Application\Proposal\GenerateProposalPdf\GenerateProposalPdfService;
use App\Application\Proposal\SendProposalEmail\SendProposalEmailService;
use App\Domain\Proposal\Contracts\ProposalRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Presentation\Http\Requests\Proposal\CreateProposalRequest;
use App\Presentation\Http\Requests\Proposal\UpdateProposalRequest;
use App\Presentation\Http\Resources\Proposal\ProposalResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Attributes as OA;

/**
 * Proposal Controller
 * 
 * Gerencia operações CRUD de propostas comerciais.
 */
#[OA\Tag(name: 'Proposals', description: 'Gerenciamento de propostas comerciais')]
class ProposalController extends Controller
{
    /**
     * @param ProposalRepositoryInterface $proposalRepository
     * @param CreateProposalHandler $createProposalHandler
     * @param GenerateProposalPdfService $pdfService
     * @param SendProposalEmailService $emailService
     */
    public function __construct(
        private readonly ProposalRepositoryInterface $proposalRepository,
        private readonly CreateProposalHandler $createProposalHandler,
        private readonly GenerateProposalPdfService $pdfService,
        private readonly SendProposalEmailService $emailService
    ) {
    }

    /**
     * List all proposals with optional filters.
     */
    #[OA\Get(
        path: '/api/proposals',
        summary: 'Listar propostas',
        security: [['sanctum' => []]],
        tags: ['Proposals'],
        parameters: [
            new OA\Parameter(name: 'status', in: 'query', required: false, schema: new OA\Schema(type: 'string', enum: ['draft', 'sent', 'approved', 'rejected', 'expired'])),
            new OA\Parameter(name: 'customer_id', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'search', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'per_page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 15))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lista de propostas',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/Proposal')),
                        new OA\Property(property: 'meta', type: 'object')
                    ]
                )
            )
        ]
    )]
    public function index(Request $request): AnonymousResourceCollection
    {
        $filters = [
            'status' => $request->query('status'),
            'customer_id' => $request->query('customer_id'),
            'search' => $request->query('search'),
        ];

        $perPage = (int) $request->query('per_page', 15);

        $proposals = $this->proposalRepository->getAll($filters, $perPage);

        return ProposalResource::collection($proposals);
    }

    /**
     * Create a new proposal.
     */
    #[OA\Post(
        path: '/api/proposals',
        summary: 'Criar nova proposta',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['customer_id', 'issue_date', 'expiration_date', 'status', 'items'],
                properties: [
                    new OA\Property(property: 'customer_id', type: 'integer', example: 1),
                    new OA\Property(property: 'opportunity_id', type: 'integer', nullable: true, example: null),
                    new OA\Property(property: 'issue_date', type: 'string', format: 'date', example: '2026-01-24'),
                    new OA\Property(property: 'expiration_date', type: 'string', format: 'date', example: '2026-02-24'),
                    new OA\Property(property: 'notes', type: 'string', nullable: true, example: 'Proposta para desenvolvimento de sistema'),
                    new OA\Property(property: 'status', type: 'string', enum: ['draft', 'sent', 'approved', 'rejected', 'expired'], example: 'draft'),
                    new OA\Property(
                        property: 'items',
                        type: 'array',
                        items: new OA\Items(
                            required: ['product_id', 'quantity', 'unit_price'],
                            properties: [
                                new OA\Property(property: 'product_id', type: 'integer', example: 1),
                                new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Customização do módulo'),
                                new OA\Property(property: 'quantity', type: 'integer', example: 10),
                                new OA\Property(property: 'unit_price', type: 'number', format: 'float', example: 250.00),
                                new OA\Property(property: 'discount_percentage', type: 'number', format: 'float', example: 10.00)
                            ],
                            type: 'object'
                        )
                    )
                ]
            )
        ),
        tags: ['Proposals'],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Proposta criada com sucesso',
                content: new OA\JsonContent(ref: '#/components/schemas/Proposal')
            ),
            new OA\Response(response: 422, description: 'Erro de validação')
        ]
    )]
    public function store(CreateProposalRequest $request): JsonResponse
    {
        $command = new CreateProposalCommand(
            customerId: $request->input('customer_id'),
            opportunityId: $request->input('opportunity_id'),
            issueDate: $request->input('issue_date'),
            expirationDate: $request->input('expiration_date'),
            notes: $request->input('notes'),
            status: $request->input('status'),
            createdBy: $request->user()->id,
            items: $request->input('items')
        );

        $proposal = $this->createProposalHandler->handle($command);

        return (new ProposalResource($proposal))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Show a specific proposal.
     */
    #[OA\Get(
        path: '/api/proposals/{id}',
        summary: 'Obter detalhes da proposta',
        security: [['sanctum' => []]],
        tags: ['Proposals'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Detalhes da proposta',
                content: new OA\JsonContent(ref: '#/components/schemas/Proposal')
            ),
            new OA\Response(response: 404, description: 'Proposta não encontrada')
        ]
    )]
    public function show(int $id): JsonResponse
    {
        $proposal = $this->proposalRepository->findById($id);

        if (!$proposal) {
            return response()->json([
                'message' => 'Proposta não encontrada.'
            ], 404);
        }

        $this->authorize('view', $proposal);

        return (new ProposalResource($proposal))->response();
    }

    /**
     * Update an existing proposal.
     */
    #[OA\Put(
        path: '/api/proposals/{id}',
        summary: 'Atualizar proposta',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'customer_id', type: 'integer', example: 1),
                    new OA\Property(property: 'opportunity_id', type: 'integer', nullable: true, example: null),
                    new OA\Property(property: 'issue_date', type: 'string', format: 'date', example: '2026-01-24'),
                    new OA\Property(property: 'expiration_date', type: 'string', format: 'date', example: '2026-02-24'),
                    new OA\Property(property: 'notes', type: 'string', nullable: true),
                    new OA\Property(property: 'status', type: 'string', enum: ['draft', 'sent', 'approved', 'rejected', 'expired']),
                    new OA\Property(
                        property: 'items',
                        type: 'array',
                        items: new OA\Items(
                            properties: [
                                new OA\Property(property: 'product_id', type: 'integer'),
                                new OA\Property(property: 'description', type: 'string', nullable: true),
                                new OA\Property(property: 'quantity', type: 'integer'),
                                new OA\Property(property: 'unit_price', type: 'number', format: 'float'),
                                new OA\Property(property: 'discount_percentage', type: 'number', format: 'float')
                            ],
                            type: 'object'
                        )
                    )
                ]
            )
        ),
        tags: ['Proposals'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Proposta atualizada com sucesso',
                content: new OA\JsonContent(ref: '#/components/schemas/Proposal')
            ),
            new OA\Response(response: 404, description: 'Proposta não encontrada'),
            new OA\Response(response: 422, description: 'Erro de validação')
        ]
    )]
    public function update(UpdateProposalRequest $request, int $id): JsonResponse
    {
        $proposal = $this->proposalRepository->findById($id);

        if (!$proposal) {
            return response()->json([
                'message' => 'Proposta não encontrada.'
            ], 404);
        }

        $this->authorize('update', $proposal);

        $updatedProposal = $this->proposalRepository->update($id, $request->validated());

        return (new ProposalResource($updatedProposal))->response();
    }

    /**
     * Delete a proposal.
     */
    #[OA\Delete(
        path: '/api/proposals/{id}',
        summary: 'Excluir proposta',
        security: [['sanctum' => []]],
        tags: ['Proposals'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Proposta excluída com sucesso'),
            new OA\Response(response: 404, description: 'Proposta não encontrada')
        ]
    )]
    public function destroy(int $id): JsonResponse
    {
        $proposal = $this->proposalRepository->findById($id);

        if (!$proposal) {
            return response()->json([
                'message' => 'Proposta não encontrada.'
            ], 404);
        }

        $this->authorize('delete', $proposal);

        $this->proposalRepository->delete($id);

        return response()->json([
            'message' => 'Proposta excluída com sucesso.'
        ]);
    }

    /**
     * Generate and download proposal PDF.
     */
    #[OA\Get(
        path: '/api/proposals/{id}/pdf',
        summary: 'Gerar PDF da proposta',
        security: [['sanctum' => []]],
        tags: ['Proposals'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'PDF da proposta',
                content: new OA\MediaType(
                    mediaType: 'application/pdf',
                    schema: new OA\Schema(type: 'string', format: 'binary')
                )
            ),
            new OA\Response(response: 404, description: 'Proposta não encontrada')
        ]
    )]
    public function downloadPdf(int $id)
    {
        $proposal = $this->proposalRepository->findById($id);

        if (!$proposal) {
            return response()->json([
                'message' => 'Proposta não encontrada.'
            ], 404);
        }

        $this->authorize('view', $proposal);

        return $this->pdfService->download($proposal);
    }

    /**
     * Send proposal by email.
     */
    #[OA\Post(
        path: '/api/proposals/{id}/send-email',
        summary: 'Enviar proposta por email',
        security: [['sanctum' => []]],
        tags: ['Proposals'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email', nullable: true, example: 'cliente@example.com')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Email enviado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Proposta enviada por email com sucesso.')
                    ]
                )
            ),
            new OA\Response(response: 404, description: 'Proposta não encontrada'),
            new OA\Response(response: 500, description: 'Erro ao enviar email')
        ]
    )]
    public function sendEmail(Request $request, int $id): JsonResponse
    {
        $proposal = $this->proposalRepository->findById($id);

        if (!$proposal) {
            return response()->json([
                'message' => 'Proposta não encontrada.'
            ], 404);
        }

        $this->authorize('view', $proposal);
        $emailTo = $request->input('email');

        $sent = $this->emailService->send($proposal, $emailTo);

        if (!$sent) {
            return response()->json([
                'message' => 'Erro ao enviar email. Tente novamente.'
            ], 500);
        }

        return response()->json([
            'message' => 'Proposta enviada por email com sucesso.',
            'sent_to' => $emailTo ?? $proposal->customer->email
        ]);
    }
}
