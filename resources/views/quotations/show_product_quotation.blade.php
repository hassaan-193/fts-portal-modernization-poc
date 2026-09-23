@extends('layouts.master')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark text-weight-bold">@lang('crud.detail') @lang('models/quotations.singular')</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{!! route('quotations.index') !!}">@lang('models/quotations.singular')</a></li>
                        <li class="breadcrumb-item active">@lang('crud.detail')</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="invoice p-3 mb-3">
                        <div class="row no-print">
                            <div class="col-12" style="position: absolute;width: 100%;right: 15px;">
                                <button type="button" onclick="printWitFtsits()" class="btn btn-success float-right m-1"><i class="fas fa-print"></i> FTSITS Print</button>
                                <button type="button" onclick="printWithExpert()" class="btn btn-warning float-right m-1"><i class="fas fa-print"></i> EXPERTS Print</button>
                                <button type="button" onclick="printWithFts()" class="btn btn-danger float-right m-1"><i class="fas fa-print"></i> FTS Print</button>
                            </div>
                        </div>
                        <div class="col-xs-12 text-center" >
                            <img class="fts_img images" src="{{asset('dist/img/fts_latter_head.jpeg')}}" style="width:100%;margin:auto;" />
                            <img class="expert_img images" src="{{asset('dist/img/experts_letter_head.jpeg')}}" style="width:100%; margin:auto; display:none;" />
                            <img class="ftsits_img images" src="{{asset('dist/img/ftsits_letter_head.jpeg')}}" style="width:100%;margin:auto; display:none;" />
                        </div>
                        <h2 class="text-center text-weight-bold" style="text-transform: uppercase;">Quotation</h2>
                        <div class="row justify-content-between mt-4">
                            <div class="col-md-5">
                                <table class="table expandable-table" border="1">
                                    <tr>
                                        <td class="left-custom"><strong>Client:</strong></td>
                                        <td class="right-custom trn_no">{{$quotation->company->name}}</td>
                                    </tr>
                                    <tr>
                                        <td class="left-custom"><strong>Project:</strong></td>
                                        <td class="right-custom">{{$quotation->quotation_type->name}}</td>
                                    </tr>
                                    <tr>
                                        <td class="left-custom"><strong>Location:</strong></td>
                                        <td class="right-custom">{{$quotation->location}}</td>
                                    </tr>
                                    <tr>
                                        <td class="left-custom"><strong>Subject:</strong></td>
                                        <td class="right-custom">{{$quotation->subject}}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-4">
                                <table class="table expandable-table" border="1">

                                </table>
                            </div>
                            <div class="col-md-3">
                                <table class="table expandable-table" border="1">
                                    <tr>
                                        <td class="left-custom" style="text-align:right;">Date:</td>
                                        <td class="right-custom" style="text-align:right;">{{$quotation->date}}</td>
                                    </tr>
                                    <tr>
                                        <td class="right-custom" style="text-align:right;">Refno:</td>
                                        <td class="right-custom" style="text-align:right;">&nbsp;Q-00{{$quotation->id}}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <table class="table expandable-table" border="1">
                            <thead>
                                <tr>
                                    <th>SR No.</th>
                                    <th>Description</th>
                                    <th>QTY</th>
                                    <th>Unit</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($quotation->products as $index => $product)
                                <tr class="expandable-header">
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $product->product->name }}</td>
                                    <td>{{ $product->quantity }}</td>
                                    <td>{{ $product->unit_price }}</td>
                                    <td>{{ $product->total_price }}</td>
                                </tr>
                                @endforeach
                                <tr class="expandable-body" data-expandable-table="collapsed">
                                    <td colspan="3"></td>
                                    <td style="text-align:right;">
                                        <strong>Net Amount</strong>
                                    </td>
                                    <td>
                                        <strong>{{ $quotation->products->sum('total_price') }}</strong>
                                    </td>
                                </tr>
                                <tr class="expandable-body" data-expandable-table="collapsed">
                                    <td colspan="3"></td>
                                    <td style="text-align:right;">
                                        <strong>VAT (5%)</strong>
                                    </td>
                                    <td>
                                        <strong>{{ $quotation->products->sum('total_price') * 0.05 }}</strong>
                                    </td>
                                </tr>
                                <tr class="expandable-body" data-expandable-table="collapsed">
                                    <td colspan="3"></td>
                                    <td style="text-align:right;">
                                        <strong>Total Amount</strong>
                                    </td>
                                    <td>
                                        <strong>{{ $quotation->products->sum('total_price') * 1.05 }}</strong>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="row justify-content-between k-term">
                            <div class="col-md-6">
                                <dl class="row">
                                    <dt class="col-sm-12">Payment:</dt>
                                </dl>
                                <div>
                                    {!!  $quotation->payment !!}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <dl class="row">
                                    <dt class="col-sm-12">Exclusion:</dt>
                                </dl>
                                <div>
                                    {!!  $quotation->exclusion !!}
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-between">
                            <div class="col-md-12">
                                <h6>
                                    <strong id="title">NOTE: Approving the below table with a Signature & Stamp shall be considered as official approval of the above quote in line with Commercial, Technical Terms & Conditions & Payment Terms. </strong>
                                </h6>
                                <table class="table expandable-table" border="1">
                                    <tr>
                                        <th colspan="3" style="text-align:center;">FOR CLIENT APPROVAL</th>
                                    </tr>
                                    <tr>
                                        <td class="k-left-custom">Quotation No:</td>
                                        <td class="right-custom" colspan="2"></td>
                                    </tr>
                                    <tr>
                                        <td class="k-left-custom">Name:</td>
                                        <td colspan="2"></td>
                                    </tr>
                                    <tr>
                                        <td class="k-left-custom">Position:</td>
                                        <td colspan="2"></td>
                                    </tr>
                                    <tr>
                                        <td class="k-left-custom">Sign:</td>
                                        <td class="k-center-custom"></td>
                                        <td class="k-right-custom">Stamp:</td>
                                    </tr>
                                    <tr>
                                        <td class="k-left-custom">Date:</td>
                                        <td colspan="2"></td>
                                    </tr>
                                </table>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-xs-12 text-center">
                                <img class="fts_img images" src="{{asset('dist/img/fts_letter_footer.jpg')}}" style="width:100%;margin:auto;" />
                                <img class="expert_img images" src="{{asset('dist/img/experts_letter_footer.jpeg')}}" style="width:100%; margin:auto; display:none;" />
                                <img class="ftsits_img images" src="{{asset('dist/img/ftsits_letter_footer.jpeg')}}" style="width:100%;margin:auto; display:none;" />
                          </div>
                        </div>
                    </div>
                </div>
            </div>
            <style>
                td.k-left-custom {
                width: 35%;
                font-weight: 600;
            }
            td.k-center-custom {
                width: 30%;
                font-weight: 600;
                height:90px;
            }
            td.k-right-custom {
                width: 35%;
                font-weight: 600;
            }
            </style>
        </div>
    </div>
    <div class="content">
        @include('components.model_files',['model' => $quotation ])
    </div>
@endsection

