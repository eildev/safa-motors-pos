@extends('master')
@section('title', '| Today Report')
@section('admin')
    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Daily Report</li>
        </ol>
    </nav>
    <div class="row">
        <div class="col-12 col-xl-12 stretch-card">
            <div class="row flex-grow-1">
                <div class="col-md-3 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-baseline">
                                <h6 class="card-title mb-0">Sale Amount</h6>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <h3 class="mb-2">
                                        ৳ {{ $todayInvoiceAmount }}
                                    </h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-baseline">
                                <h6 class="card-title mb-0">Purchase Cost</h6>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <h3 class="mb-2">
                                        ৳ {{ $today_grand_total }}
                                    </h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-baseline">
                                <h6 class="card-title mb-0">EXPENSE</h6>
                                <div class="dropdown mb-2">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <h3 class="mb-2">
                                        ৳ {{ $todayExpenseAmount }}
                                    </h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-baseline">
                                <h6 class="card-title mb-0">Salary Sheet</h6>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <h3 class="mb-2">
                                        ৳ {{ $totalSalary }}
                                    </h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">

        {{-- today sale Report  --}}
        <div class="col-md-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title text-info">Today Sale Report</h6>

                    <div id="" class="table-responsive">
                        <table id="dataTableExample" class="table">
                            <thead>
                                <tr>
                                    <th>Invoice No.</th>
                                    <th>Product Name</th>
                                    <th>Qty</th>
                                    <th>Due</th>
                                    <th>Sale Amount</th>
                                </tr>
                            </thead>
                            <tbody class="showData">
                                @if ($totalSales->count() > 0)
                                    @foreach ($totalSales as $key => $sale)
                                        <tr>
                                            <td>
                                                <a
                                                    href="{{ route('sale.invoice', $sale->id) }}">#{{ $sale->invoice_number ?? 0 }}</a>
                                            </td>
                                            <td>
                                                <ul>
                                                    @foreach ($sale->saleItem as $item)
                                                        <li>{{ $item->product->name ?? '' }}</li>
                                                    @endforeach
                                                </ul>
                                            </td>
                                            <td>
                                                {{ $sale->quantity ?? 0 }}
                                            </td>
                                            <td>
                                                {{ $sale->due ?? 0 }}
                                            </td>
                                            <td>
                                                {{ $sale->receivable ?? 0 }}
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="12">
                                            <div class="text-center text-warning mb-2">Data Not Found</div>
                                        </td>
                                    </tr>
                                @endif
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th>Qty : {{ $todayTotalSaleQty ?? 0 }}</th>
                                    <th>Total : {{ $todayTotalSaleDue ?? 0 }}Tk</th>
                                    <th>Total : {{ $todayTotalSaleAmount ?? 0 }}Tk</th>
                                </tr>
                            </tfoot>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>


        {{-- today sale cash Report  --}}
        <div class="col-md-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title text-info">Today Cash & Sales Report</h6>

                    <div id="" class="table-responsive">
                        <table id="dataTableExample1" class="table">
                            <thead>
                                <tr>
                                    <th>Invoice No.</th>
                                    <th>Particulars</th>
                                    <th>Total</th>
                                    <th>Due</th>
                                    <th>Paid</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $due = 0;
                                    $paid = 0;
                                    $total = 0;
                                @endphp
                                @if ($totalSaleCashReport->count() > 0)
                                    @foreach ($totalSaleCashReport as $key => $sale)
                                        <tr>
                                            <td>
                                                @php
                                                    $particularData = $sale->particularData();
                                                @endphp
                                                @if ($particularData)
                                                    @if ($particularData->invoice_number)
                                                        <a href="{{ route('sale.invoice', $particularData->id) }}">
                                                            #{{ $particularData->invoice_number ?? 0 }}
                                                        </a>
                                                    @else
                                                        <a
                                                            href="{{ route('return.products.invoice', $particularData->id) }}">
                                                            #{{ $particularData->return_invoice_number ?? 0 }}
                                                        </a>
                                                    @endif
                                                @else
                                                    <a href="{{ route('transaction.invoice.receipt', $sale->id) }}">
                                                        #{{ rand(000000, 999999) }}
                                                    </a>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($sale->particulars == 'Return' || $sale->particulars == 'Adjust Due Collection')
                                                    @if ($sale->particulars == 'Adjust Due Collection')
                                                        Return
                                                    @else
                                                        Cash Return
                                                    @endif
                                                @elseif (strpos($sale->particulars, 'Sale#') !== false)
                                                    Sale
                                                @elseif ($sale->particulars == 'SaleDue')
                                                    Due Collection
                                                @else
                                                    {{ $sale->particulars ?? '' }}
                                                @endif
                                            </td>
                                            <td>
                                                @if ($sale->particulars == 'Return' || $sale->particulars == 'Adjust Due Collection')
                                                    @if ($sale->particulars == 'Adjust Due Collection')
                                                        {{ number_format($sale->credit, 2) ?? 0 }}
                                                        @php
                                                            $total += $sale->credit;
                                                        @endphp
                                                    @else
                                                        {{ number_format($sale->debit, 2) ?? 0 }}
                                                        @php
                                                            $total += $sale->debit;
                                                        @endphp
                                                    @endif
                                                @elseif ($sale->particulars == 'SaleDue' || $sale->particulars == 'PurchaseDue')
                                                    {{ number_format($sale->credit, 2) ?? 0 }}
                                                    @php
                                                        $total += $sale->credit;
                                                    @endphp
                                                @else
                                                    {{ number_format($sale->debit, 2) ?? 0 }}
                                                    @php
                                                        $total += $sale->debit;
                                                    @endphp
                                                @endif
                                            </td>
                                            <td>
                                                @if ($sale->particulars == 'Return' || $sale->particulars == 'Adjust Due Collection')
                                                    00
                                                @else
                                                    @if ($sale->debit > $sale->credit)
                                                        {{ number_format($sale->debit - $sale->credit, 2) ?? 0 }}
                                                        @php
                                                            $cal = $sale->debit - $sale->credit;
                                                            $due += $sale->debit - $sale->credit;
                                                        @endphp
                                                    @else
                                                        00
                                                    @endif
                                                @endif
                                            </td>
                                            <td>
                                                @if ($sale->particulars == 'Return')
                                                    00
                                                @else
                                                    {{ number_format($sale->credit, 2) ?? 0 }}
                                                    @php
                                                        $paid += $sale->credit;
                                                    @endphp
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="12">
                                            <div class="text-center text-warning mb-2">Data Not Found</div>
                                        </td>
                                    </tr>
                                @endif
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th>{{ number_format($total, 2) ?? 0 }}</th>
                                    <th>{{ number_format($due, 2) ?? 0 }}</th>
                                    <th>{{ number_format($paid, 2) ?? 0 }}</th>
                                </tr>
                            </tfoot>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>


        {{-- today Expanse Report  --}}
        <div class="col-md-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title text-info">Expense Report</h6>

                    <div id="" class="table-responsive">
                        <table id="dataTableExample2" class="table">
                            <thead>
                                <tr>
                                    <th>SN#</th>
                                    <th>Expense Purpose</th>
                                    <th>Category</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody class="showData">
                                {{-- @dd($expense); --}}
                                @if ($expense->count() > 0)
                                    {{-- @dd($expense); --}}
                                    @php
                                        $num = 0;
                                    @endphp
                                    <?php
                                    $totalAmount = 0;
                                    ?>
                                    @foreach ($expense as $key => $expenseData)
                                        <tr>
                                            <td>{{ $num++ }}</td>
                                            <td>{{ $expenseData->purpose ?? '' }}</td>
                                            <td>{{ $expenseData['expenseCat']['name'] ?? '' }}</td>
                                            <td>{{ $expenseData->amount ?? '' }}</td>
                                            <?php $totalAmount += isset($expenseData->amount) ? $expenseData->amount : 0; ?>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="12">
                                            <div class="text-center text-warning mb-2">Data Not Found</div>
                                        </td>
                                    </tr>
                                @endif

                            </tbody>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th><strong>Total : {{ $totalAmount ?? 0 }} Tk</strong></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>


        {{-- today Purchase Report  --}}
        <div class="col-md-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title text-info">Today Purchase Report</h6>

                    <div id="" class="table-responsive">
                        <table id="dataTableExample4" class="table">
                            <thead>
                                <tr>
                                    <th>SN#</th>
                                    <th>Invoice No.</th>
                                    <th>Product Name</th>
                                    <th>Qty</th>
                                    <th>Due</th>
                                    <th>Purchase Amount</th>
                                </tr>
                            </thead>
                            <tbody class="showData">
                                @if ($purchases->count() > 0)
                                    {{-- @dd($purchases); --}}
                                    @foreach ($purchases as $key => $purchase)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>
                                                <a
                                                    href="{{ route('purchase.invoice', $purchase->id) }}">#{{ $purchase->id ?? 0 }}</a>
                                            </td>
                                            <td>
                                                <ul>
                                                    @foreach ($purchase->purchaseItem as $item)
                                                        <li>{{ $item->product->name ?? '' }}
                                                            <br>({{ $item->product->barcode ?? '' }})
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </td>
                                            <td>
                                                {{ $purchase->total_quantity ?? 0 }}
                                            </td>
                                            <td>
                                                {{ $purchase->due ?? 0 }}
                                            </td>
                                            <td>
                                                {{ $purchase->grand_total ?? 0 }}
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="12">
                                            <div class="text-center text-warning mb-2">Data Not Found</div>
                                        </td>
                                    </tr>
                                @endif
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th>Qty : {{ $todayTotalPurchaseQty ?? 0 }}</th>
                                    <th>Total : {{ $todayTotalPurchaseDue ?? 0 }}Tk</th>
                                    <th>Total : {{ $todayTotalPurchaseAmount ?? 0 }}Tk</th>
                                </tr>
                            </tfoot>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>


        {{-- today Expanse Report  --}}
        <div class="col-md-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title text-info">Today Salary Report</h6>

                    <div id="" class="table-responsive">
                        <table id="dataTableExample3" class="table">
                            <thead>
                                <tr>
                                    <th>SN#</th>
                                    <th>Employee Name</th>
                                    <th>Amount</th>
                                    <th>Due</th>
                                </tr>
                            </thead>
                            <tbody>

                                @if ($salary->count() > 0)
                                    @foreach ($salary as $key => $data)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $data->emplyee->full_name ?? '' }}</td>
                                            <td>{{ $data->debit ?? 0 }}</td>
                                            <td>{{ $data->balance ?? 0 }}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="12">
                                            <div class="text-center text-warning mb-2">Data Not Found</div>
                                        </td>
                                    </tr>
                                @endif

                            </tbody>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th><strong>Total Amount : {{ $totalSalary ?? 0 }} Tk</strong></th>
                                    <th><strong>Total Due : {{ $totalSalaryDue ?? 0 }} Tk</strong></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection
