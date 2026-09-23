<?php

namespace App\Services;

use App\Models\Lpoout;
use setasign\Fpdi\Fpdi;

class LpoPdfService
{
    /**
     * Generate a PDF from the LPO print view using DomPDF.
     * Falls back to DomPDF since wkhtmltopdf (Snappy) requires a platform-specific binary.
     *
     * @return string Path to the generated temporary PDF file
     */
    public function generateLpoPdf(Lpoout $lpoout): string
    {
        $html = view('lpoouts.print-pdf', compact('lpoout'))->render();

        $tempPath = storage_path('app/temp/lpo_' . $lpoout->id . '_' . time() . '.pdf');

        if (!is_dir(dirname($tempPath))) {
            mkdir(dirname($tempPath), 0755, true);
        }

        $pdf = app('dompdf.wrapper');
        $pdf->loadHTML($html)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isRemoteEnabled' => true,
                'isHtml5ParserEnabled' => true,
            ]);

        file_put_contents($tempPath, $pdf->output());

        return $tempPath;
    }

    /**
     * Get PDF attachment paths from the related PurchaseOrder's Spatie media.
     *
     * @return array<string> Absolute file paths to PDF attachments
     */
    public function getAttachmentPaths(Lpoout $lpoout): array
    {
        $purchaseOrder = $lpoout->purchaseOrder;

        if (!$purchaseOrder) {
            return [];
        }

        $paths = [];
        $mediaItems = $purchaseOrder->getMedia();

        foreach ($mediaItems as $media) {
            if (strtolower($media->mime_type) === 'application/pdf' && file_exists($media->getPath())) {
                $paths[] = $media->getPath();
            }
        }

        return $paths;
    }

    /**
     * Merge multiple PDF files into one using FPDI.
     *
     * @param array<string> $pdfPaths Array of absolute paths to PDF files
     * @return string Path to the merged PDF file
     * @throws \Exception If no valid PDFs found or merge fails
     */
    public function mergePdfs(array $pdfPaths): string
    {
        $validPaths = array_filter($pdfPaths, function ($path) {
            return file_exists($path) && is_readable($path);
        });

        if (empty($validPaths)) {
            throw new \Exception('No valid PDF files to merge.');
        }

        if (count($validPaths) === 1) {
            return reset($validPaths);
        }

        $fpdi = new Fpdi();

        foreach ($validPaths as $pdfPath) {
            try {
                $pageCount = $fpdi->setSourceFile($pdfPath);
                for ($i = 1; $i <= $pageCount; $i++) {
                    $templateId = $fpdi->importPage($i);
                    $size = $fpdi->getTemplateSize($templateId);
                    $fpdi->AddPage($size['orientation'], [$size['width'], $size['height']]);
                    $fpdi->useTemplate($templateId);
                }
            } catch (\Exception $e) {
                \Log::warning('Skipping unreadable PDF during merge: ' . $pdfPath . ' - ' . $e->getMessage());
                continue;
            }
        }

        $mergedPath = storage_path('app/temp/merged_' . time() . '_' . uniqid() . '.pdf');

        if (!is_dir(dirname($mergedPath))) {
            mkdir(dirname($mergedPath), 0755, true);
        }

        $fpdi->Output($mergedPath, 'F');

        return $mergedPath;
    }

    /**
     * Generate LPO PDF, merge with attachments, and return the final file path.
     *
     * @return string Path to the final (possibly merged) PDF
     */
    public function generateMergedPdf(Lpoout $lpoout): string
    {
        $lpoPdfPath = $this->generateLpoPdf($lpoout);
        $attachmentPaths = $this->getAttachmentPaths($lpoout);

        if (empty($attachmentPaths)) {
            return $lpoPdfPath;
        }

        $allPdfs = array_merge([$lpoPdfPath], $attachmentPaths);

        try {
            $mergedPath = $this->mergePdfs($allPdfs);

            // Clean up the standalone LPO temp file if merge succeeded and produced a different file
            if ($mergedPath !== $lpoPdfPath && file_exists($lpoPdfPath)) {
                unlink($lpoPdfPath);
            }

            return $mergedPath;
        } catch (\Exception $e) {
            \Log::error('PDF merge failed, returning LPO-only PDF: ' . $e->getMessage());
            return $lpoPdfPath;
        }
    }

    /**
     * Clean up a temporary file.
     */
    public function cleanup(string $path): void
    {
        if (file_exists($path) && strpos($path, storage_path('app/temp')) === 0) {
            unlink($path);
        }
    }
}
