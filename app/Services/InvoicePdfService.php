<?php

namespace App\Services;

use App\Models\Invoice;
use Illuminate\Support\Facades\Log;
use PDF;

class InvoicePdfService
{
    /**
     * Generate invoice PDF with specified letterhead template using DomPDF
     *
     * @param Invoice $invoice
     * @param string $letterheadType (fts|experts|ftsits)
     * @return string Path to generated PDF
     */
    public function generateInvoicePdf(Invoice $invoice, string $letterheadType = 'fts'): string
    {
        try {
            // Validate letterhead type
            $validTypes = ['fts', 'experts', 'ftsits'];
            if (!in_array(strtolower($letterheadType), $validTypes)) {
                $letterheadType = 'fts';
            }

            // Render the print view to HTML
            $html = view('invoices.show-print', [
                'invoice' => $invoice,
                'letterheadType' => strtolower($letterheadType)
            ])->render();

            $tempPath = storage_path('app/temp/invoice_' . $invoice->id . '_' . time() . '.pdf');

            if (!is_dir(dirname($tempPath))) {
                mkdir(dirname($tempPath), 0755, true);
            }

            // Use DomPDF for PDF generation
            // DomPDF is pure PHP with no external dependencies
            $pdf = app('dompdf.wrapper')
                ->loadHTML($html)
                ->setPaper('a4', 'portrait')
                ->setOptions([
                    'enable_remote' => true,
                    'enable_html5_parser' => false,
                ]);

            file_put_contents($tempPath, $pdf->output());

            Log::info('Invoice PDF generated successfully', [
                'invoice_id' => $invoice->id,
                'invoice_no' => $invoice->invoice_no,
                'letterhead_type' => $letterheadType,
                'pdf_path' => $tempPath,
                'file_size' => filesize($tempPath),
            ]);

            return $tempPath;
        } catch (\Exception $e) {
            Log::error('Invoice PDF generation failed for Invoice #' . $invoice->id, [
                'letterhead_type' => $letterheadType,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Delete temporary PDF file
     *
     * @param string $filePath
     * @return bool
     */
    public function deletePdf(string $filePath): bool
    {
        if (file_exists($filePath)) {
            return @unlink($filePath);
        }
        return true;
    }
}
