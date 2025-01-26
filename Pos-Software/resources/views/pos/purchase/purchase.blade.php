@extends('master')
@section('title', '| Add Purchase')
@section('admin')
    <style>
        .input-small {
            width: 100px;
        }
    </style>
    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Purchase Products</li>
        </ol>
    </nav>


    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="card-title"> Purchase Products</h6>
                    </div>
                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label for="ageSelect" class="form-label">Supplier <span class="text-danger">*</span></label>
                            <div class="row align-items-center g-3">
                                <div class="col-lg-8">
                                    <select class="js-example-basic-single form-select select-supplier supplier_id w-100"
                                        data-width="100%" name="supplier_id" onchange="errorRemove(this);">
                                    </select>
                                    <span class="text-danger supplier_id_error"></span>
                                </div>
                                <div class="col-lg-4">
                                    <button class="btn btn-primary w-100" data-bs-toggle="modal"
                                        data-bs-target="#exampleModalLongScollable">
                                        Add Supplier
                                    </button>
                                </div>
                                <span class="text-danger purchase_date_error"></span>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="ageSelect" class="form-label">Products <span
                                        class="text-danger">*</span></label>
                                <select class="js-example-basic-single form-select product_select" data-width="100%"
                                    onclick="errorRemove(this);">
                                    @if ($products->count() > 0)
                                        <option selected disabled>Select Products</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->name ?? '' }}
                                                ({{ $product->variation->stocks->sum('quantity') ?? 0 }}
                                                {{ $product->purchaseUnit->name ?? '' }})
                                            </option>
                                        @endforeach
                                    @else
                                        <option selected disabled>
                                            Please Add Product</option>
                                    @endif
                                </select>
                                <span class="text-danger product_select_error"></span>
                            </div>
                        </div>

                        <div class="mb-3 col-md-6">
                            <label for="password" class="form-label">Purchase Date</label>
                            <div class="input-group flatpickr me-2 mb-2 mb-md-0" id="dashboardDate">
                                <span class="input-group-text input-group-addon bg-transparent border-primary"
                                    data-toggle><i data-feather="calendar" class="text-primary"></i></span>
                                <input type="text" name="date"
                                    class="form-control bg-transparent border-primary purchase_date"
                                    placeholder="Select date" data-input>
                            </div>
                            <span class="text-danger purchase_date_error"></span>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="ageSelect" class="form-label">Products <span class="text-danger">*</span></label>
                            <select class="js-example-basic-single form-select product_select" data-width="100%"
                                onclick="errorRemove(this);">
                                @if ($products->count() > 0)
                                    <option selected disabled>Select Products</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name ?? '' }}
                                            ({{ $product->stock ?? 0 }}
                                            {{ $product->unit->name ?? '' }})
                                        </option>
                                    @endforeach
                                @else
                                    <option selected disabled>
                                        Please Add Product</option>
                                @endif
                            </select>
                            <span class="text-danger product_select_error"></span>
                        </div>

                        <div class="mb-3 col-md-6">
                            <label for="ageSelect" class="form-label">Product Varient<span
                                    class="text-danger">*</span></label>
                            <select class="js-example-basic-single form-select select-varient varient_id" data-width="100%"
                                onchange="errorRemove(this);" name="varient_id">
                            </select>
                            <span class="text-danger varient_id_error"></span>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="formFile">Invoice File/Picture upload</label>
                            <input class="form-control document_file" name="document" type="file" id="formFile"
                                onclick="errorRemove(this);">
                            <span class="text-danger document_file_error"></span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="formFile">Invoice Number</label>
                            <input class="form-control" name="invoice" type="text">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- table  --}}
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="card-title">Purchase Table</h6>
                    </div>
                    <div class="mb-2">
                        <p class="text-danger">Sell Price must be greater than Cost Price</p>
                    </div>

                    <div id="" class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#SL</th>
                                    <th>Product</th>
                                    <th>Cost Price</th>
                                    <th>Sell Price</th>
                                    <th>Qty</th>
                                    <th>Sub Total</th>
                                    <th>
                                        <i class="fa-solid fa-trash-can"></i>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="showData">
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td>
                                        <div class="row align-items-center">
                                            <div class="col-md-4">
                                                Total :
                                            </div>
                                            <div class="col-md-8">
                                                <input type="number" class="form-control total border-0 " name="total"
                                                    readonly value="0.00" />
                                            </div>
                                        </div>
                                        <div class="row align-items-center">
                                            <div class="col-md-4">
                                                Carrying Cost :
                                            </div>
                                            <div class="col-md-8">
                                                <input type="number" class="form-control carrying_cost"
                                                    name="carrying_cost" onkeyup="calculateTotal();" value="0.00" />
                                            </div>
                                        </div>
                                        <div class="row align-items-center">
                                            <div class="col-md-4">
                                                Sub Total :
                                            </div>
                                            <div class="col-md-8">
                                                <input type="number" class="form-control grand_total border-0 "
                                                    name="sub_total" readonly value="0.00" />
                                            </div>
                                        </div>
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="my-3">
                        <button class="btn btn-primary payment_btn" data-bs-toggle="modal" data-bs-target="#paymentModal"
                            disabled><i class="fa-solid fa-money-check-dollar"></i>
                            Payment</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- supplier Add Modal  --}}
    @include('pos.supplier.add-modal')

    {{-- payement modal  --}}
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="exampleModalScrollableTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalScrollableTitle">Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="modal-body">
                    <div id="" class="table-responsive mb-3">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Paying Items :</th>
                                    <th>
                                        <span class="paying_items">0</span>
                                    </th>
                                    <th>Sub Total :</th>
                                    <th>
                                        <input type="number" name="subTotal" class="subTotal form-control border-0 "
                                            readonly value="00">
                                    </th>
                                </tr>
                                <tr>
                                    <th>Previous Due:</th>
                                    <th>
                                        (<span class="previous_due">00</span>TK)
                                    </th>
                                    <th>Grand Total:</th>
                                    <th>
                                        <input type="number" name="grand_total"
                                            class="grandTotal form-control border-0 " readonly value="00">
                                    </th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    <div class="paymentForm row">
                        <div class="mb-3 col-md-6">
                            <label for="name" class="form-label">Transaction Method <span
                                    class="text-danger">*</span></label>
                            @php
                                $payments = App\Models\Bank::get();
                            @endphp
                            <select class="form-select payment_method" data-width="100%" onclick="errorRemove(this);"
                                onblur="errorRemove(this);" name="payment_method">
                                @if ($payments->count() > 0)
                                    @foreach ($payments as $payemnt)
                                        <option value="{{ $payemnt->id }}">
                                            {{ $payemnt->name }}
                                        </option>
                                    @endforeach
                                @else
                                    <option selected disabled>Please Add Transaction</option>
                                @endif
                            </select>
                            <span class="text-danger payment_method_error"></span>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="name" class="form-label">Pay Amount <span
                                    class="text-danger">*</span></label>
                            <div class="d-flex align-items-center">
                                <input class="form-control total_payable border-end-0 rounded-0" name="total_payable"
                                    type="number" onkeyup="payFunc();" onclick="errorRemove(this);"
                                    onblur="errorRemove(this);">
                                <span class="text-danger total_payable_error"></span>
                                <button class="btn btn-info border-start-0 rounded-0 paid_btn">Paid</button>
                            </div>

                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="name" class="form-label">Due</label>
                            <input name="note" class="form-control final_due" id="" placeholder=""
                                readonly></input>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="name" class="form-label">Note</label>
                            <input name="note" class="form-control note" id=""
                                placeholder="Enter Note (Optional)" rows="3"></input>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-cart-shopping"></i>
                            Purchase</button>
                    </div>
                </div>
            </div>
        </div>
    </div>






    <script>
        // error remove
        function errorRemove(element) {
            if (element.value != '') {
                $(element).siblings('span').hide();
                $(element).css('border-color', 'green');
            }
        }
        $(document).ready(function() {
            // show error
            function showError(name, message) {
                $(name).css('border-color', 'red'); // Highlight input with red border
                $(name).focus(); // Set focus to the input field
                $(`${name}_error`).show().text(message); // Show error message
            }

            // show supplier
            function supplierView() {
                // console.log('hello')
                $.ajax({
                    url: '/supplier/view',
                    method: 'GET',
                    success: function(res) {
                        const suppliers = res.data;
                        // console.log(suppliers);
                        $('.select-supplier').empty();
                        if (suppliers.length > 0) {
                            $('.select-supplier').html(
                                `<option selected disabled>Select a Supplier</option>`);
                            $.each(suppliers, function(index, supplier) {
                                $('.select-supplier').append(
                                    `<option value="${supplier.id}">${supplier.name}</option>`
                                );
                            })
                        } else {
                            $('.select-supplier').html(`
                                    <option selected disable>Please add supplier</option>`)
                        }
                    }
                })
            }
            supplierView();

            // save supplier
            const saveSupplier = document.querySelector('.save_supplier');
            saveSupplier.addEventListener('click', function(e) {
                e.preventDefault();
                let formData = new FormData($('.supplierForm')[0]);
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: '/supplier/store',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        if (res.status == 200) {
                            $('#exampleModalLongScollable').modal('hide');
                            $('.supplierForm')[0].reset();
                            supplierView();
                            toastr.success(res.message);
                        } else if (res.status == 400) {
                            if (res.error.name) {
                                showError('.supplier_name', res.error.name);
                            }
                            if (res.error.phone) {
                                showError('.phone', res.error.phone);
                            }
                            if (res.error.email) {
                                showError('.email', res.error.email);
                            }
                            if (res.error.address) {
                                showError('.address', res.error.address);
                            }
                            if (res.error.business_name) {
                                showError('.business_name', res.error.business_name);
                            }
                            if (res.error.due_balance) {
                                showError('.due_balance', res.error.due_balance);
                            }
                        } else {
                            toastr.error(res.message);
                        }
                    }
                });
            })

        })
    </script>

@endsection
