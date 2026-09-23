<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Link;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\TableComponent;

class CardWithTable extends TableComponent
{
    public $tableClass = 'table table-sm';
    public $getProject;
    public $getType;
    public $getQuery;
    public $getColumns;


    public function mount($project, $title)
    {
        $this->getProject = $project;
        $this->getType =  $title;

        // get columns for table
        $this->getColumns =  $this->$title();
    }

    public function hydrate()
    {
        $type = $this->getType;
        $this->getColumns =  $this->$type();
    }

    public function query() : Builder
    {
        return $this->getQuery['query'];
    }

    public function columns() : array
    {
        return $this->getColumns;
    }

    public function Invoices(){
        $project_id = $this->getProject;

        $this->getQuery =  [
            'query' => \App\Models\Invoice::whereHas('quotation.project',function($query) use ($project_id){
                $query->where('projects.id',$project_id);
            })
        ];
        return [
            Column::make('Invoice','invoice_no')
                ->searchable()
                ->sortable(),
            Column::make('Start Date','start_date'),
            Column::make('End Date','end_date'),
            Column::make('Total Amount','total_amount'),
            Column::make('Action')
                ->components([
                    Link::make(false)
                    ->icon('fa fa-eye')
                    ->class('btn btn-ghost-primary')
                    ->href(function($model) {
                        return route('invoices.show', $model->id);
                    })
                ]),
        ];
    }
    public function Lpoins(){
        $project_id = $this->getProject;

        $this->getQuery =  [
            'query' => \App\Models\Lpoin::with('quotation')->whereHas('quotation.project',function($query) use ($project_id){
                $query->where('projects.id',$project_id);
            })
        ];
        return [
            Column::make('Ref #','ref_no')
                ->searchable()
                ->sortable(),
            Column::make('Date Issue','date_issue'),
            Column::make('Date Due','date_due'),
            Column::make('Amount','quotation.contract_value'),
            Column::make('Action')
                ->components([
                    Link::make(false)
                    ->icon('fa fa-eye')
                    ->class('btn btn-ghost-primary')
                    ->href(function($model) {
                        return route('lpoins.show', $model->id);
                    })
                ]),
        ];
    }
    public function Extensions(){
        $this->getQuery =  [
            'query' => \App\Models\ProjectExtension::with('quotation')->where('project_id', $this->getProject)
        ];

        return [
            Column::make('Quotation','quotation_link')
            ->html()
            ->customAttribute(),
            Column::make('Name','name')
                ->searchable()
                ->sortable(),
            Column::make('Value')
                ->searchable()
                ->sortable(),
            Column::make('Action')
                ->components([
                    Link::make(false)
                    ->icon('fa fa-eye')
                    ->class('btn btn-ghost-primary')
                    ->href(function($model) {
                        return route('projects.view_extensions', $model->id);
                    })
                ]),
        ];
    }
    public function Receipts(){
        $project_id = $this->getProject;

        $this->getQuery =  [
            'query' => \App\Models\Receipt::with(['transaction_payment_type'])->whereHas('project',function($query) use ($project_id){
                $query->where('projects.id',$project_id);
            })
        ];
        return [
            Column::make('Date','date_time'),
            Column::make('Type','transaction_payment_type.name')
                ->searchable()
                ->sortable(),
            Column::make('Amount','total')
                ->searchable(),
            Column::make('Status')
                ->view('projects.projects_partials.payment_status_tag'),
            Column::make('Action')
                ->components([
                    Link::make(false)
                    ->icon('fa fa-eye')
                    ->class('btn btn-ghost-primary')
                    ->href(function($model) {
                        return route('receipts.show', $model->id);
                    })
                ]),
        ];
    }
    public function Lpoouts(){
        $project_id = $this->getProject;

        $this->getQuery =  [
            'query' => \App\Models\Lpoout::where('project_id',$project_id)->where('is_latest_revision', true)
            // 'query' => \App\Models\ProjectLpoout::with('lpoout')->where('project_id',$project_id)
        ];
        return [
            Column::make('Date','date'),
            Column::make('Vendor','vendor_link')
                ->html()
                ->customAttribute(),
            Column::make('Total Amount','total_amount'),
            Column::make('Action')
                ->components([
                    Link::make(false)
                    ->icon('fa fa-eye')
                    ->class('btn btn-ghost-primary')
                    ->href(function($model) {
                        return route('lpoouts.show', $model->id);
                    })
                ]),
        ];
    }
    public function Petty_Cashes(){
        $project_id = $this->getProject;

        $this->getQuery =  [
            'query' => \App\Models\PettyCash::with('payment_type')->where('project_id',$project_id)
        ];
        return [
            Column::make('Date','date_time'),
            Column::make('Description','description'),
            Column::make('Type','payment_type.name'),
            Column::make('Total Amount','total_amount'),
            Column::make('Action')
                ->components([
                    Link::make(false)
                    ->icon('fa fa-eye')
                    ->class('btn btn-ghost-primary')
                    ->href(function($model) {
                        return route('pettyCashes.show', $model->id);
                    })
                ]),
        ];
    }
    public function Payments(){
        $project_id = $this->getProject;

        $this->getQuery =  [
            'query' => \App\Models\Payment::with(['transaction_payment_type'])
                ->where('transactionable_type', 'App\Models\PaymentInvoice')
                ->whereHasMorph('transactionable', [\App\Models\PaymentInvoice::class], function ($query) use ($project_id) {
                    $query->where('project_id', $project_id);
                })
        ];
        return [
            Column::make('Date','date_time'),
            Column::make('Type','transaction_payment_type.name')
                ->searchable()
                ->sortable(),
            Column::make('Amount','total')
                ->searchable(),
            Column::make('Status')
                ->view('projects.projects_partials.payment_status_tag'),
            Column::make('Action')
                ->components([
                    Link::make(false)
                    ->icon('fa fa-eye')
                    ->class('btn btn-ghost-primary')
                    ->href(function($model) {
                        return route('payments.show', $model->id);
                    })
                ]),
        ];
    }
    public function Extension_Invoices(){
        $project_id = $this->getProject;

        $this->getQuery =  [
            'query' => \App\Models\Invoice::whereHas('quotation.extension_project',function($query) use ($project_id){
                $query->where('projects.id',$project_id);
            })
        ];
        return [
            Column::make('Invoice','invoice_no')
                ->searchable()
                ->sortable(),
            Column::make('Start Date','start_date'),
            Column::make('End Date','end_date'),
            Column::make('Total Amount','total_amount'),
            Column::make('Action')
                ->components([
                    Link::make(false)
                    ->icon('fa fa-eye')
                    ->class('btn btn-ghost-primary')
                    ->href(function($model) {
                        return route('invoices.show', $model->id);
                    })
                ]),
        ];
    }
    public function Extension_Lpoins(){
        $project_id = $this->getProject;

        $this->getQuery =  [
            'query' => \App\Models\Lpoin::whereHas('quotation.extension_project',function($query) use ($project_id){
                $query->where('projects.id',$project_id);
            })
        ];
        return [
            Column::make('Ref #','ref_no')
                ->searchable()
                ->sortable(),
            Column::make('Date Issue','date_issue'),
            Column::make('Date Due','date_due'),
            Column::make('Action')
                ->components([
                    Link::make(false)
                    ->icon('fa fa-eye')
                    ->class('btn btn-ghost-primary')
                    ->href(function($model) {
                        return route('lpoins.show', $model->id);
                    })
                ]),
        ];
    }
    public function Extension_Receipts(){
        $project_id = $this->getProject;

        $this->getQuery =  [
            'query' => \App\Models\Receipt::with(['transaction_payment_type'])->whereHas('extension_project',function($query) use ($project_id){
                $query->where('projects.id',$project_id);
            })
        ];
        return [
            Column::make('Date','date_time'),
            Column::make('Type','transaction_payment_type.name')
                ->searchable()
                ->sortable(),
            Column::make('Amount','total')
                ->searchable(),
            Column::make('Action')
                ->components([
                    Link::make(false)
                    ->icon('fa fa-eye')
                    ->class('btn btn-ghost-primary')
                    ->href(function($model) {
                        return route('receipts.show', $model->id);
                    })
                ]),
        ];
    }

