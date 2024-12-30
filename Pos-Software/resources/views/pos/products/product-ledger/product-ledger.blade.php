@extends('master')
@section('title')
    | {{ $data->name }}
@endsection
@section('admin')
    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">
                Product Ledger
            </li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card border-0 shadow-none">
                <div class="card-body">
                    <div class="container-fluid d-flex justify-content-between">
                        <div class="col-lg-3 ps-0">
                            @if (!empty($invoice_logo_type))
                                @if ($invoice_logo_type == 'Name')
                                    <a href="#" class="noble-ui-logo logo-light d-block mt-3">{{ $siteTitle }}</a>
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
                                <a href="#" class="noble-ui-logo logo-light d-block mt-3">EIL<span>Electro</span></a>
                            @endif
                            <hr>
                            <p class="show_branch_address">{{ $branch->address ?? '' }}</p>
                            <p class="show_branch_email">{{ $branch->email ?? '' }}</p>
                            <p class="show_branch_phone">{{ $branch->phone ?? '' }}</p>
                        </div>
                        <div>
                            <button type="button"
                                class="btn btn-outline-primary btn-icon-text me-2 mb-2 mb-md-0 print-btn">
                                <i class="btn-icon-prepend" data-feather="printer"></i>
                                Print
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body show_ledger">
                    <div class="container-fluid w-100">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <td>Product Name</td>
                                                <td>{{ $data->name ?? '' }}</td>
                                                <td>Branch Name</td>
                                                <td>{{ $data->branch->name ?? '' }}</td>
                                                <td>Barcode</td>
                                                <td>{{ $data->barcode ?? '' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Category</td>
                                                <td>{{ $data->category->name ?? '' }}</td>
                                                <td>Subcategory</td>
                                                <td>{{ $data->subcategory->name ?? '' }}</td>
                                                <td>Brand</td>
                                                <td>{{ $data->brand->name ?? '' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Cost Price</td>
                                                <td>{{ $data->cost ?? 0 }}</td>
                                                <td>Sale Price</td>
                                                <td>{{ $data->price ?? 0 }}</td>
                                                <td>Current Stock</td>
                                                <td>{{ $data->stock ?? 0 }} {{ $data->unit->name }}</td>
                                            </tr>
                                            <tr>
                                                <td>Total Sold</td>
                                                <td>{{ $data->total_sold ?? '' }}</td>
                                                <td>Color</td>
                                                <td>{{ $data->color ?? '' }}</td>
                                                <td>Size</td>
                                                <td>{{ $data->size->size ?? '' }}</td>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <h4 class="my-3 text-center">
                        Product Ledger
                    </h4>
                    <div class="container-fluid w-100">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Particulars</th>
                                                <th>Stock In/Out</th>
                                                <th>Balance</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            @if (count($reports))
                                                @php
                                                    $currentStock = 0;
                                                @endphp
                                                @foreach ($reports as $report)
                                                    <tr>
                                                        <td>{{ $report['date']->format('F j, Y') }}</td>
                                                        <td>{{ $report['particulars'] }}</td>
                                                        <td>
                                                            @if ($report['stockIn'] || $report['stockOut'])
                                                                @if ($report['stockIn'])
                                                                    <span>{{ $report['stockIn'] }}</span>
                                                                @else
                                                                    <span
                                                                        class="text-danger">{{ $report['stockOut'] }}</span>
                                                                @endif
                                                            @else
                                                                0
                                                            @endif
                                                        </td>
                                                        <td>{{ $report['balance'] }}</td>
                                                        {{-- @dd($report['balance']); --}}
                                                        {{-- @php
                                                            $currentStock += $report['balance'];
                                                        @endphp --}}
                                                    </tr>
                                                @endforeach
                                                {{-- <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <td>Current Stock</td>
                                                    <td>{{ $currentStock }}</td>
                                                </tr> --}}
                                            @else
                                                <tr>
                                                    <td>No Data Fount</td>
                                                </tr>
                                            @endif
                                        </tbody>

                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <iframe id="printFrame" src="" width="0" height="0"></iframe>

    <style>
        #printFrame {
            display: none;
            /* Hide the iframe */
        }

        .table> :not(caption)>*>* {
            padding: 0px 10px !important;
        }

        .margin_left_m_14 {
            margin-left: -14px;
        }

        .w_40 {
            width: 250px !important;
            text-wrap: wrap;
        }

        @media print {
            .page-content {
                margin-top: 0 !important;
                padding-top: 0 !important;
                min-height: 740px !important;
                background-color: #ffffff !important;
            }

            .grid-margin,
            .card,
            .card-body,
            table {
                background-color: #ffffff !important;
                color: #000 !important;
            }

            .footer_invoice p {
                font-size: 12px !important;
            }

            button,
            a,
            .filter_box,
            nav,
            .footer,
            .id,
            .dataTables_filter,
            .dataTables_length,
            .dataTables_info {
                display: none !important;
            }
        }
    </style>

    <script>
        // Error Remove Function 
        function errorRemove(element) {
            tag = element.tagName.toLowerCase();
            if (element.value != '') {
                // console.log('ok');
                if (tag == 'select') {
                    $(element).closest('.mb-3').find('.text-danger').hide();
                } else {
                    $(element).siblings('span').hide();
                    $(element).css('border-color', 'green');
                }
            }
        }

        // Show Error Function 
        function showError(payment_balance, message) {
            $(payment_balance).css('border-color', 'red');
            $(payment_balance).focus();
            $(`${payment_balance}_error`).show().text(message);
        }


        // print
        document.querySelector('.print-btn').addEventListener('click', function(e) {
            e.preventDefault();
            $('#dataTableExample').removeAttr('id');
            $('.table-responsive').removeAttr('class');
            // Trigger the print function
            window.print();
        });


        $('.print').click(function(e) {
            e.preventDefault();
            let id = $(this).attr('data-id');
            let type = $(this).attr('type');
            var printFrame = $('#printFrame')[0];



            if (type == 'sale') {
                var printContentUrl = '/sale/invoice/' + id;
            } else if (type == 'return') {
                var printContentUrl = '/return/products/invoice/' + id;
            } else if (type == 'purchase') {
                var printContentUrl = '/purchase/invoice/' + id;
            } else {
                var printContentUrl = '/transaction/invoice/receipt/' + id;
            }

            $('#printFrame').attr('src', printContentUrl);
            printFrame.onload = function() {
                printFrame.contentWindow.focus();
                printFrame.contentWindow.print();
            };
        })
    </script>
@endsection
