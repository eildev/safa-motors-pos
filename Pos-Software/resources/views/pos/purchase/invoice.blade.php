@extends('master')
@section('title', '| Purchase Invoice')
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
                            <p class="show_branch_address">{{ $branch->address ?? 'accordion ' }}</p>
                            <p class="show_branch_email">{{ $branch->email ?? '' }}</p>
                            <p class="show_branch_phone">{{ $branch->phone ?? '' }}</p>

                            <p class="mt-4">Supplier Information</p>
                            <p class=" mb-1 show_supplier_name"><b>{{ $supplier->name ?? '' }}</b></p>
                            <p class="show_supplier_phone">{{ $supplier->phone ?? '' }}</p>

                        </div>
                        <div class="col-lg-3 pe-0 text-end">
                            <h4 class="fw-bolder text-uppercase text-end mt-4 mb-2">invoice</h4>
                            <h6 class="text-end mb-5 pb-4">#PURCHASE-{{ $purchase->invoice ?? 0 }}</h6>

                            <p class="text-end mb-1 mt-5">Total </p>
                            <h4 class="text-end fw-normal">৳
                                {{ number_format($purchase->grand_total + ($purchase->carrying_cost > 0 ? $purchase->carrying_cost : 0), 2, '.', ',') }}
                            </h4>
                            <h6 class="mb-0 mt-2 text-end fw-normal"><span class="text-muted show_purchase_date">Invoice
                                    Date :</span> {{ $purchase->purchase_date ?? '' }}</h6>
                        </div>
                    </div>
                    <div class="container-fluid mt-5 d-flex justify-content-center w-100">
                        <div class="table-responsive w-100">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Description</th>
                                        <th class="text-end">Quantity</th>
                                        <th class="text-end">Unit cost</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($products->count() > 0)
                                        @php $lastIndex = 0; @endphp
                                        @foreach ($products as $index => $product)
                                            <tr class="text-end">
                                                <td class="text-start">{{ $index + 1 }}</td>
                                                <td class="text-start">
                                                    <a
                                                        href="{{ route('product.ledger', $product->product->id) }}">{{ $product->product->name ?? '' }}</a>
                                                </td>
                                                <td>{{ $product->quantity }}</td>
                                                <td>{{ number_format($product->unit_price, 2) }}</td>
                                                <td>{{ number_format($product->total_price, 2) }}</td>
                                            </tr>
                                            @php $lastIndex = $index + 1; @endphp
                                        @endforeach
                                        @for ($i = $lastIndex + 1; $i < 10; $i++)
                                            <tr class="text-end">
                                                <td class="text-start">{{ $i }}</td>
                                                <td class="text-start"></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                        @endfor
                                    @else
                                        <tr>
                                            <td colspan="5">
                                                No Data Found
                                            </td>
                                        </tr>
                                    @endif
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
                                                <td class="text-end">৳ {{ number_format($purchase->total_amount, 2) }}</td>
                                            </tr>
                                            <tr>
                                                <td>Previous Due</td>
                                                <td class="text-end">৳
                                                    {{ number_format(max(0, $purchase->grand_total - $purchase->total_amount), 2) }}
                                                </td>
                                            </tr>

                                            @if ($purchase->carrying_cost > 0)
                                                <tr>
                                                    <td>Carrying Cost</td>
                                                    <td class="text-end">৳ {{ number_format($purchase->carrying_cost, 2) }}
                                                    </td>
                                                </tr>
                                            @endif

                                            <tr>
                                                <td class="text-bold-800">Grand Total</td>
                                                <td class="text-bold-800 text-end">৳
                                                    {{ number_format($purchase->grand_total + $purchase->carrying_cost, 2) }}
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>Payment Made</td>
                                                <td
                                                    class="text-end {{ $purchase->grand_total <= $purchase->paid ? 'text-success' : 'text-danger' }}">
                                                    {{ $purchase->grand_total <= $purchase->paid ? '৳' : '(-) ৳' }}
                                                    {{ number_format($purchase->paid + ($purchase->carrying_cost > 0 ? $purchase->carrying_cost : 0), 2) }}
                                                </td>
                                            </tr>

                                            @if ($purchase->due != 0)
                                                <tr class="bg-dark">
                                                    <td class="text-bold-800">Balance Due</td>
                                                    <td class="text-bold-800 text-end">৳
                                                        {{ number_format($purchase->due, 2) }}</td>
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
                        <p class="">{{ $authName ?? '' }}</p>
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
        <div class="footer_invoice text-center">
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

            .table> :not(caption)>*>* {
                padding: 0px 10px !important;
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
        }
    </style>
@endsection
