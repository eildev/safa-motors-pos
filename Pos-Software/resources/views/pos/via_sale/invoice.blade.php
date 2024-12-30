@extends('master')
@section('title', '| Via Purchase Invoice')
@section('admin')
    <div class="row">
        <div class="col-md-12">
            <div class="card border-0 shadow-none">
                <div class="card-body ">
                    <div class="container-fluid d-flex justify-content-between">
                        <div class="col-lg-3 ps-0">
                            @if (!empty($invoice_logo_type))
                                @if ($invoice_logo_type == 'Name')
                                    <a href="#" class="noble-ui-logo logo-light d-block mt-3">{{ $siteTitle }}</a>
                                @elseif($invoice_logo_type == 'Logo')
                                    @if (!empty($logo))
                                        <img class="margin_left_m_14" height="100" width="200" src="{{ url($logo) }}"
                                            alt="logo">
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
                                <a href="#" class="noble-ui-logo logo-light d-block mt-3">EIL<span>POS</span></a>
                            @endif
                            <p class="show_branch_address w_40">{{ $address ?? 'Banasree' }}</p>
                            <p class="show_branch_address">{{ $phone ?? '' }}, 01708008705, 01720389177</p>
                            <p class="show_branch_address">{{ $email ?? '' }}</p>
                            <!--<hr>-->
                            <p class="mt-4 mb-1 show_supplier_name"><span>Supplier Info:</span>
                                <b>{{ $viaSale->supplier_name ?? '' }}</b>
                            </p>

                        </div>
                        <div class="col-lg-3 pe-0 text-end">
                            <h4 class="fw-bolder text-uppercase text-end mt-4 mb-2">invoice</h4>
                            <h6 class="text-end mb-5 pb-4">#VIA-SALE-{{ $viaSale->invoice_number ?? 0 }}</h6>

                            <p class="text-end mb-1 mt-5">Total </p>
                            <h4 class="text-end fw-normal">৳ {{ $viaSale->sub_total ?? 00.0 }}</h4>
                            <h6 class="mb-0 mt-2 text-end fw-normal"><span class="text-muted show_purchase_date">Invoice
                                    Date :</span> {{ $viaSale->invoice_date ?? '' }}</h6>
                        </div>
                    </div>

                    <div class="container-fluid mt-5 d-flex justify-content-center w-100">
                        <div class="table-responsive w-100">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Description</th>
                                        <th class="text-end">Unit cost</th>
                                        <th class="text-end">Quantity</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="text-end">
                                        <td class="text-start">1</td>
                                        <td class="text-start">{{ $viaSale->product_name }}</td>
                                        <td>{{ $viaSale->cost_price }}</td>
                                        <td>{{ $viaSale->quantity }}</td>
                                        <td>{{ $viaSale->sub_total }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="container-fluid mt-5 w-100">
                        <div class="row">
                            <div class="col-md-6 ms-auto">
                                <div class="table-responsive">
                                    <table class="table">
                                        <tbody>
                                            <tr>
                                                <td>Sub Total</td>
                                                <td class="text-end">৳ {{ number_format($viaSale->sub_total) }}</td>
                                            </tr>
                                            <tr>
                                                <td>Paid</td>
                                                <td class="text-end">৳ {{ number_format($viaSale->paid) }}</td>
                                            </tr>
                                            @if ($viaSale->due > 0)
                                                <tr>
                                                    <td>Due</td>
                                                    <td class="text-end text-danger">৳ {{ number_format($viaSale->due) }}
                                                    </td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="container-fluid w-100 btn_group">
                        {{-- <a href="javascript:;" class="btn btn-primary float-end mt-4 ms-2"><i data-feather="send"
                                class="me-3 icon-md"></i>Send Invoice</a> --}}
                        <a href="javascript:;" class="btn btn-outline-primary float-end mt-4" onclick="window.print();"><i
                                data-feather="printer" class="me-2 icon-md"></i>Print</a>
                    </div>
                    <div class="mt-5">
                        <h5 class="fw-normal text-success m-0 p-0"><b>Invoice by</b></h5>
                        <p class=""> {{ $authName ?? '' }}</p>
                    </div>

                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="w-100 mx-auto btn_group">
                <a href="{{ route('purchase.view') }}" class="btn btn-primary  mt-4 ms-2"><i
                        class="fa-solid fa-arrow-rotate-left me-2"></i>Manage Purchase</a>
                <a href="{{ route('purchase') }}" class="btn btn-outline-primary mt-4"><i data-feather="plus-circle"
                        class="me-2 icon-md"></i>Add Purchase</a>
            </div>

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
        .footer_invoice {
            display: none !important;

        }

        @media print {
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
            .footer {
                display: none !important;
            }

            .page-content {
                margin-top: 0 !important;
                padding-top: 0 !important;
            }

            .btn_group {
                display: none !important;
            }
        }
    </style>
@endsection
