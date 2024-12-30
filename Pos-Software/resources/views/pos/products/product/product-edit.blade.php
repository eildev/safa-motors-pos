@extends('master')
@section('title', '| Product Edit')
@section('admin')
    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Update Product</li>
        </ol>
    </nav>
    <form class="productForm" enctype="multipart/form-data">
        <div class="row">
            <div class="col-lg-8 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="card-title">Update Product</h6>
                        </div>
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="name" class="form-label">Product Name <span
                                        class="text-danger">*</span></label>
                                <input class="form-control name" name="name" type="text" onkeyup="errorRemove(this);"
                                    onblur="errorRemove(this);" value="{{ $product->name ?? '' }}">
                                <span class="text-danger name_error"></span>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="name" class="form-label">Product Code</label>
                                <input class="form-control" name="barcode" type="number"
                                    value="{{ $product->barcode ?? '' }}">
                            </div>
                            <div class="mb-3 col-md-4">
                                @php
                                    $categories = App\Models\Category::get();
                                @endphp
                                <label for="ageSelect" class="form-label">Category <span
                                        class="text-danger">*</span></label>
                                <select class="form-select category_id" id="category_name" name="category_id"
                                    onchange="errorRemove(this);" value="{{ $product->category->name ?? '' }}">
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ $category->id == $product->category_id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="text-danger category_id_error"></span>
                            </div>
                            <div class="mb-3 col-md-4">
                                @php
                                    $subcategories = App\Models\SubCategory::get();
                                @endphp
                                <label for="ageSelect" class="form-label">Subcategory <span
                                        class="text-danger">*</span></label>
                                <select class="form-select subcategory_id" name="subcategory_id"
                                    onchange="errorRemove(this);">
                                    @foreach ($subcategories as $subcategory)
                                        <option value="{{ $subcategory->id }}"
                                            {{ $subcategory->id == $product->subcategory_id ? 'selected' : '' }}>
                                            {{ $subcategory->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3 col-md-4">
                                @php
                                    $brands = App\Models\Brand::get();
                                @endphp
                                <label for="ageSelect" class="form-label">Brand <span class="text-danger">*</span></label>
                                <select class="form-select brand_id" name="brand_id" onchange="errorRemove(this);">
                                    @foreach ($brands as $brand)
                                        <option value="{{ $brand->id }}"
                                            {{ $brand->id == $product->brand_id ? 'selected' : '' }}>
                                            {{ $brand->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="text-danger brand_id_error"></span>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="password" class="form-label">Cost Price</label>
                                <input class="form-control" name="cost" type='number' placeholder="00.00"
                                    value="{{ $product->cost ?? 0 }}" />
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="password" class="form-label">Sale Price <span
                                        class="text-danger">*</span></label>
                                <input class="form-control price" name="price" type='number' placeholder="00.00"
                                    onkeyup="errorRemove(this);" onblur="errorRemove(this);"
                                    value="{{ $product->price ?? 0 }}" />
                                <span class="text-danger price_error"></span>
                            </div>
                            <div class="mb-3 col-12">
                                <label for="" class="form-label">Description</label>
                                <textarea class="form-control" name="details" id="tinymceExample" rows="5">{{ $product->description ?? '' }}</textarea>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            {{-- <div class="mb-3 col-md-6">
                                <label for="name" class="form-label">Stock</label>
                                <input class="form-control" name="stock" type="number" placeholder="00"
                                    value="{{ $product->stock ?? '' }}">
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="name" class="form-label">Main Unit Stock</label>
                                <input class="form-control" name="main_unit_stock" type="number" placeholder="00"
                                    value="{{ $product->main_unit_stock ?? '' }}">
                            </div> --}}
                            {{-- <div class="mb-3 col-md-6">
                                <label for="name" class="form-label">Total Sold</label>
                                <input class="form-control" name="total_sold" type="number" placeholder="00"
                                    value="{{ $product->total_sold ?? '' }}">
                            </div> --}}
                            <div class="mb-3 col-md-6">
                                <label for="ageSelect" class="form-label">Color</label>
                                {{-- <div id="pickr_1"></div> --}}
                                <input type="color" class="form-control" name="color" id=""
                                    value="{{ $product->color ?? '#000' }}">
                            </div>
                            <div class="mb-3 col-md-6">
                                @php
                                    $sizes = App\Models\Psize::get();
                                @endphp
                                <label for="ageSelect" class="form-label">Size </label>
                                <select class="form-select size_id" name="size_id">
                                    @foreach ($sizes as $size)
                                        <option value="{{ $size->id }}"
                                            {{ $size->id == $product->size_id ? 'selected' : '' }}>
                                            {{ $size->size }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3 col-md-12">
                                @php
                                    $units = App\Models\Unit::get();
                                @endphp
                                <label for="ageSelect" class="form-label">Unit <span class="text-danger">*</span></label>
                                <select class="form-select unit_id" name="unit_id" onchange="errorRemove(this);">
                                    @foreach ($units as $unit)
                                        <option value="{{ $unit->id }}"
                                            {{ $unit->id == $product->unit_id ? 'selected' : '' }}>
                                            {{ $unit->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="text-danger unit_id_error"></span>
                            </div>
                            <div class="mb-3 col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title">Product Image</h6>
                                        {{-- <div style="height:150px;position:relative">
                                            <button class="btn btn-info edit_upload_img"
                                                style="position: absolute;top:50%;left:50%;transform:translate(-50%,-50%)">Browse</button>
                                            <img class="img-fluid showEditImage"
                                                src="{{ $product->image ? asset('uploads/images/' . $product->image) : asset('dummy/image.jpg') }}"
                                                style="height:100%; object-fit:cover">
                                        </div> --}}
                                        <input type="file" class="productImage"
                                            data-default-file="{{ $product->image ? asset('uploads/images/' . $product->image) : asset('dummy/image.jpg') }}"
                                            name="image" id="myDropify" />
                                    </div>
                                </div>
                            </div>
                            <div>
                                <button class="btn btn-primary w-full update_product" type="submit"
                                    value="{{ $product->id }}">Update</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>


    <script>
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

            // when select category
            const category = document.querySelector('#category_name');
            category.addEventListener('change', function() {
                let category_id = $(this).val();
                // alert(category_id);
                // console.log(category_id);
                if (category_id) {
                    $.ajax({
                        url: '/subcategory/find/' + category_id,
                        type: 'GET',
                        dataType: 'JSON',
                        success: function(res) {
                            if (res.status == 200) {
                                // console.log(res.data)
                                // subcategory data
                                $('select[name="subcategory_id"]').html(
                                    '<option selected disabled>Select a Sub-Category</option>'
                                );
                                $.each(res.data, function(key, item) {
                                    $('select[name="subcategory_id"]').append(
                                        '<option myid="' + item.id +
                                        '" value="' + item.id +
                                        '">' + item
                                        .name + '</option>');
                                })

                                // size selcet
                                $('select[name="size_id"]').html(
                                    '<option selected disabled>Select a Size</option>');
                                $.each(res.size, function(key, item) {
                                    $('select[name="size_id"]').append(
                                        '<option myid="' + item.id +
                                        '" value="' + item.id +
                                        '">' + item
                                        .size + '</option>');
                                })

                            }
                        }
                    });
                }
            });


            // update_product
            $('.update_product').click(function(e) {
                e.preventDefault();
                let id = $(this).val();
                let formData = new FormData($('.productForm')[0]);
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: '/product/update/' + id,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        if (res.status == 200) {
                            // console.log(res);
                            $('.productForm')[0].reset();
                            toastr.success(res.message);
                            window.location.href = "{{ route('product.view') }}";
                        } else {
                            // console.log(res.error);
                            const error = res.error;
                            // console.log(error)
                            if (error.name) {
                                showError('.name', error.name);
                            }
                            if (error.category_id) {
                                showError('.category_id', error.category_id);
                            }
                            if (error.brand_id) {
                                showError('.brand_id', error.brand_id);
                            }
                            if (error.price) {
                                showError('.price', error.price);
                            }
                            if (error.unit_id) {
                                showError('.unit_id', error.unit_id);
                            }
                        }
                    }
                });
            })
        });
    </script>
@endsection
