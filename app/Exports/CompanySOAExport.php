<?php

namespace App\Exports;

use App\Models\Company;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class CompanySOAExport implements FromView
{
    
    private $company;

    public function __construct($company)
    {
        $this->company = $company;
    }
    public function view(): View
    {
        return view('exports.companies_report', [
            'company' => $this->company
        ]);
    }
    
}
