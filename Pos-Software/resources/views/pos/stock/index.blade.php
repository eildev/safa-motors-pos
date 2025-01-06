@extends('master')
@section('title', '| Stock Management')
@section('admin')
    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Stock Managment</li>
        </ol>
    </nav>
    <style>
        .nav-link:hover,
        .nav-link.active {
            color: #6587ff !important;
        }
    </style>

    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="example w-100">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="home-tab" data-bs-toggle="tab" href="#home" role="tab"
                            aria-controls="home" aria-selected="true">All Stock</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="profile-tab" data-bs-toggle="tab" href="#profile" role="tab"
                            aria-controls="profile" aria-selected="false">Low Stock</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="add-stock" data-bs-toggle="tab" href="#add_stock" role="tab"
                            aria-controls="add_stock" aria-selected="false">Add Stock</a>
                    </li>
                </ul>
                <div class="tab-content border border-top-0 p-3" id="myTabContent">
                    <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    @include('pos.stock.report')
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                        <div class="card">
                            <div class="card-body">
                                {{-- @include('all_modules.bank.cash') --}}
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="add_stock" role="tabpanel" aria-labelledby="add-stock">
                        <div class="card">
                            <div class="card-body">
                                {{-- @include('all_modules.bank.cash') --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Add Bank Account Modal -->
    <div class="modal fade" id="stock_add_modal" tabindex="-1" aria-labelledby="exampleModalScrollableTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalScrollableTitle">Add Stock Account Info</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="modal-body">
                    <form id="signupForm" class="bankForm row">
                        <div class="mb-3 col-md-6">
                            <label for="name" class="form-label">Bank Name <span class="text-danger">*</span></label>
                            <input class="form-control bank_name" name="bank_name" type="text"
                                onkeyup="errorRemove(this);">
                            <span class="text-danger bank_name_error"></span>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="name" class="form-label">Account Number <span
                                    class="text-danger">*</span></label>
                            <input class="form-control account_number" name="account_number" type="number"
                                onkeyup="errorRemove(this);">
                            <span class="text-danger account_number_error"></span>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="name" class="form-label">Account Name </label>
                            <input class="form-control account_name" name="account_name" type="text"
                                onkeyup="errorRemove(this);">
                            <span class="text-danger account_name_error"></span>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="name" class="form-label">Bank Branch Name</label>
                            <input class="form-control bank_branch_name" name="bank_branch_name" type="text"
                                onkeyup="errorRemove(this);">
                            <span class="text-danger bank_branch_name_error"></span>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="name" class="form-label">Initial Balance</label>
                            <input class="form-control initial_balance" name="initial_balance" type="number"
                                onkeyup="errorRemove(this);" value="00.00">
                            <span class="text-danger initial_balance_error"></span>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="name" class="form-label">Select Currency</label>
                            <select class="form-control currency_code" name="currency_code" onkeyup="errorRemove(this);">
                                <option value="bdt">BDT</option>
                                <option value="usd">USD</option>
                                <option value="pkr">PKR</option>
                                <option value="inr">INR</option>
                            </select>
                            <span class="text-danger currency_code_error"></span>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary modal_close" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary save_bank">Save</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Add Cash Modal -->
    <div class="modal fade" id="cash_modal" tabindex="-1" aria-labelledby="exampleModalScrollableTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalScrollableTitle">Add Cash Info</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="modal-body">
                    <form id="signupForm" class="cashForm row">
                        <div class="mb-3 col-md-6">
                            <label for="name" class="form-label">Cash Account Name <span
                                    class="text-danger">*</span></label>
                            <input class="form-control cash_account_name" name="cash_account_name" type="text"
                                onkeyup="errorRemove(this);">
                            <span class="text-danger cash_account_name_error"></span>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="name" class="form-label">Opening Balance<span
                                    class="text-danger">*</span></label>
                            <input class="form-control opening_balance" name="opening_balance" type="number"
                                onkeyup="errorRemove(this);">
                            <span class="text-danger opening_balance_error"></span>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary modal_close" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary save_cash">Save</button>
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


            // save bank account information
            const saveBank = document.querySelector('.save_bank');
            saveBank.addEventListener('click', function(e) {
                e.preventDefault();
                let formData = new FormData($('.bankForm')[0]);
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: '/bank/store',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        // console.log(res);
                        if (res.status == 200) {
                            $('#exampleModalLongScollable').modal('hide');
                            $('.bankForm')[0].reset();
                            bankView();
                            toastr.success(res.message);
                        } else {
                            if (res.error.account_name) {
                                showError('.account_name', res.error.account_name);
                            }
                            if (res.error.account_number) {
                                showError('.account_number', res.error.account_number);
                            }
                            if (res.error.bank_name) {
                                showError('.bank_name', res.error.bank_name);
                            }
                            if (res.error.bank_branch_name) {
                                showError('.bank_branch_name', res.error.bank_branch_name);
                            }
                            if (res.error.initial_balance) {
                                showError('.initial_balance', res.error.initial_balance);
                            }
                            if (res.error.currency_code) {
                                showError('.currency_code', res.error.currency_code);
                            }
                        }
                    }
                });
            })

            // bankInfo View Function
            function bankView() {
                // console.log('hello');
                $.ajax({
                    url: '/bank/view',
                    method: 'GET',
                    success: function(res) {
                        const banks = res.data;
                        // console.log(banks.account_transaction);
                        $('.show_bank_data').empty();
                        if ($.fn.DataTable.isDataTable('#myTableExample')) {
                            $('#myTableExample').DataTable().clear().destroy();
                        }
                        if (banks.length > 0) {
                            $('.total_banks').text(res.total_bank);
                            $('.total_initial_balance').text(res.total_initial_balance);
                            $('.total_current_balance').text(res.total_current_balance);
                            $.each(banks, function(index, bank) {
                                // console.log(bank);
                                // Calculate the sum of account_transaction balances
                                const tr = document.createElement('tr');
                                tr.innerHTML = `
                                    <td>
                                        <a href="/bank/details/${bank.id}" >${bank.account_name ?? ""}</a>
                                    </td>
                                    <td>${bank.account_number ?? ""}</td>
                                    <td>${bank.bank_name ?? ""}</td>
                                    <td>${bank.bank_branch_name ?? 0}</td>
                                    <td>${bank.initial_balance ?? 0}</td>
                                    <td>${bank.current_balance ?? 0}</td>
                                    <td>
                                        <a href="/bank/details/${bank.id}" class="btn btn-icon btn-xs btn-primary">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        <a href="#" class="btn btn-icon btn-xs btn-success">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <a href="#" class="btn btn-icon btn-xs btn-danger">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </a>
                                    </td>
                                `;
                                $('.show_bank_data').append(tr);
                            });
                        } else {
                            $('.show_bank_data').html(`
                            <tr>
                                <td colspan='9'>
                                    <div class="text-center text-warning mb-2">Data Not Found</div>
                                    <div class="text-center">
                                        <button class="btn btn-xs btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModalLongScollable">Add Bank Info<i data-feather="plus"></i></button>
                                    </div>
                                </td>
                            </tr>
                            `);
                        }
                        // Initialize DataTables after table data is populated
                        dynamicDataTableFunc('myTableExample');
                    }
                });
            }
            bankView();

            // save cash information
            const saveCash = document.querySelector('.save_cash');
            saveCash.addEventListener('click', function(e) {
                e.preventDefault();
                let formData = new FormData($('.cashForm')[0]);
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: '/cash-account/store',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        // console.log(res);
                        if (res.status == 200) {
                            $('#cash_modal').modal('hide');
                            $('.cashForm')[0].reset();
                            cashView();
                            toastr.success(res.message);
                        } else {
                            if (res.error.cash_account_name) {
                                showError('.cash_account_name', res.error.cash_account_name);
                            }
                            if (res.error.opening_balance) {
                                showError('.opening_balance', res.error.opening_balance);
                            }
                        }
                    }
                });
            })


            // Cash Info View Function
            function cashView() {
                $.ajax({
                    url: '/cash-account/view',
                    method: 'GET',
                    success: function(res) {
                        // console.log(res.data);
                        const banks = res.data;
                        // console.log(banks.account_transaction);
                        $('.show_cash_data').empty();
                        if ($.fn.DataTable.isDataTable('#cashTableExample')) {
                            $('#cashTableExample').DataTable().clear().destroy();
                        }
                        if (banks.length > 0) {
                            $('.total_cash').text(res.total_cash);
                            $('.total_opening_balance').text(res.total_initial_balance);
                            $('.cash_current_balance').text(res.total_current_balance);
                            $.each(banks, function(index, bank) {
                                // console.log(bank);
                                // Calculate the sum of account_transaction balances
                                const tr = document.createElement('tr');
                                tr.innerHTML = `
                                    <td>
                                        <a href="/bank/details/${bank.id}" >${bank.cash_account_name ?? ""}</a>
                                    </td>
                                    <td>${bank.opening_balance	 ?? ""}</td>
                                    <td>${bank.current_balance ?? ""}</td>
                                    <td>
                                        <a href="/cash/details/${bank.id}" class="btn btn-icon btn-xs btn-primary">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        <a href="#" class="btn btn-icon btn-xs btn-success">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <a href="#" class="btn btn-icon btn-xs btn-danger">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </a>
                                    </td>
                                `;
                                $('.show_cash_data').append(tr);
                            });
                        } else {
                            $('.show_cash_data').html(`
                            <tr>
                                <td colspan='9'>
                                    <div class="text-center text-warning mb-2">Data Not Found</div>
                                    <div class="text-center">
                                        <button class="btn btn-xs btn-primary" data-bs-toggle="modal" data-bs-target="#cash_modal">Add Cash Info<i data-feather="plus"></i></button>
                                    </div>
                                </td>
                            </tr>
                            `);
                        }

                        // Reinitialize DataTable
                        dynamicDataTableFunc('cashTableExample');
                    }
                });
            }
            cashView();
        })


        // tab active on the page reload
        document.addEventListener("DOMContentLoaded", function() {
            // Get the last active tab from localStorage
            let activeTab = localStorage.getItem('activeTab');

            // If there is an active tab stored, activate it
            if (activeTab) {
                let tabElement = document.querySelector(`a[href="${activeTab}"]`);
                if (tabElement) {
                    new bootstrap.Tab(tabElement).show();
                }
            }

            // Store the currently active tab in localStorage
            document.querySelectorAll('.nav-link').forEach(tab => {
                tab.addEventListener('shown.bs.tab', function(event) {
                    let activeTabHref = event.target.getAttribute('href');
                    localStorage.setItem('activeTab', activeTabHref);
                });
            });


            // modal not close function
            modalShowHide('exampleModalLongScollable');
            modalShowHide('cash_modal');
        });
    </script>




@endsection
