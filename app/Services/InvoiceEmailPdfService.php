<?php

namespace App\Services;

use PDF;

/**
 * Service for generating email template as PDF
 */
class InvoiceEmailPdfService
{
    /**
     * Generate email template as PDF
     *
     * @param  object $invoice
     * @param  object $company
     * @return string Path to generated PDF file
     * @throws \Exception
     */
    public function generateEmailTemplatePdf($invoice, $company)
    {
        try {
            // Render the HTML from the view
            $html = \View::make('emails.invoice-email', [
                'invoice' => $invoice,
                'company' => $company,
            ])->render();

            // Create PDF using DomPDF wrapper
            $pdf = \PDF::loadHTML($html);
            $pdf->setPaper('A4', 'portrait');

            // Save to temporary file
            $tempPath = storage_path('app/temp/email_' . uniqid() . '.pdf');
            
            // Ensure temp directory exists
            if (!is_dir(dirname($tempPath))) {
                mkdir(dirname($tempPath), 0755, true);
            }

            // Save the PDF
            $pdf->save($tempPath);

            if (!file_exists($tempPath)) {
                throw new \Exception('Failed to create PDF file');
            }

            return $tempPath;
        } catch (\Exception $e) {
            \Log::error('Email PDF generation failed: ' . $e->getMessage());
            throw new \Exception('Failed to generate email PDF: ' . $e->getMessage());
        }
    }

    /**
     * Delete PDF file
     *
     * @param  string $filePath
     * @return bool
     */
    public function deletePdf($filePath)
    {
        try {
            if ($filePath && file_exists($filePath)) {
                unlink($filePath);
                return true;
            }
            return false;
        } catch (\Exception $e) {
            \Log::warning("Failed to delete email PDF: " . $e->getMessage());
            return false;
        }
    }
}
