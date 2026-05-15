<?php

namespace App\Application\Proposal\SendProposalEmail;

use App\Domain\Proposal\Models\Proposal;
use App\Application\Proposal\GenerateProposalPdf\GenerateProposalPdfService;
use App\Mail\ProposalMail;
use Illuminate\Support\Facades\Mail;

/**
 * Send Proposal Email Service
 * 
 * Envia email com proposta comercial anexada em PDF.
 */
class SendProposalEmailService
{
    /**
     * @param GenerateProposalPdfService $pdfService
     */
    public function __construct(
        private readonly GenerateProposalPdfService $pdfService
    ) {
    }

    /**
     * Envia proposta por email para o cliente
     *
     * @param Proposal $proposal
     * @param string|null $emailTo Email de destino (opcional, usa customer email por padrão)
     * @return bool True se email foi enviado com sucesso
     */
    public function send(Proposal $proposal, ?string $emailTo = null): bool
    {
        $proposal->loadMissing(['customer', 'creator', 'items.product.category']);

        $pdfContent = $this->pdfService->output($proposal);

        $to = $emailTo ?? $proposal->customer->email;

        try {
            Mail::to($to)->send(new ProposalMail($proposal, $pdfContent));
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Erro ao enviar proposta por email', [
                'proposal_id' => $proposal->id,
                'email_to' => $to,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }

    /**
     * Envia proposta para múltiplos destinatários
     *
     * @param Proposal $proposal
     * @param array $emails Lista de emails
     * @return array ['success' => [...], 'failed' => [...]]
     */
    public function sendToMultiple(Proposal $proposal, array $emails): array
    {
        $result = [
            'success' => [],
            'failed' => []
        ];

        foreach ($emails as $email) {
            if ($this->send($proposal, $email)) {
                $result['success'][] = $email;
            } else {
                $result['failed'][] = $email;
            }
        }

        return $result;
    }
}
