@extends('master')
@section('title', '| Tags')
@section('admin')
    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Tags</li>
        </ol>
    </nav>
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="card-title">Tags Table</h6>
                        <button class="btn btn-rounded-primary btn-sm" data-bs-toggle="modal"
                            data-bs-target="#exampleModalLongScollable"><i data-feather="plus"></i></button>
                    </div>
                    <div class="table-responsive">
                        <table id="example" class="table">
                            <thead>
                                <tr>
                                    <th>SN</th>
                                    <th>Tags Name</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="showTagData">

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
                    <h5 class="modal-title" id="exampleModalScrollableTitle">Add Tags</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="modal-body">
                    <form id="signupForm" class="tagForm" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="name" class="form-label">Tag Name</label>
                            <input id="defaultconfig" class="form-control tag_name" maxlength="250" name="name"
                                type="text" onkeyup="errorRemove(this);" onblur="errorRemove(this);">
                            <span class="text-danger tag_name_error"></span>
                        </div>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary save_tag">Save</button>
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
                    <h5 class="modal-title" id="exampleModalScrollableTitle">Edit Tags</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="modal-body">
                    <form id="signupForm" class="tagsFormEdit" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="name" class="form-label">Category Name</label>
                            <input id="defaultconfig" class="form-control edit_tag_name" maxlength="250" name="name"
                                type="text" onkeyup="errorRemove(this);" onblur="errorRemove(this);">
                            <span class="text-danger edit_tag_name_error"></span>
                        </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary update_tags">Update</button>
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

            // show error
            function showError(name, message) {
                $(name).css('border-color', 'red'); // Highlight input with red border
                $(name).focus(); // Set focus to the input field
                $(`${name}_error`).show().text(message); // Show error message
            }

            // save Tag
            const saveTag = document.querySelector('.save_tag');
            saveTag.addEventListener('click', function(e) {
                e.preventDefault();
                let formData = new FormData($('.tagForm')[0]);
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: '/tag/store',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        if (res.status == 200) {
                            $('#exampleModalLongScollable').modal('hide');
                            $('.tagForm')[0].reset();
                            tagsView();
                            toastr.success(res.message);
                        } else {
                            showError('.tag_name', res.error.name);
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
            // show   tags View;
            function tagsView() {
                $.ajax({
                    url: '/tags/view',
                    method: 'GET',
                    success: function(res) {
                        // console.log(res.data);
                        const tags = res.data;
                        $('.showTagData').empty();
                        if ($.fn.DataTable.isDataTable('#example')) {
                            $('#example').DataTable().clear().destroy();
                        }
                        if (tags.length > 0) {
                            $.each(tags, function(index, tag) {
                                const tr = document.createElement('tr');
                                tr.innerHTML = `
                            <td>
                                ${index+1}
                            </td>
                            <td>
                                ${tag.name ?? ""}
                            </td>
                           <td> <button id="tagButton_${tag.id}"
                                    class="btn ${tag.status === 'inactive' ? 'btn-danger' : 'btn-success'} tagButton"
                                    data-id="${tag.id}"
                                    data-status="${tag.status}">
                                ${tag.status === 'inactive' ? 'Inactive' : 'Active'}
                            </button> </td>

                            <td>

                                <a href="#" class="btn btn-primary btn-icon tags_edit" data-id=${tag.id} data-bs-toggle="modal" data-bs-target="#edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>

                                <a href="#" class="btn btn-danger btn-icon tags_delete" data-id=${tag.id}>
                                    <i class="fa-solid fa-trash-can"></i>
                                </a>

                            </td>
                            `;
                                $('.showTagData').append(tr);
                            })
                        } else {
                            $('.showTagData').html(`
                            <tr>
                                <td colspan='8'>
                                    <div class="text-center text-warning mb-2">Data Not Found</div>
                                    <div class="text-center">
                                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModalLongScollable">Add
                                            Tags<i data-feather="plus"></i></button>
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
            tagsView();

            // edit Tags
            $(document).on('click', '.tags_edit', function(e) {
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
                    url: `/tags/edit/${id}`,
                    type: 'GET',
                    success: function(data) {

                        $('.edit_tag_name').val(data.tags.name);
                        $('.update_tags').val(data.tags.id);

                    }
                });
            })

            // update category
            $('.update_tags').click(function(e) {
                e.preventDefault();
                // alert('ok');
                let id = $('.update_tags').val();
                console.log(id);
                let formData = new FormData($('.tagsFormEdit')[0]);
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: `/tags/update/${id}`,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        if (res.status == 200) {
                            $('#edit').modal('hide');
                            $('.tagsFormEdit')[0].reset();
                            tagsView();
                            toastr.success(res.message);
                        } else {
                            showError('.edit_tag_name', res.error.name)

                        }
                    }
                });
            })


            // // Tags Delete
            $(document).on('click', '.tags_delete', function(e) {
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
                            url: `/tags/destroy/${id}`,
                            type: 'GET',
                            success: function(res) {
                                if (res.status == 200) {
                                    Swal.fire({
                                        title: "Deleted!",
                                        text: "Your file has been deleted.",
                                        icon: "success"
                                    });
                                    tagsView();
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
                $('.showTagData').on('click', '.tagButton', function() {
                    var button = $(this);
                    var tagId = button.data('id');
                    $.ajax({
                        url: '/tags/status/' + tagId,
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
