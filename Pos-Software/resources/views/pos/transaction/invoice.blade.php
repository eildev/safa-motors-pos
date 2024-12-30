@extends('master')
@section('title', '| Transaction Invoice')
@section('admin')
    <div class="row" bis_skin_checked="1">
        <div class="col-md-2">

        </div>
        <div class="col-md-8 print_page card card-body" bis_skin_checked="1">
            <div class="row justify-content-center" bis_skin_checked="1">
                <div class="col-md-12" bis_skin_checked="1">
                    <div id="print-area" bis_skin_checked="1">
                        <div class="container-fluid d-flex justify-content-between">
                            <div class="col-lg-6 ps-0" bis_skin_checked="1">
                                <div class="logo-area" bis_skin_checked="1">
                                    {{-- <h1 class="title">EIL POS Bangladesh</h1> --}}
                                    @if (!empty($invoice_logo_type))
                                        @if ($invoice_logo_type == 'Name')
                                            <a href="#"
                                                class="noble-ui-logo logo-light d-block mt-3">{{ $siteTitle }}</a>
                                        @elseif($invoice_logo_type == 'Logo')
                                            @if (!empty($logo))
                                                <img class="margin_left_m_14" height="100" width="200"
                                                    src="{{ url($logo) }}" alt="logo">
                                            @else
                                                <p class="mt-1 mb-1 show_branch_name"><b>{{ $siteTitle }}</b></p>
                                            @endif
                                        @elseif($invoice_logo_type == 'Both')
                                            @if (!empty($logo))
                                                <img class="margin_left_m_14" height="90" width="150"
                                                    src="{{ url($logo) }}" alt="logo">
                                            @endif
                                            <p class="mt-1 mb-1 show_branch_name"><b>{{ $siteTitle }}</b></p>
                                        @endif
                                    @else
                                        <a href="#"
                                            class="noble-ui-logo logo-light d-block mt-3">EIL<span>POS</span></a>
                                    @endif
                                </div>
                                <p class="show_branch_address w-50">{{ $address ?? 'Banasree' }}</p>
                                <p class="show_branch_address">{{ $phone ?? '' }}, 01708008705, 01720389177</p>
                                <p class="show_branch_address">{{ $email ?? '' }}</p>
                                <!--<hr>-->
                            </div>
                            <div class="col-lg-6 pe-0 text-end">
                                <h4 class="fw-bolder text-uppercase text-end mt-4 mb-2">invoice</h4>
                                <h6 class="text-end mb-5 pb-4">#PAYMENT-{{ $transaction->id ?? 0 }}</h6>

                                <p class="text-end mb-1 mt-5">Total </p>
                                <h4 class="text-end fw-normal">৳
                                    @if ($transaction->particulars == 'Return' || $transaction->particulars == 'Adjust Due Collection')
                                        @if ($transaction->particulars == 'Adjust Due Collection')
                                            {{ number_format($transaction->credit, 2) ?? 0 }}
                                        @else
                                            {{ number_format($transaction->debit, 2) ?? 0 }}
                                        @endif
                                    @elseif ($transaction->particulars == 'SaleDue' || $transaction->particulars == 'PurchaseDue')
                                        {{ number_format($transaction->credit, 2) ?? 0 }}
                                    @else
                                        {{ number_format($transaction->debit, 2) ?? 0 }}
                                    @endif
                                </h4>
                                <h6 class="mb-0 mt-2 text-end fw-normal"><span class="text-muted show_purchase_date">Invoice
                                        Date :</span>
                                    {{ \Carbon\Carbon::parse($transaction->date)->format('d F Y') ?? '' }}</h6>
                            </div>
                        </div>
                        <div class="container-fluid my-3">
                            <h4 style="text-align: center; font-weight:bold;">
                                @if ($transaction->particulars == 'Return' || $transaction->particulars == 'Adjust Due Collection')
                                    @if ($transaction->particulars == 'Adjust Due Collection')
                                        Return
                                    @else
                                        Cash Return
                                    @endif
                                @elseif (strpos($transaction->particulars, 'Sale#') !== false)
                                    Sale
                                @elseif ($transaction->particulars == 'SaleDue')
                                    Cash Deposit
                                @elseif (strpos($transaction->particulars, 'Purchase#') !== false)
                                    Purchase
                                @elseif ($transaction->particulars == 'PurchaseDue')
                                    Due Payment
                                @else
                                    {{ $transaction->particulars ?? '' }}
                                @endif
                                Invoice
                            </h4>
                        </div>
                        <table class="table payment-invoice-header mt-2">
                            <tbody>
                                <tr>
                                    <td colspan="4" style="border-top: 0">

                                    </td>
                                </tr>
                                <tr>
                                    <td style="width:15%;">Payment No :</td>
                                    <td style="width: 35%;">{{ $transaction->id }}</td>
                                    <td style="width:15%;">Date :</td>
                                    <td>{{ \Carbon\Carbon::parse($transaction->date)->format('d F Y') ?? '' }}</td>
                                </tr>
                                <tr>
                                    <td>Name :</td>
                                    <td>
                                        {{ $data->name ?? '' }}
                                    </td>
                                    <td>Mobile :</td>
                                    <td>
                                        {{ $data->phone ?? '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>Account Type :</td>
                                    <td>
                                        @if (isset($transaction->customer_id))
                                            <span>Customer</span>
                                        @elseif(isset($transaction->supplier_id))
                                            <span>Supplier</span>
                                        @elseif(isset($transaction->others_id))
                                            <span>Investor</span>
                                        @else
                                            <span></span>
                                        @endif
                                    </td>
                                    <td>Account:</td>
                                    <td>{{ $transaction->bank->name ?? '' }}</td>
                                </tr>
                                <tr>
                                    <td>Transaction Type :</td>
                                    <td style="text-transform: capitalize">
                                        @if ($transaction->payment_type == 'pay')
                                            <span>Cash Payment</span>
                                        @elseif($transaction->payment_type == 'receive')
                                            <span>Cash Received</span>
                                        @endif
                                    </td>
                                    <td>Note :</td>
                                    <td>@php
                                        $note = $transaction->note;
                                        $noteChunks = str_split($note, 70);
                                        echo implode('<br>', $noteChunks);
                                    @endphp</td>
                                </tr>

                            </tbody>
                        </table>
                        <table class="table table-bordered table-plist my-3 mt-4">
                            <tbody>
                                <tr class="bg-primary">
                                    <th>Date</th>
                                    <th>Total</th>
                                    <th>Paid</th>
                                    <th>Total Due</th>
                                </tr>
                            </tbody>
                            <tbody>
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($transaction->date)->format('d F Y') }}</td>
                                    <td>
                                        @if ($transaction->particulars == 'Return' || $transaction->particulars == 'Adjust Due Collection')
                                            @if ($transaction->particulars == 'Adjust Due Collection')
                                                {{ number_format($transaction->credit, 2) ?? 0 }}
                                                @php
                                                    $total = $transaction->credit;
                                                @endphp
                                            @else
                                                {{ number_format($transaction->debit, 2) ?? 0 }}
                                                @php
                                                    $total = $transaction->debit;
                                                @endphp
                                            @endif
                                        @elseif ($transaction->particulars == 'SaleDue' || $transaction->particulars == 'PurchaseDue')
                                            {{ number_format($transaction->credit, 2) ?? 0 }}
                                            @php
                                                $total = $transaction->credit;
                                            @endphp
                                        @else
                                            {{ number_format($transaction->debit, 2) ?? 0 }}
                                            @php
                                                $total = $transaction->debit;
                                            @endphp
                                        @endif
                                    </td>
                                    <td>
                                        @if ($transaction->particulars == 'Return')
                                            00
                                        @else
                                            {{ number_format($transaction->credit, 2) ?? 0 }}
                                        @endif
                                    </td>
                                    <td>
                                        {{-- @dd($transaction->credit) --}}
                                        @if ($total == $transaction->credit)
                                            00.00
                                        @elseif ($transaction->balance)
                                            {{ number_format($transaction->balance, 2) ?? 0 }}
                                        @else
                                            00.00
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <button class="btn btn-secondary btn-block" onclick="window.print();">
                        <i class="fa fa-print"></i>
                        Print
                    </button>
                    <a href="{{ route('transaction.add') }}" class="btn btn-primary buttona btn-block">
                        <i class="fa fa-reply"></i>
                        Back
                    </a>
                </div>
                <div class="mt-5">
                    <h5 class="fw-normal text-success m-0 p-0"><b>Invoice by</b></h5>
                    <p class=""> {{ $authName ?? '' }}</p>
                </div>

            </div>
        </div>
        <div class="col-md-2">

        </div>
        <div class="footer_invoice text-center mt-4">
            <p>© 2024 <a href="https://eclipseintellitech.com/" target="_blank">Eclipse Intellitech
                    Limited.</a> All rights
                reserved. Powered by Eclipse Intellitech <a href="https://electro-pos.eclipseintellitech.com/login"
                    target="_blank">EIL
                    Electro</a> Software</p>
        </div>
    </div>
    <style>
        .table> :not(caption)>*>* {
            padding: 0px 10px !important;

        }

        .footer_invoice {
            display: none !important;

        }

        @media print {

            .table> :not(caption)>*>* {
                padding: 0px 10px !important;
                color: #000;
            }

            .footer_invoice {
                display: block !important;
                position: absolute !important;
                bottom: 0 !important;
                left: 50% !important;
                transform: translateX(-50%);
            }

            .footer_invoice p {
                font-size: 12px !important;
                color: #000;
            }

            nav,
            button,
            .footer {
                display: none !important;
            }

            .page-content {
                margin-top: 0 !important;
                padding-top: 0 !important;
                border: none !important;
                height: 100% !important;
            }

            .print_page {
                height: 100% !important;
                border: none !important;
                box-shadow: none !important;
                color: #000;
            }

            .btn_group,
            .buttona {
                display: none !important;
            }
        }
    </style>
@endsection
