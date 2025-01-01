@extends('master')
@section('title','| Brand')
@section('admin')
    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Brand</li>
        </ol>
    </nav>
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="card-title">Brand Table</h6>
                        <button class="btn btn-rounded-primary btn-sm" data-bs-toggle="modal"
                            data-bs-target="#exampleModalLongScollable"><i data-feather="plus"></i></button>
                    </div>
                    <div id="" class="table-responsive">
                        <table id="example" class="table">
                            <thead>
                                <tr>
                                    <th>SN</th>
                                    <th>Brand Name</th>

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
                    <h5 class="modal-title" id="exampleModalScrollableTitle">Add Brand</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="modal-body">
                    <form id="signupForm" class="brandForm" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="name" class="form-label">Brand Name</label>
                            <input id="defaultconfig" class="form-control brand_name" maxlength="250" name="name"
                                type="text" onkeyup="errorRemove(this);" onblur="errorRemove(this);">
                            <span class="text-danger brand_name_error"></span>
                        </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary save_brand">Save</button>
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
                    <h5 class="modal-title" id="exampleModalScrollableTitle">Edit Brand</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="modal-body">
                    <form id="signupForm" class="brandFormEdit" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="name" class="form-label">Brand Name</label>
                            <input id="defaultconfig" class="form-control edit_brand_name" maxlength="250" name="name"
                                type="text" onkeyup="errorRemove(this);" onblur="errorRemove(this);">
                            <span class="text-danger edit_brand_name_error"></span>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary update_brand">Update</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function errorRemove(element) {
            if (element.value != '') {
                $(element).siblings('span').hide();
                $(element).css('border-color', 'green');
            }
        }

        $(document).ready(function() {
            // image onload when brand edit
            // const edit_upload_img = document.querySelector('.edit_upload_img');
            // const edit_image = document.querySelector('.edit_image');
            // edit_upload_img.addEventListener('click', function(e) {
            //     e.preventDefault();
            //     edit_image.click();

            //     edit_image.addEventListener('change', function(e) {
            //         var reader = new FileReader();
            //         reader.onload = function(e) {
            //             document.querySelector('.showEditImage').src = e.target.result;
            //         }
            //         reader.readAsDataURL(this.files[0]);
            //     });
            // });

            // show error
            function showError(name, message) {
                $(name).css('border-color', 'red');
                $(name).focus();
                $(`${name}_error`).show().text(message);
            }

            // save brand
            const saveBrand = document.querySelector('.save_brand');
            saveBrand.addEventListener('click', function(e) {
                e.preventDefault();
                let formData = new FormData($('.brandForm')[0]);
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: '/brand/store',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        if (res.status == 200) {
                            $('#exampleModalLongScollable').modal('hide');
                            // formData.delete(entry[0]);
                            // alert('added successfully');
                            $('.brandForm')[0].reset();
                            brandView();
                            toastr.success(res.message);
                        } else {
                            showError('.brand_name', res.error.name);
                        }
                    }
                });
            })


            // show brand
            function brandView() {
                $.ajax({
                    url: '/brand/view',
                    method: 'GET',
                    success: function(res) {
                        // console.log(res.data);
                        const brands = res.data;
                        $('.showData').empty();
                        if ($.fn.DataTable.isDataTable('#example')) {
                            $('#example').DataTable().clear().destroy();
                        }
                        if (brands.length > 0) {
                            $.each(brands, function(index, brand) {
                                const tr = document.createElement('tr');
                                tr.innerHTML = `
                            <td>
                                ${index+1}
                            </td>
                            <td>
                                ${brand.name ?? ""}
                            </td>

                                 <td> <button id="brandButton_${brand.id}"
                                    class="btn ${brand.status === 'inactive' ? 'btn-danger' : 'btn-success'} brandButton"
                                    data-id="${brand.id}"
                                    data-status="${brand.status}">
                                ${brand.status === 'inactive' ? 'Inactive' : 'Active'}
                            </button> </td>

                            <td>
                                <a href="#" class="btn btn-primary btn-icon brand_edit" data-id=${brand.id} data-bs-toggle="modal" data-bs-target="#edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <a href="#" class="btn btn-danger btn-icon brand_delete" data-id=${brand.id}>
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
                                            Brand<i data-feather="plus"></i></button>
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
            brandView();

            // edit brand
            $(document).on('click', '.brand_edit', function(e) {
                e.preventDefault();
                // alert('ok');
                let id = this.getAttribute('data-id');
                // alert(id);
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: `/brand/edit/${id}`,
                    type: 'GET',
                    success: function(data) {
                        // console.log(data.brand.name);
                        $('.edit_brand_name').val(data.brand.name);

                        $('.update_brand').val(data.brand.id);
                        if (data.brand.description) {
                            $('.edit_description').val(data.brand.description);
                        } else {
                            $('.edit_description').val('');
                        }
                        if (data.brand.image) {
                            $('.showEditImage').attr('src',
                                'http://127.0.0.1:8000/uploads/brand/' + data.brand
                                .image);
                        } else {
                            $('.showEditImage').attr('src',
                                'http://127.0.0.1:8000/dummy/image.jpg');
                        }
                    }
                });
            })

            // update brand
            $('.update_brand').click(function(e) {
                e.preventDefault();
                // alert('ok');
                let id = $('.update_brand').val();
                // console.log(id);
                let formData = new FormData($('.brandFormEdit')[0]);
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: `/brand/update/${id}`,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        if (res.status == 200) {
                            $('#edit').modal('hide');
                            $('.brandFormEdit')[0].reset();
                            brandView();
                            toastr.success(res.message);
                        } else {
                            showError('.edit_brand_name', res.error.name);
                        }
                    }
                });
            })


            // brand Delete
            $(document).on('click', '.brand_delete', function(e) {
                e.preventDefault();
                // alert("ok")
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
                            url: `/brand/destroy/${id}`,
                            type: 'GET',
                            success: function(res) {
                                if (res.status == 200) {
                                    Swal.fire({
                                        title: "Deleted!",
                                        text: "Your file has been deleted.",
                                        icon: "success"
                                    });
                                    brandView();
                                } else {
                                    Swal.fire({
                                        position: "top-end",
                                        icon: "warning",
                                        title: "File Delete Unsuccessful",
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
                $('.showData').on('click', '.brandButton', function() {
                    var button = $(this);
                    var brandId = button.data('id');
                    $.ajax({
                        url: '/brand/status/' + brandId,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.status === 200) {
                                var newStatus = response.newStatus;
                                button
                                    .removeClass(newStatus === 'active' ? 'btn-danger' :
                                        'btn-success')
                                    .addClass(newStatus === 'active' ? 'btn-success' :
                                        'btn-danger')
                                    .text(newStatus === 'active' ? 'Active' :
                                        'Inactive');
                            } else {
                                alert('Failed to update status. Please try again.');
                            }
                        },
                        error: function() {
                            alert('Error occurred while updating status.');
                        }
                    });
                });
            });

    </script>
@endsection
