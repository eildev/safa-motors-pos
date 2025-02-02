@extends('master')
@section('title', '| Unit')
@section('admin')
    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Unit</li>
        </ol>
    </nav>
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="card-title">Unit Table</h6>
                        <button class="btn btn-rounded-primary btn-sm" data-bs-toggle="modal"
                            data-bs-target="#exampleModalLongScollable"><i data-feather="plus"></i></button>
                    </div>
                    <div id="" class="table-responsive">
                        <table id="example" class="table">
                            <thead>
                                <tr>
                                    <th>SN</th>
                                    <th>Unit Name</th>
                                    <th>Status</th>
                                    <th>Action</th>
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


    <!-- Modal -->
    <div class="modal fade" id="exampleModalLongScollable" tabindex="-1" aria-labelledby="exampleModalScrollableTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalScrollableTitle">Add Unit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="modal-body">
                    <form id="signupForm" class="unitForm row">
                        <div class="mb-3 col-md-12">
                            <label for="name" class="form-label">Unit Name</label>
                            <input id="defaultconfig" class="form-control unit_name" maxlength="39" name="name"
                                type="text" onkeyup="errorRemove(this);" onblur="errorRemove(this);">
                            <span class="text-danger unit_name_error"></span>
                        </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary save_unit">Save</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="edit" tabindex="-1" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalScrollableTitle">Edit Unit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="modal-body">
                    <form id="signupForm" class="editUnitForm">

                        <div class="mb-3">
                            <label for="name" class="form-label">Unit Name</label>
                            <input id="defaultconfig" class="form-control edit_unit_name" maxlength="39" name="name"
                                type="text" onkeyup="errorRemove(this);" onblur="errorRemove(this);">
                            <span class="text-danger edit_unit_name_error"></span>
                        </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary update_unit">Update</button>
                </div>
                </form>
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
            // save unit
            const saveUnit = document.querySelector('.save_unit');
            saveUnit.addEventListener('click', function(e) {
                e.preventDefault();
                let formData = new FormData($('.unitForm')[0]);
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: '/unit/store',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        if (res.status == 200) {
                            $('#exampleModalLongScollable').modal('hide');
                            $('.unitForm')[0].reset();
                            unitView();
                            toastr.success(res.message);
                        } else {
                            if (res.error.name) {
                                showError('.unit_name', res.error.name);
                            }

                        }
                    }
                });
            })


            // show Unit
            function unitView() {
                $.ajax({
                    url: '/unit/view',
                    method: 'GET',
                    success: function(res) {
                        const units = res.data;
                        $('.showData').empty();
                        if ($.fn.DataTable.isDataTable('#example')) {
                            $('#example').DataTable().clear().destroy();
                        }
                        if (units.length > 0) {
                            $.each(units, function(index, unit) {
                                const tr = document.createElement('tr');
                                const statusClass = unit.status === 'inactive' ? 'btn-danger' :
                                    'btn-success';
                                const statusText = unit.status === 'inactive' ? 'Inactive' :
                                    'Active';
                                tr.innerHTML = `
                            <td>
                                ${index+1}
                            </td>
                            <td>
                                ${unit.name ?? ""}
                            </td>
                             <td>
                             <button id="unitButton_${unit.id}"
                                class="btn ${statusClass} unitButton"
                                data-id="${unit.id}"
                                data-status="${unit.status}">
                            ${statusText}
                           </button>
                          </td>
                            <td>
                                <a href="#" class="btn btn-primary btn-icon unit_edit" data-id=${unit.id} data-bs-toggle="modal" data-bs-target="#edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <a href="#" class="btn btn-danger btn-icon unit_delete" data-id=${unit.id}>
                                    <i class="fa-solid fa-trash-can"></i>
                                </a>
                            </td>
                            `;
                                $('.showData').append(tr);
                            })
                        } else {
                            $('.showData').html(`
                            <tr>
                                <td colspan='8'>
                                    <div class="text-center text-warning mb-2">Data Not Found</div>
                                    <div class="text-center">
                                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModalLongScollable">Add
                                            Unit<i data-feather="plus"></i></button>
                                    </div>
                                </td>
                            </tr>`)
                        }
                        $('#example').DataTable({
                            columnDefs: [{
                                "defaultContent": "-",
                                "targets": "_all"
                            }],
                            dom: 'Bfrtip',
                        });

                    }
                })
            }
            unitView();

            // edit Unit
            $(document).on('click', '.unit_edit', function(e) {
                e.preventDefault();
                // console.log('0k');
                let id = this.getAttribute('data-id');

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: `/unit/edit/${id}`,
                    type: 'GET',
                    success: function(res) {
                        if (res.status == 200) {
                            $('.edit_unit_name').val(res.unit.name);
                            $('.update_unit').val(res.unit.id);
                        } else {
                            toastr.warning("No Data Found");
                        }
                    }
                });
            })

            // update unit
            $('.update_unit').click(function(e) {
                e.preventDefault();
                // alert('ok');
                let id = $(this).val();
                // console.log(id);
                let formData = new FormData($('.editUnitForm')[0]);
                console.log("ID: ", id);
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: `/unit/update/${id}`,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        if (res.status == 200) {
                            $('#edit').modal('hide');
                            $('.editUnitForm')[0].reset();
                            unitView();
                            toastr.success(res.message);
                        } else {
                            if (res.error.name) {
                                showError('.edit_unit_name', res.error.name);
                            }

                        }
                    }
                });
            })

            // unit Delete
            $(document).on('click', '.unit_delete', function(e) {
                // $('.unit_delete').click(function(e) {
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
                            url: `/unit/destroy/${id}`,
                            type: 'GET',
                            success: function(data) {
                                if (data.status == 200) {
                                    Swal.fire({
                                        title: "Deleted!",
                                        text: "Your file has been deleted.",
                                        icon: "success"
                                    });
                                    unitView();
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
        $(document).ready(function() {
            $('.showData').on('click', '.unitButton', function() {
                var unitId = $(this).data('id');
                // alert(categoryId);
                $.ajax({
                    url: '/unit/status/' + unitId,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.status == 200) {
                            // var button = $('#categoryButton_' + categoryId);
                            if (response.status == 200) {
                                var button = $('#unitButton_' +
                                    unitId);
                                if (response.newStatus == 'active') {
                                    button.removeClass('btn-danger').addClass(
                                        'btn-success').text('Active');
                                } else {
                                    button.removeClass('btn-success').addClass(
                                        'btn-danger').text('Inactive');
                                }
                            } else {
                                button.removeClass('btn-success').addClass(
                                    'btn-danger').text(
                                    'Inactive');
                            }
                        }
                    }
                });
            });
        });
    </script>
@endsection
