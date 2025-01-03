@extends('master')
@section('title', '| Product Category')
@section('admin')
    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Category</li>
        </ol>
    </nav>
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="card-title">Category Table</h6>
                        <button class="btn btn-rounded-primary btn-sm" data-bs-toggle="modal"
                            data-bs-target="#exampleModalLongScollable"><i data-feather="plus"></i></button>
                    </div>
                    <div class="table-responsive">
                        <table id="example" class="table">
                            <thead>
                                <tr>
                                    <th>SN</th>
                                    <th>Category Name</th>

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
                    <h5 class="modal-title" id="exampleModalScrollableTitle">Add Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="modal-body">
                    <form id="signupForm" class="categoryForm" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="name" class="form-label">Category Name</label>
                            <input id="defaultconfig" class="form-control category_name" maxlength="250" name="name"
                                type="text" onkeyup="errorRemove(this);" onblur="errorRemove(this);">
                            <span class="text-danger category_name_error"></span>
                        </div>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary save_category">Save</button>
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
                    <h5 class="modal-title" id="exampleModalScrollableTitle">Edit Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="modal-body">
                    <form id="signupForm" class="categoryFormEdit" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="name" class="form-label">Category Name</label>
                            <input id="defaultconfig" class="form-control edit_category_name" maxlength="250" name="name"
                                type="text" onkeyup="errorRemove(this);" onblur="errorRemove(this);">
                            <span class="text-danger edit_category_name_error"></span>
                        </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary update_category">Update</button>
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

            let protocol = window.location.protocol + "//";
            let host = window.location.host;
            let url = protocol + host;
            // image onload when category edit
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
                $(name).css('border-color', 'red'); // Highlight input with red border
                $(name).focus(); // Set focus to the input field
                $(`${name}_error`).show().text(message); // Show error message
            }

            // save category
            const saveCategory = document.querySelector('.save_category');
            saveCategory.addEventListener('click', function(e) {
                e.preventDefault();
                let formData = new FormData($('.categoryForm')[0]);
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: '/category/store',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        if (res.status == 200) {
                            $('#exampleModalLongScollable').modal('hide');
                            // formData.delete(entry[0]);
                            // alert('added successfully');
                            $('.categoryForm')[0].reset();
                            categoryView();
                            toastr.success(res.message);
                        } else {
                            showError('.category_name', res.error.name);
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 500) {
                            toastr.error(xhr.responseJSON.error);
                        } else {
                            toastr.error('An unexpected error occurred. Please try again.');
                        }
                    }
                });
            })


            // show category
            function categoryView() {
                $.ajax({
                    url: '/category/view',
                    method: 'GET',
                    success: function(res) {
                        // console.log(res.data);
                        const categories = res.data;
                        $('.showData').empty();
                        if ($.fn.DataTable.isDataTable('#example')) {
                            $('#example').DataTable().clear().destroy();
                        }
                        if (categories.length > 0) {
                            $.each(categories, function(index, category) {
                                const tr = document.createElement('tr');
                                tr.innerHTML = `
                            <td>
                                ${index+1}
                            </td>
                            <td>
                                ${category.name ?? ""}
                            </td>
                           <td> <button id="categoryButton_${category.id}"
                                    class="btn ${category.status === 'inactive' ? 'btn-danger' : 'btn-success'} categoryButton"
                                    data-id="${category.id}"
                                    data-status="${category.status}">
                                ${category.status === 'inactive' ? 'Inactive' : 'Active'}
                            </button> </td>

                            <td>
                                @if (Auth::user()->can('category.edit'))
                                <a href="#" class="btn btn-primary btn-icon category_edit" data-id=${category.id} data-bs-toggle="modal" data-bs-target="#edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                @endif
                                @if (Auth::user()->can('category.delete'))
                                <a href="#" class="btn btn-danger btn-icon category_delete" data-id=${category.id}>
                                    <i class="fa-solid fa-trash-can"></i>
                                </a>
                                @endif
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
                                            Category<i data-feather="plus"></i></button>
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
            categoryView();

            // edit category
            $(document).on('click', '.category_edit', function(e) {
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
                    url: `/category/edit/${id}`,
                    type: 'GET',
                    success: function(data) {
                        // console.log(data.category.name);
                        $('.edit_category_name').val(data.category.name);
                        $('.update_category').val(data.category.id);
                        if (data.category.image) {
                            $('.showEditImage').attr('src',
                                `${url}/uploads/category/` + data.category
                                .image);
                        } else {
                            $('.showEditImage').attr('src',
                                `${url}/dummy/image.jpg`);
                        }
                    }
                });
            })

            // update category
            $('.update_category').click(function(e) {
                e.preventDefault();
                // alert('ok');
                let id = $('.update_category').val();
                // console.log(id);
                let formData = new FormData($('.categoryFormEdit')[0]);
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: `/category/update/${id}`,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        if (res.status == 200) {
                            $('#edit').modal('hide');
                            $('.categoryFormEdit')[0].reset();
                            categoryView();
                            toastr.success(res.message);
                        } else {
                            showError('.edit_category_name', res.error.name)
                            // $('.edit_category_name').css('border-color', 'red');
                            // $('.edit_category_name').focus();
                            // $('.edit_category_name_error').show();
                            // $('.edit_category_name_error').text(res.error.name);
                        }
                    }
                });
            })


            // category Delete
            $(document).on('click', '.category_delete', function(e) {
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
                            url: `/category/destroy/${id}`,
                            type: 'GET',
                            success: function(res) {
                                if (res.status == 200) {
                                    Swal.fire({
                                        title: "Deleted!",
                                        text: "Your file has been deleted.",
                                        icon: "success"
                                    });
                                    categoryView();
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


            // category Status
            $(document).ready(function() {
                $('.showData').on('click', '.categoryButton', function() {
                    var button = $(this);
                    var categoryId = button.data('id');
                    $.ajax({
                        url: '/category/status/' + categoryId,
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


        });
    </script>
@endsection
