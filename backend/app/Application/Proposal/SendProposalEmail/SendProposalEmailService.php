<?php

namespace App\Application\Proposal\SendProposalEmail;

use App\Application\Proposal\GenerateProposalPdf\GenerateProposalPdfService;
use App\Domain\Proposal\Contracts\ProposalRepositoryInterface;
use App\Domain\Proposal\Enums\ProposalStatus;
use App\Domain\Proposal\Models\Proposal;
use App\Mail\ProposalMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendProposalEmailService
{
    public function __construct(
        private readonly GenerateProposalPdfService $pdfService,
        private readonly ProposalRepositoryInterface $proposalRepository,
    ) {}

    public function send(Proposal $proposal, ?string $emailTo = null): bool
    {
        $proposal->loadMissing(['customer', 'creator', 'items.product.category']);

        $pdfContent = $this->pdfService->output($proposal);
        $to = $emailTo ?? $proposal->customer->email;

        try {
            Mail::to($to)->send(new ProposalMail($proposal, $pdfContent));

            if ($proposal->proposalStatus() === ProposalStatus::Draft) {
                $proposal->send();
                $this->proposalRepository->save($proposal);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Erro ao enviar proposta por email', [
                'proposal_id' => $proposal->id,
                'email_to' => $to,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * @param array<int, string> $emails
     * @return array{success: array<int, string>, failed: array<int, string>}
     */
    public function sendToMultiple(Proposal $proposal, array $emails): array
    {
        $result = [
            'success' => [],
            'failed' => [],
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