    public function Payment_invoices(){
        $vendor_id = $this->getProject;

        $this->getQuery =  [
            'query' => \App\Models\PaymentInvoice::where('vendor_id',$vendor_id)
        ];
        return [
            Column::make('Invoice #','invoice_no')
                ->searchable()
                ->sortable(),
            Column::make('Start Date','start_date'),
            Column::make('End Date','end_date'),
            Column::make('Amount','total_amount'),
            Column::make('Action')
                ->components([
                    Link::make(false)
                    ->icon('fa fa-eye')
                    ->class('btn btn-ghost-primary')
                    ->href(function($model) {
                        return route('paymentInvoices.show', $model->id);
                    })
                ]),
        ];
    }

    public function Vendor_Payments(){
        $vendor_id = $this->getProject;

        $this->getQuery =  [
            'query' => \App\Models\Payment::with(['transaction_payment_type', 'transactionable'])
                ->where('transactionable_type', 'App\Models\PaymentInvoice')
                ->whereHasMorph('transactionable', [\App\Models\PaymentInvoice::class], function ($query) use ($vendor_id) {
                    $query->where('vendor_id', $vendor_id);
                })
        ];
        return [
            Column::make('Date','date_time'),
            Column::make('Type','transaction_payment_type.name')
                ->searchable()
                ->sortable(),
            Column::make('Amount','total')
                ->searchable(),
            Column::make('Action')
                ->components([
                    Link::make(false)
                    ->icon('fa fa-eye')
                    ->class('btn btn-ghost-primary')
                    ->href(function($model) {
                        return route('payments.show', $model->id);
                    })
                ]),
        ];
    }

    public function Vendor_Lpoouts(){
        $vendor_id = $this->getProject; // in this context, getProject will actually hold vendor_id

        $this->getQuery = [
            'query' => \App\Models\Lpoout::with(['vendor', 'lpo_out_type'])
                ->where('vendor_id', $vendor_id)
        ];

        return [
            Column::make('Invoice No', 'lpo_invoice_no')
                ->searchable()
                ->sortable(),
            Column::make('Date', 'date')
                ->sortable(),
            Column::make('Vendor', 'vendor_link')
                ->html()
                ->customAttribute(),
            Column::make('LPO Type', 'lpo_out_type.name')
                ->searchable()
                ->sortable(),
            Column::make('Total Amount', 'total_amount')
                ->sortable(),
            Column::make('Status', 'status')
                ->sortable(),
            Column::make('Action')
                ->components([
                    Link::make(false)
                        ->icon('fa fa-eye')
                        ->class('btn btn-ghost-primary')
                        ->href(function ($model) {
                            return route('lpoouts.show', $model->id);
                        })
                ]),
        ];
    }
}
