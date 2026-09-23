<?php

namespace App\Services;

use App\Models\StaffPayroll;

class StaffPayrollPdfService
{
    /**
     * Generate a PDF pay slip from the staff payroll record using DomPDF.
     *
     * @param StaffPayroll $payroll
     * @return string Path to the generated temporary PDF file
     */
    public function generatePayslipPdf(StaffPayroll $payroll): string
    {
        $html = view('staff_payrolls.pdf-payslip', compact('payroll'))->render();

        $tempPath = storage_path('app/temp/payslip_' . $payroll->id . '_' . time() . '.pdf');

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
}
