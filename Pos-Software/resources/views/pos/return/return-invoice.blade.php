@extends('master')
@section('title', '| Return View')
@section('admin')
    <div class="row ">
        <div class="col-md-12 ">
            <div class="card border-0 shadow-none invoice_bg">
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
                            <p class="mt-2 mb-1 show_supplier_name"><span>Customer Name:</span>
                                <b>{{ $customer->name ?? '' }}</b>
                            </p>
                            @if ($customer->address)
                                <p class="show_supplier_address"><span>Address:</span> {{ $customer->address ?? '' }}</p>
                            @endif
                            @if ($customer->email)
                                <p class="show_supplier_email"><span>Email:</span> {{ $customer->email ?? '' }}</p>
                            @endif
                            <p class="show_supplier_phone"><span>Phone:</span> {{ $customer->phone ?? '' }}</p>

                        </div>
                        <div class="col-lg-3 pe-0 text-end">
                            <h4 class="fw-bolder text-uppercase text-end mt-4 mb-2">invoice</h4>
                            <h6 class="text-end mb-5 pb-4"># INV-{{ $return->return_invoice_number ?? 0 }}</h6>

                            <p class="text-end mb-1 mt-5">Return</p>
                            <h4 class="text-end fw-normal">৳ {{ $return->refund_amount ?? 00.0 }}</h4>
                            <h6 class="mb-0 mt-2 text-end fw-normal"><span class="text-muted show_purchase_date">Invoice
                                    Date :</span> {{ $return->created_at ?? '' }}</h6>
                        </div>
                    </div>

                    <div class="container-fluid mt-2 d-flex justify-content-center w-100">
                        <div class="w-100">
                            <table class="table table-bordered invoice_table_bg">
                                <thead>
                                    <tr class="invoice_table_th_bg">
                                        <th>#</th>
                                        <th>Product Name</th>
                                        <th class="text-end">Unit cost</th>
                                        <th class="text-end">Quantity</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($return_items->count() > 0)
                                        @php $lastIndex = 0; @endphp
                                        @foreach ($return_items as $index => $item)
                                            <tr class="text-end">
                                                <td class="text-start">{{ $index + 1 }}</td>
                                                <td class="text-start">{{ $item->product->name ?? '' }}</td>
                                                <td>{{ $item->return_price ?? 0 }}</td>
                                                <td>{{ $item->quantity ?? 0 }}</td>
                                                <td>{{ $item->product_total ?? 0 }}</td>
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
                                        <tr class="text-center">
                                            <td>Data Not Found</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="container-fluid mt-2">
                        <div class="row">
                            <div class="col-md-6 ms-auto total_calculation">
                                <div class="table-responsive">
                                    <table class="table">
                                        <tbody class="total_calculation_bg">
                                            <tr>
                                                <td>Product Total</td>
                                                <td class="text-end">৳ {{ number_format($return->refund_amount, 2) }}</td>
                                            </tr>
                                        </tbody>

                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="w-100 mx-auto btn_group">
                <a href="{{ route('sale.view') }}" class="btn btn-primary  mt-4 ms-2"><i
                        class="fa-solid fa-arrow-rotate-left me-2"></i>Manage Sale</a>
                <a href="{{ route('sale') }}" class="btn btn-outline-primary mt-4"><i data-feather="plus-circle"
                        class="me-2 icon-md"></i>Sale</a>
            </div>
        </div>
    </div>

@endsection
