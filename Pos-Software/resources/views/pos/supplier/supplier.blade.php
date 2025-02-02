@extends('master')
@section('title', '| Supplier Page')
@section('admin')
    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Supplier</li>
        </ol>
    </nav>
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="card-title">Supplier Table</h6>
                        <button class="btn btn-rounded-primary btn-sm" data-bs-toggle="modal"
                            data-bs-target="#exampleModalLongScollable"><i data-feather="plus"></i></button>
                    </div>
                    <div id="" class="table-responsive">
                        <table id="example" class="table">
                            <thead>
                                <tr>
                                    <th>SN</th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Bussiness Name</th>
                                    <th>Due</th>
                                    <th>Supplier Type</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody class="showData">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- add Modal  --}}
    @include('pos.supplier.add-modal')



    <!-- Modal -->
    <div class="modal fade" id="edit" tabindex="-1" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalScrollableTitle">Edit Supplier</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="modal-body">
                    <form id="signupForm" class="editSupplierForm row">
                        <div class="mb-3 col-md-6">
                            <label for="name" class="form-label">Supplier Name <span
                                    class="text-danger">*</span></label>
                            <input id="defaultconfig" class="form-control edit_supplier_name" maxlength="255" name="name"
                                type="text" onkeyup="errorRemove(this);">
                            <span class="text-danger edit_name_error"></span>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="name" class="form-label">Phone Nnumber <span
                                    class="text-danger">*</span></label>
                            <input id="defaultconfig" class="form-control edit_phone" maxlength="39" name="phone"
                                type="tel" onkeyup="errorRemove(this);">
                            <span class="text-danger edit_phone_error"></span>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="name" class="form-label">Email</label>
                            <input id="defaultconfig" class="form-control edit_email" maxlength="39" name="email"
                                type="email" onkeyup="errorRemove(this);">
                            <span class="text-danger edit_email_error"></span>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="name" class="form-label">Address</label>
                            <input id="defaultconfig" class="form-control edit_address" maxlength="39" name="address"
                                type="text" onkeyup="errorRemove(this);">
                            <span class="text-danger edit_address_error"></span>
                        </div>
                        <div class="mb-3 col-md-12">
                            <label for="name" class="form-label">Bussiness Name</label>
                            <input id="defaultconfig" class="form-control edit_business_name" name="business_name"
                                type="text" onkeyup="errorRemove(this);">
                            <span class="text-danger edit_business_error"></span>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary update_supplier">Update</button>
                        </div>
                        <input type="hidden" name="supplier_type" class="edit_supplier_type" value="wholesale">
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // error remove
        // function errorRemove(element) {
        //     if (element.value != '') {
        //         $(element).siblings('span').hide();
        //         $(element).css('border-color', 'green');
        //     }
        // }


        // remove error
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


        $(document).ready(function() {
            // show error
            function showError(name, message) {
                $(name).css('border-color', 'red'); // Highlight input with red border
                $(name).focus(); // Set focus to the input field
                $(`${name}_error`).show().text(message); // Show error message
            }
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

            function supplierView() {
                $.ajax({
                    url: '/supplier/view',
                    method: 'GET',
                    success: function(res) {
                        const suppliers = res.data;
                        // Clear the table body
                        $('.showData').empty();

                        // Destroy the existing DataTable instance if it exists
                        if ($.fn.DataTable.isDataTable('#example')) {
                            $('#example').DataTable().clear().destroy();
                        }
                        // Check if suppliers data is present
                        if (suppliers.length > 0) {
                            $.each(suppliers, function(index, supplier) {
                                const tr = document.createElement('tr');
                                tr.innerHTML = `
                                        <td>${index + 1}</td>
                                        <td>
                                            <a href="/supplier/profile/${supplier.slug}" >
                                                ${supplier.name ?? ""}
                                            </a>
                                        </td>
                                        <td>${supplier.phone ?? ""}</td>
                                        <td>
                                            ${supplier.business_name ?? ""}
                                        </td>
                                        <td>
                                            <span>
                                                ${supplier.due_balance > 0 ? `সাপ্লায়ার আপানার <br/> থেকে পাবেন <br/> ${supplier.due_balance}` : ''}
                                                ${supplier.due_balance < 0 ? ` ${-(supplier.due_balance)} <br/> আপনি সাপ্লায়ার <br/> থেকে পাবেন` : ''}
                                                ${supplier.due_balance == 0 ? `${supplier.due_balance}` : ''}
                                            </span>
                                        </td>
                                        <td>
                                           <span class="text-capitalize">
                                            ${supplier.supplier_type ?? ""}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="#" class="btn btn-primary btn-icon supplier_edit" data-id="${supplier.id}" data-bs-toggle="modal" data-bs-target="#edit">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </a>
                                            <a href="#" class="btn btn-danger btn-icon supplier_delete" data-id="${supplier.id}">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </a>
                                        </td>
                                    `;
                                $('.showData').append(tr);
                            });
                        } else {
                            $('.showData').html(`
                                    <tr>
                                        <td colspan='8'>
                                            <div class="text-center text-warning mb-2">Data Not Found</div>
                                            <div class="text-center">
                                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModalLongScollable">Add Supplier<i data-feather="plus"></i></button>
                                            </div>
                                        </td>
                                    </tr>`);
                        }
                        // Reinitialize DataTable
                        $('#example').DataTable({
                            columnDefs: [{
                                "defaultContent": "-",
                                "targets": "_all"
                            }],
                            dom: 'Bfrtip',
                            buttons: [{
                                    extend: 'excelHtml5',
                                    text: 'Excel',
                                    exportOptions: {
                                        header: true,
                                        columns: ':visible'
                                    },
                                    customize: function(xlsx) {
                                        return '{{ $header ?? '' }}\n {{ $phone ?? '+880.....' }}\n {{ $email ?? '' }}\n{{ $address ?? '' }}\n\n' +
                                            xlsx + '\n\n';
                                    }
                                },
                                {
                                    extend: 'pdfHtml5',
                                    text: 'PDF',
                                    exportOptions: {
                                        header: true,
                                        columns: ':visible'
                                    },
                                    customize: function(doc) {
                                        doc.content.unshift({
                                            text: '{{ $header ?? '' }}\n {{ $phone ?? '+880.....' }}\n {{ $email ?? '' }}\n{{ $address ?? '' }}',
                                            fontSize: 14,
                                            alignment: 'center',
                                            margin: [0, 0, 0, 12]
                                        });
                                        doc.content.push({
                                            text: 'Thank you for using our service!',
                                            fontSize: 14,
                                            alignment: 'center',
                                            margin: [0, 12, 0, 0]
                                        });
                                        return doc;
                                    }
                                },
                                {
                                    extend: 'print',
                                    text: 'Print',
                                    exportOptions: {
                                        header: true,
                                        columns: ':visible'
                                    },
                                    customize: function(win) {
                                        $(win.document.body).prepend(
                                            '<h4>{{ $header }}</br>{{ $phone ?? '+880....' }}</br>Email:{{ $email }}</br>Address:{{ $address }}</h4>'
                                        );
                                        $(win.document.body).find('h1')
                                            .hide(); // Hide the title element
                                    }
                                }
                            ]
                        });
                    }
                });
            }
            supplierView();

            // edit Unit
            $(document).on('click', '.supplier_edit', function(e) {
                e.preventDefault();
                // console.log('0k');
                let id = this.getAttribute('data-id');
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: `/supplier/edit/${id}`,
                    type: 'GET',
                    success: function(res) {
                        if (res.status == 200) {
                            $('.edit_supplier_name').val(res.supplier.name);
                            $('.edit_email').val(res.supplier.email);
                            $('.edit_phone').val(res.supplier.phone);
                            $('.edit_address').val(res.supplier.address);
                            $('.edit_email').val(res.supplier.email);
                            $('.edit_business_name').val(res.supplier.business_name);
                            $('.edit_supplier_type').val(res.supplier.supplier_type);
                            $('.update_supplier').val(res.supplier.id);
                        } else if (res.status == 404) {
                            toastr.warning(res.message);
                        } else {
                            toastr.error(res.message);
                        }
                    }
                });
            })

            // update supplier
            $('.update_supplier').click(function(e) {
                e.preventDefault();
                // alert('ok');
                let id = $(this).val();
                // console.log(id);
                let formData = new FormData($('.editSupplierForm')[0]);
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: `/supplier/update/${id}`,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        if (res.status == 200) {
                            $('#edit').modal('hide');
                            $('.editSupplierForm')[0].reset();
                            supplierView();
                            toastr.success(res.message);
                        } else if (res.status == 400) {
                            if (res.error.name) {
                                showError('.edit_supplier_name', res.error.name);
                            }
                            if (res.error.phone) {
                                showError('.edit_phone', res.error.phone);
                            }
                            if (res.error.email) {
                                showError('.edit_email', res.error.email);
                            }
                            if (res.error.address) {
                                showError('.edit_address', res.error.address);
                            }
                            if (res.error.business_name) {
                                showError('.edit_business_name', res.error.business_name);
                            }
                        } else {
                            toastr.error(res.message);
                        }
                    }
                });
            })

            // supplier Delete
            $(document).on('click', '.supplier_delete', function(e) {
                // $('.supplier_delete').click(function(e) {
                e.preventDefault();
                let id = this.getAttribute('data-id');

                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to Delete this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });
                        $.ajax({
                            url: `/supplier/destroy/${id}`,
                            type: 'GET',
                            success: function(data) {
                                if (data.status == 200) {
                                    Swal.fire({
                                        title: "Deleted!",
                                        text: "Your file has been deleted.",
                                        icon: "success"
                                    });
                                    supplierView();
                                } else {
                                    Swal.fire({
                                        position: "top-end",
                                        icon: "warning",
                                        title: "Deleted Unsuccessful!",
                                        showConfirmButton: false,
                                        timer: 1500
                                    });
                                }
                            }
                        });
                    }
                });
            })
        });
    </script>
@endsection
