@extends('master')
@section('title', '| Add Product')
@section('admin')
    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">New Product</li>
        </ol>
    </nav>
    <form class="productForm" enctype="multipart/form-data">
        <div class="row">
            <div class="col-lg-8 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="card-title">Add Product</h6>
                        </div>
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="name" class="form-label">Product Name <span
                                        class="text-danger">*</span></label>
                                <input class="form-control name" onblur="generateCode(this);" name="name" type="text"
                                    onkeyup="errorRemove(this);" onchange="errorRemove(this);" value="{{ old('name') }}">
                                <span class="text-danger name_error"></span>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="name" class="form-label">Product Code</label>
                                <input class="form-control @error('barcode') is-invalid @enderror" name="barcode"
                                    type="number" value="{{ old('barcode') }}" readonly>
                                @error('barcode')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-4">
                                @php
                                    $categories = App\Models\Category::get();
                                @endphp
                                <label for="ageSelect" class="form-label">Category <span
                                        class="text-danger">*</span></label>
                                <select class="js-example-basic-single form-select category_id" id="category_name"
                                    name="category_id" onchange="errorRemove(this);">
                                    @if ($categories->count() > 0)
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ old('category_name') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}</option>
                                        @endforeach
                                    @else
                                        <option selected disabled>Please Add Category</option>
                                    @endif
                                </select>
                                <span class="text-danger category_id_error"></span>
                            </div>
                            <div class="mb-3 col-md-4">
                                <label for="ageSelect" class="form-label">Subcategory <span
                                        class="text-danger">*</span></label>
                                <select class="js-example-basic-single form-select subcategory_id" name="subcategory_id">
                                    {{-- <option selected disabled>Select Subcategory</option> --}}
                                </select>
                            </div>
                            <div class="mb-3 col-md-4">
                                @php
                                    $brands = App\Models\Brand::get();
                                @endphp
                                <label for="ageSelect" class="form-label">Brand <span class="text-danger">*</span></label>
                                <select class="js-example-basic-single form-select brand_id" name="brand_id"
                                    onchange="errorRemove(this);">
                                    @if ($brands->count() > 0)
                                        {{-- <option selected disabled>Select Brand</option> --}}
                                        @foreach ($brands as $brand)
                                            <option value="{{ $brand->id }}"
                                                {{ old('brand_id') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option selected disabled>Please Add Brand</option>
                                    @endif
                                </select>
                                <span class="text-danger brand_id_error"></span>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="password" class="form-label">Cost Price</label>
                                <input class="form-control" name="cost" value="{{ old('cost') }}" type='number'
                                    placeholder="00.00" />
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="password" class="form-label">Sale Price <span
                                        class="text-danger">*</span></label>
                                <input class="form-control price" name="price" value="{{ old('price') }}" type='number'
                                    placeholder="00.00" onkeyup="errorRemove(this);" onblur="errorRemove(this);" />
                                <span class="text-danger price_error"></span>
                            </div>
                            <div class="mb-3 col-12">
                                <label for="" class="form-label">Description</label>
                                <textarea class="form-control" value="{{ old('details') }}" name="details" id="tinymceExample" rows="5"></textarea>
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
                                <input class="form-control" name="stock" type="number" placeholder="00">
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="name" class="form-label">Main Unit Stock</label>
                                <input class="form-control" name="main_unit_stock" type="number" placeholder="00">
                            </div> --}}
                            {{-- <div class="mb-3 col-md-6">
                                <label for="name" class="form-label">Total Sold</label>
                                <input class="form-control" name="total_sold" type="number" placeholder="00">
                            </div> --}}
                            <div class="mb-3 col-md-6">
                                <label for="ageSelect" class="form-label">Color</label>
                                {{-- <div id="pickr_1"></div> --}}
                                <input type="color" class="form-control" value="{{ old('color') }}" name="color"
                                    id="">
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="ageSelect" class="form-label">Size </label>
                                <select class="js-example-basic-single form-select size_id" name="size_id">
                                    <option selected disabled>Select Size</option>
                                </select>
                            </div>
                            <div class="mb-3 col-md-12">
                                @php
                                    $units = App\Models\Unit::get();
                                @endphp
                                <label for="ageSelect" class="form-label">Unit <span class="text-danger">*</span></label>
                                <select class="js-example-basic-single form-select unit_id" name="unit_id"
                                    onchange="errorRemove(this);">
                                    @if ($units->count() > 0)
                                        <option selected disabled>Select Unit</option>
                                        @foreach ($units as $unit)
                                            <option value="{{ $unit->id }}"
                                                {{ old('unit_id') == $unit->id ? 'selected' : '' }}>{{ $unit->name }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option selected disabled>Please Add Unit</option>
                                    @endif
                                </select>
                                <span class="text-danger unit_id_error"></span>
                            </div>
                            <div class="mb-3 col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title">Product Image</h6>
                                        <p class="mb-3 text-warning">Note: <span class="fst-italic">Image not
                                                required. If you
                                                add
                                                a category image
                                                please add a 400 X 400 size image.</span></p>
                                        <input type="file" class="categoryImage" name="image" id="myDropify" />
                                    </div>
                                </div>
                            </div>
                            <div>
                                <input class="btn btn-primary w-full save_product" type="submit" value="Submit">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>


    <script>
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

            // when select category
            subCategory($('.category_id').val());
            $('.category_id').change(function() {
                let id = $(this).val();
                // alert(id);
                if (id) {
                    subCategory(id);
                }
            })

            function subCategory(categoryId) {
                $.ajax({
                    url: '/subcategory/find/' + categoryId,
                    type: 'GET',
                    dataType: 'JSON',
                    success: function(res) {
                        if (res.status == 200) {
                            $('.subcategory_id').empty();
                            // $('.subcategory_id').size_id();
                            // console.log(res);

                            // show subcategory
                            if (res.data.length > 0) {

                                // console.log(res.data)
                                // $('.subcategory_id').html(
                                //     '<option selected disabled>Select a SubCategory</option>'
                                // );
                                $.each(res.data, function(key, item) {
                                    $('.subcategory_id').append(
                                        `<option value="${item.id}">${item.name}</option>`
                                    );
                                })
                            } else {
                                $('.subcategory_id').html(`
                                        <option selected disable>Please add Subcategory</option>`)
                            }

                            // show Size
                            if (res.size.length > 0) {
                                // console.log(res.size);
                                $('.size_id').html(
                                    '<option selected disabled>Select a Size</option>'
                                );
                                $.each(res.size, function(key, item) {

                                    $('.size_id').append(
                                        `<option value="${item.id}">${item.size}</option>`
                                    );
                                })
                            } else {
                                $('.size_id').html(`
                                        <option selected disable>Please add Size</option>`)
                            }
                        }
                    }
                });
            }

            // product save
            $('.save_product').click(function(e) {
                e.preventDefault();
                // alert('ok')
                let formData = new FormData($('.productForm')[0]);
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: '/product/store',
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

        function generateCode(input) {
            var nameInput = input.value.trim();
            if (nameInput !== "") {
                var codeInput = input.parentElement.nextElementSibling.querySelector('input[name="barcode"]');
                var randomNumber = Math.floor(Math.random() * 1000000) +
                    20; // Generate a random number between 1 and 1000000
                var generatedCode = nameInput.replace(/\s+/g, '').toUpperCase() + randomNumber;
                var generatedNumber = randomNumber; // Extract the generated number

                codeInput.value = generatedNumber; // Set the generated number directly in the input field
            }
        }
    </script>
@endsection
