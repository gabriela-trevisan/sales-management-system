<?php

namespace App\Application\Proposal\GenerateProposalPdf;

use App\Domain\Proposal\Models\Proposal;
use Barryvdh\DomPDF\Facade\Pdf;

/**
 * Generate Proposal PDF Service
 * 
 * Gera PDF da proposta comercial com layout profissional.
 */
class GenerateProposalPdfService
{
    /**
     * Gera PDF da proposta
     *
     * @param Proposal $proposal
     * @return \Barryvdh\DomPDF\PDF
     */
    public function generate(Proposal $proposal): \Barryvdh\DomPDF\PDF
    {
        $proposal->loadMissing(['customer', 'creator', 'items.product.category']);

        $pdf = Pdf::loadView('proposals.pdf', ['proposal' => $proposal]);

        $pdf->setPaper('A4', 'portrait');
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);

        return $pdf;
    }

    /**
     * Gera e retorna PDF para download
     *
     * @param Proposal $proposal
     * @param string|null $filename Nome do arquivo (sem extensão)
     * @return \Illuminate\Http\Response
     */
    public function download(Proposal $proposal, ?string $filename = null)
    {
        $pdf = $this->generate($proposal);
        
        $filename = $filename ?? "proposta-{$proposal->number}";
        
        return $pdf->download("{$filename}.pdf");
    }

    /**
     * Gera e retorna PDF inline (para visualização no navegador)
     *
     * @param Proposal $proposal
     * @return \Illuminate\Http\Response
     */
    public function stream(Proposal $proposal)
    {
        $pdf = $this->generate($proposal);
        
        return $pdf->stream("proposta-{$proposal->number}.pdf");
    }

    /**
     * Gera e salva PDF em arquivo
     *
     * @param Proposal $proposal
     * @param string $path Caminho completo com nome do arquivo
     * @return string Caminho do arquivo salvo
     */
    public function save(Proposal $proposal, string $path): string
    {
        $pdf = $this->generate($proposal);
        
        $pdf->save($path);
        
        return $path;
    }

    /**
     * Retorna o PDF como string (útil para anexar em emails)
     *
     * @param Proposal $proposal
     * @return string Conteúdo binário do PDF
     */
    public function output(Proposal $proposal): string
    {
        $pdf = $this->generate($proposal);
        
        return $pdf->output();
    }
}
