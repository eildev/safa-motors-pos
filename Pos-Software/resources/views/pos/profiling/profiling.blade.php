@extends('master')
@section('title')
    | {{ $data->name }}
@endsection
@section('admin')
    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">
                @if ($isCustomer)
                    Customer
                @else
                    Supplier
                @endif Profile
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
                            @if ($data->wallet_balance > 0)
                                <button type="button"
                                    class="btn btn-outline-primary btn-icon-text float-left add_money_modal"
                                    id="payment-btn" data-bs-toggle="modal" data-bs-target="#duePayment">
                                    <i class="btn-icon-prepend" data-feather="credit-card"></i>
                                    Payment
                                </button>
                            @endif
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
                                                <td>Account Of</td>
                                                <td>{{ $data->name ?? '' }}</td>
                                                <td>Address</td>
                                                <td>{{ $data->address ?? '' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Contact No.</td>
                                                <td>{{ $data->phone ?? '' }}</td>
                                                <td>Email</td>
                                                <td>{{ $data->email ?? '' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Total Receivable</td>
                                                <td>{{ $data->total_receivable ?? '' }}</td>
                                                <td>Total Paid</td>
                                                <td>{{ $data->total_payable ?? '' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Total Due</td>
                                                <td>{{ $data->wallet_balance ?? '' }}</td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <h4 class="my-3 text-center">
                        @if ($isCustomer)
                            Customer
                        @else
                            Supplier
                        @endif Ledger
                    </h4>
                    <div class="container-fluid w-100">
                        <div class="row">
                            <!-- //First col Start -->

                            <div class="col-md-12">

                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th class="id">Invoice</th>
                                                <th>Particulars</th>
                                                <th>Total</th>
                                                <th>Due</th>
                                                <th>Paid</th>
                                                <th>Balance</th>
                                                <th class="id">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            @if ($transactions->count() > 0)
                                                @php
                                                    $totalDebit = 0;
                                                    $totalCredit = 0;
                                                    $totalBalance = 0;
                                                @endphp

                                                @foreach ($transactions as $transaction)
                                                    <tr>
                                                        <td>{{ \Carbon\Carbon::parse($transaction->date)->format('F j, Y') ?? '' }}
                                                        </td>
                                                        <td class="id">
                                                            @php
                                                                $particularData = $transaction->particularData();
                                                            @endphp
                                                            @if ($particularData)
                                                                @if ($isCustomer)
                                                                    @if ($particularData->invoice_number)
                                                                        <a
                                                                            href="{{ route('sale.invoice', $particularData->id) }}">
                                                                            #{{ $particularData->invoice_number ?? 0 }}
                                                                        </a>
                                                                    @else
                                                                        <a
                                                                            href="{{ route('return.products.invoice', $particularData->id) }}">
                                                                            #{{ $particularData->return_invoice_number ?? 0 }}
                                                                        </a>
                                                                    @endif
                                                                @else
                                                                    <a
                                                                        href="{{ route('purchase.invoice', $particularData->id) }}">
                                                                        #{{ $particularData->invoice ?? 0 }}
                                                                    </a>
                                                                @endif
                                                            @else
                                                                <a
                                                                    href="{{ route('transaction.invoice.receipt', $transaction->id) }}">
                                                                    #{{ rand(000000, 999999) }}
                                                                </a>
                                                            @endif
                                                        </td>
                                                        <td>
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
                                                        </td>
                                                        <td>
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
                                                        </td>
                                                        <td>
                                                            @if ($transaction->particulars == 'Return' || $transaction->particulars == 'Adjust Due Collection')
                                                                00
                                                            @else
                                                                @if ($transaction->debit > $transaction->credit)
                                                                    {{ number_format($transaction->debit - $transaction->credit, 2) ?? 0 }}
                                                                @else
                                                                    00
                                                                @endif
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
                                                            @if ($transaction->particulars == 'Return')
                                                                00
                                                            @else
                                                                @if ($transaction->balance > 0)
                                                                    <span class="text-danger">
                                                                        {{ number_format(-$transaction->balance, 2) ?? 0 }}</span>
                                                                @else
                                                                    <span>
                                                                        {{ number_format(-$transaction->balance, 2) ?? 0 }}
                                                                    </span>
                                                                @endif
                                                                @php
                                                                    $totalBalance += $transaction->balance ?? 0;
                                                                    $totalDebit += $transaction->debit ?? 0;
                                                                    $totalCredit += $transaction->credit ?? 0;
                                                                @endphp
                                                            @endif
                                                        </td>
                                                        <td class="id">
                                                            @if ($particularData)
                                                                @if ($isCustomer)
                                                                    @if ($particularData->invoice_number)
                                                                        <a href="#"
                                                                            class="btn-sm btn-outline-primary float-end print"
                                                                            data-id="{{ $particularData->id }}"
                                                                            type="sale">
                                                                            <i data-feather="printer"
                                                                                class="me-2 icon-md"></i>
                                                                        </a>
                                                                    @else
                                                                        <a href="#"
                                                                            class="btn-sm btn-outline-primary float-end print"
                                                                            data-id="{{ $particularData->id }}"
                                                                            type="return">
                                                                            <i data-feather="printer"
                                                                                class="me-2 icon-md"></i>
                                                                        </a>
                                                                    @endif
                                                                @else
                                                                    <a href="#"
                                                                        class="btn-sm btn-outline-primary float-end print"
                                                                        data-id="{{ $particularData->id }}"
                                                                        type="purchase">
                                                                        <i data-feather="printer" class="me-2 icon-md"></i>
                                                                    </a>
                                                                @endif
                                                            @else
                                                                {{-- @dd($transaction->id) --}}
                                                                <a href="#"
                                                                    class="btn-sm btn-outline-primary float-end print"
                                                                    data-id="{{ $transaction->id }}" type="transaction">
                                                                    <i data-feather="printer" class="me-2 icon-md"></i>
                                                                </a>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                <tr>
                                                    <td class="id"></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td>Total</td>
                                                    <td> {{ number_format($totalDebit, 2) }}</td>
                                                    <td> {{ number_format($totalCredit, 2) }}</td>
                                                    <td>
                                                        @if ($totalBalance > 0)
                                                            <span
                                                                class="text-danger">{{ number_format(-$totalBalance, 2) }}</span>
                                                        @else
                                                            <span>{{ number_format(-$totalBalance, 2) }}</span>
                                                        @endif
                                                    </td>
                                                    <td></td>
                                                </tr>
                                            @else
                                                <tr>
                                                    <td colspan="7" class="text-center">No Data Found</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- //End Col -->
                            @if (!$isCustomer)
                                <h4 style="text-align: center ;margin:10px">Via Sale</h4>
                                <div class="col-md-12 mt-2">

                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Invoice Date</th>
                                                    <th>Product Name</th>
                                                    <th>Quantity</th>
                                                    <th>Cost Price</th>
                                                    <th>Sale Price</th>
                                                    <th>Sub total</th>
                                                    <th>Paid</th>
                                                    <th>Due</th>
                                                    <th>Payment</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                                @if ($viaSales->count() > 0)
                                                    @php
                                                        $totalDebit = 0;
                                                        $totalCredit = 0;
                                                        $totalBalance = 0;
                                                    @endphp

                                                    @foreach ($viaSales as $viaSale)
                                                        <tr>
                                                            <td>{{ \Carbon\Carbon::parse($viaSale->invoice_date)->format('F j, Y') ?? '' }}
                                                            </td>

                                                            <td>
                                                                {{ $viaSale->viaProduct->product_name ?? ($viaSale->product_name ?? '') }}
                                                            </td>
                                                            <td>
                                                                {{ $viaSale->quantity ?? 00 }}
                                                            </td>
                                                            <td>
                                                                {{ $viaSale->cost_price ?? 00 }}
                                                            </td>
                                                            <td>
                                                                {{ $viaSale->sale_price ?? 00 }}
                                                            </td>
                                                            <td>
                                                                {{ $viaSale->sub_total ?? 00 }}
                                                            </td>
                                                            <td>
                                                                {{ $viaSale->paid ?? 00 }}
                                                            </td>
                                                            <td>
                                                                {{ $viaSale->due ?? 00 }}
                                                            </td>
                                                            <td>
                                                                @if ($viaSale->due > 0)
                                                                    <a class="dropdown-item via_sale_add_payment"
                                                                        href="#" data-id="{{ $viaSale->id }}"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#viaSalePaymentModal"><i
                                                                            class="fa-solid fa-credit-card me-2"></i>
                                                                        Payment</a>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td colspan="7" class="text-center">No Data Found</td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif
                            {{-- //End Col --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Via Sale Payment Start -->

    <div class="modal fade" id="viaSalePaymentModal" tabindex="-1" aria-labelledby="exampleModalScrollableTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalScrollableTitle">Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="modal-body">
                    <form id="signupForm" class="paymentForm row">
                        <div class="mb-3 col-md-12">
                            <label for="name" class="form-label">Payment Date<span
                                    class="text-danger">*</span></label>
                            <div class="input-group flatpickr" id="flatpickr-date">
                                <input type="text" class="form-control from-date flatpickr-input payment_date"
                                    placeholder="Payment Date" data-input="" readonly="readonly" name="payment_date">
                                <span class="input-group-text input-group-addon" data-toggle=""><svg
                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" class="feather feather-calendar">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2">
                                        </rect>
                                        <line x1="16" y1="2" x2="16" y2="6"></line>
                                        <line x1="8" y1="2" x2="8" y2="6"></line>
                                        <line x1="3" y1="10" x2="21" y2="10"></line>
                                    </svg></span>
                            </div>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="name" class="form-label">Transaction Account<span
                                    class="text-danger">*</span></label>
                            @php
                                $payments = App\Models\Bank::all();
                            @endphp
                            <select class="form-select transaction_account" data-width="100%" name="transaction_account"
                                onclick="errorRemove(this);" onblur="errorRemove(this);">
                                @if ($payments->count() > 0)
                                    @foreach ($payments as $payment)
                                        <option value="{{ $payment->id }}">{{ $payment->name }}</option>
                                    @endforeach
                                @else
                                    <option selected disabled>Please Add Payment</option>
                                @endif
                            </select>
                            <span class="text-danger transaction_account_error"></span>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="name" class="form-label">Amount<span class="text-danger">*</span></label>
                            <input id="defaultconfig" class="form-control amount" maxlength="39" name="amount"
                                type="number" onkeyup="errorRemove(this);" onblur="errorRemove(this);">
                            <span class="text-danger amount_error"></span>
                        </div>
                        <div class="mb-3 col-md-12">
                            <label for="name" class="form-label">Note</label>
                            <textarea name="note" class="form-control note" id="" placeholder="Enter Note (Optional)"
                                rows="3"></textarea>
                        </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary via_sale_save_payment">Payment</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Via Sale Payment End -->


    <iframe id="printFrame" src="" width="0" height="0"></iframe>
    <!-- Modal add Payment -->
    <div class="modal fade" id="duePayment" tabindex="-1" aria-labelledby="exampleModalScrollableTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalScrollableTitle">Due Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="modal-body">
                    <form id="addPaymentForm" class="addPaymentForm row" method="POST">
                        <input type="hidden" name="data_id" id="data_id" value="{{ $data->id }}">
                        <input type="hidden" name="isCustomer" value="{{ $isCustomer }}">
                        <input type="hidden" name="due_amount" value="{{ $data->wallet_balance }}">
                        <div>
                            <label for="name" class="form-label">Due Amount : <span id="due-amount">
                                    {{ number_format($data->wallet_balance, 2) }}</span> ৳ </label> <br>
                            <label for="remaining" class="form-label">Remaining Due:
                                <span class="text-danger" id="remaining-due">
                                    {{ number_format($data->wallet_balance, 2) }} </span>৳
                            </label>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="name" class="form-label">Balance Amount <span
                                    class="text-danger">*</span></label>
                            <input type="number" class="form-control add_amount payment_balance" name="payment_balance"
                                onkeyup="dueShow()" onkeydown="errorRemove(this);">
                            <span class="text-danger payment_balance_error"></span>
                        </div>

                        <div class="mb-3 col-md-6">
                            <label for="name" class="form-label">Transaction Account <span
                                    class="text-danger">*</span></label>
                            <select class="form-control account" name="account" id=""
                                onchange="errorRemove(this);">
                                @foreach ($banks as $bank)
                                    <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                                @endforeach
                            </select>
                            <span class="text-danger account_error"></span>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <a type="button" class="btn btn-primary" id="add_payment">Payment</a>
                </div>
                </form>
            </div>
        </div>
    </div>
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

        // due Show
        function dueShow() {
            let dueAmountText = document.getElementById('due-amount').innerText.trim();
            let dueAmount = parseFloat(dueAmountText.replace(/[^\d.-]/g, ''));

            let paymentBalanceText = document.querySelector('.payment_balance').value.trim();
            let paymentBalance = parseFloat(paymentBalanceText)

            let remainingDue = dueAmount - (paymentBalance || 0);
            document.getElementById('remaining-due').innerText = remainingDue.toFixed(2) ?? 0 + ' ৳';

        }


        const savePayment = document.getElementById('add_payment');
        savePayment.addEventListener('click', function(e) {
            // console.log('Working on payment')
            e.preventDefault();

            let formData = new FormData($('.addPaymentForm')[0]);
            // CSRF Token setup
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // AJAX request
            $.ajax({
                url: '/due/invoice/payment/transaction',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    console.log(res);
                    if (res.status == 200) {
                        // Hide the correct modal
                        $('#duePayment').modal('hide');
                        // Reset the form
                        $('.addPaymentForm')[0].reset();
                        toastr.success(res.message);
                        window.location.reload();
                    } else if (res.status == 400) {
                        showError('.account', res.message);
                    } else {
                        // console.log(res);
                        if (res.error.payment_balance) {
                            showError('.payment_balance', res.error.payment_balance);
                        }
                        if (res.error.account) {
                            showError('.account', res.error.account);
                        }
                    }
                },
                error: function(err) {
                    toastr.error('An error occurred, Empty Feild Required.');
                }
            });
        });


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

        //Via Sale Add Payment
        $(document).ready(function() {


            $(document).on('click', '.via_sale_add_payment', function(e) {
                e.preventDefault();
                let id = $(this).attr('data-id');
                // alert(id);
                var currentDate = new Date().toISOString().split('T')[0];
                $('.payment_date').val(currentDate);
                $('.via_sale_save_payment').val(id);


                $.ajax({
                    url: '/via-sale/get/' + id,
                    method: "GET",
                    success: function(res) {
                        console.log(res);
                        if (res.status == 200) {
                            console.log(res);
                            $('.amount').val(res.data.due);
                        }
                    }
                })
            });

            // save payment
            $(document).on('click', '.via_sale_save_payment', function(e) {
                e.preventDefault();
                let id = $(this).val();
                // alert(id);
                let formData = new FormData($('.paymentForm')[0]);
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: `/via-sale/payment/${id}`,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        if (res.status == 200) {
                            $('#paymentModal').modal('hide');
                            $('.paymentForm')[0].reset();
                            toastr.success(res.message);
                            window.location.reload();
                        } else if (res.status == 400) {
                            $('#paymentModal').modal('hide');
                            toastr.warning(res.message);
                        } else {
                            if (res.error.paid) {
                                showError('.amount', res.error.paid);
                            }
                            if (res.error.amount) {
                                showError('.amount', res.error.amount);
                            }
                            if (res.error.payment_method) {
                                showError('.transaction_account', res.error.payment_method);
                            }
                        }
                    }
                });
            })
        })
    </script>
@endsection
