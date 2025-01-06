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
                                <input class="form-control name product_id" name="name" type="text" onkeyup="errorRemove(this);"
                                    onchange="errorRemove(this);" value="{{$product->name }}">
                                <span class="text-danger name_error"></span>
                            </div>
                            <div class="mb-3 col-md-6">
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
                                                {{ $product->category_id  == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}</option>
                                        @endforeach
                                    @else
                                        <option selected disabled>Please Add Category</option>
                                    @endif
                                </select>
                                <span class="text-danger category_id_error"></span>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="ageSelect" class="form-label">Subcategory <span
                                        class="text-danger">*</span></label>
                                <select class="js-example-basic-single form-select subcategory_id" name="subcategory_id">
                                </select>
                            </div>
                            <div class="mb-3 col-md-6">
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
                                                {{ $product->brand_id == $brand->id ? 'selected' : '' }}>{{ $brand->name }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option selected disabled>Please Add Brand</option>
                                    @endif
                                </select>
                                <span class="text-danger brand_id_error"></span>
                            </div>
                            <div class="mb-3 col-md-4">
                                <label for="ageSelect" class="form-label">Size <span
                                    class="text-danger">*</span></label>
                                <select class="js-example-basic-single form-select size_id  size" name="size"  onchange="errorRemove(this);">
                                    <option selected disabled>Select Size</option>
                                </select>
                                <span class="text-danger size_error"></span>
                            </div>
                            <div class="mb-3 col-md-4">
                                @php
                                    $units = App\Models\Unit::where('status','active')->get();
                                @endphp
                                <label for="ageSelect" class="form-label"> Unit <span class="text-danger">*</span></label>
                                <select class="js-example-basic-single form-select unit" name="unit"
                                    onchange="errorRemove(this);">
                                    @if ($units->count() > 0)
                                        <option selected disabled>Select Unit</option>
                                        @foreach ($units as $unit)
                                            <option value="{{ $unit->id }}" {{ $product->unit  === $unit->id ? 'selected' : '' }}>{{ $unit->name }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option selected disabled>Please Add Purchase Unit</option>
                                    @endif
                                </select>
                                <span class="text-danger unit_error"></span>
                            </div>


                            <div class="mb-3 col-md-4">
                                <label for="ageSelect" class="form-label">Model No </label>
                                <input type="text" class="form-control"  name="model_no" value={{$product->variation->model_no ?? ''}}>
                            </div>

                            <div class="mb-3 col-md-6">
                                <label for="password" class="form-label">Cost Price</label>
                                <input class="form-control" name="cost_price" value={{$product->cost_price ?? ''}}  type='number'
                                    placeholder="00.00" />
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="password" class="form-label">Sale Price <span
                                        class="text-danger">*</span></label>
                                <input class="form-control base_sell_price" name="base_sell_price"
                                    type='number' placeholder="00.00" value={{$product->base_sell_price ?? ''}}  onkeyup="errorRemove(this);"
                                    onblur="errorRemove(this);" />
                                <span class="text-danger base_sell_price_error"></span>
                            </div>
                            <div class="mb-3 col-12">
                                <label for="" class="form-label">Description</label>
                                <textarea class="form-control" name="description" rows="3">{{$product->description ?? ''}} </textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="ageSelect" class="form-label">Color</label>
                                <input type="color" value="{{$product->product_details->color ?? ''}}"  class="form-control"  name="color"
                                    id="">
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="ageSelect" class="form-label">Quality</label>
                                <select class="form-control js-example-basic-single "name="quality">
                                    <option selected disabled>Select Quality</option>
                                    <option value="grade-a" {{ isset($product->variation) && $product->variation->quality == 'grade-a' ? 'selected' : '' }}>Grade A</option>
                                    <option value="grade-b" {{ isset($product->variation) && $product->variation->quality == 'grade-b' ? 'selected' : '' }}>Grade B</option>
                                    <option value="grade-c" {{ isset($product->variation) && $product->variation->quality == 'grade-c' ? 'selected' : '' }}>Grade C</option>
                                </select>
                            </div>

                            <div class="mb-3 col-md-12">
                                @php
                                    $tags = App\Models\Tags::where('status','active')->get();
                                    $tagsEdit = App\Models\ProductTags::where('product_id', $product->id)->pluck('tag_id')->toArray();                                @endphp
                                <label class="form-label">Tags </label>

                                <select name="tag_id[]" class="compose-multiple-select form-select form-control"
                                    multiple="multiple">
                                    @if ($tags->count() > 0)
                                        @foreach ($tags as $tag)
                                        <option value="{{ $tag->id }}" {{ in_array($tag->id, $tagsEdit) ? 'selected' : '' }}>
                                            {{ $tag->name }}
                                        </option>
                                        @endforeach
                                    @else
                                        <option selected disabled>Please Add Tags</option>
                                    @endif
                                </select>

                                <span class="text-danger tag_id_error"></span>
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
                                        {{-- <input type="file" class="categoryImage" name="image" id="myDropify" /> --}}
                                        <input type="file"
                                        data-default-file="{{ $product->variation? asset('uploads/products/' . $product->variation->image) : '' }}"
                                        class="categoryImage" name="image" id="myDropify" />
                                    </div>
                                </div>
                            </div>
                            <div>
                                <button class="btn btn-primary w-full update_product" type="submit"
                                value="{{ $product->id }}">Update</button>
                                {{-- <input class="btn btn-primary w-full save_product" type="submit" value="Submit"> --}}
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
                let categoryId  = $(this).val();
                    if (categoryId) {
                        subCategory(categoryId); // Use the injected product ID
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
                                        <option selected disabled>Please add Size</option>`)
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

                            if (error.base_sell_price) {
                                showError('.base_sell_price', error.base_sell_price);
                            }
                            if (error.unit) {
                                showError('.unit', error.unit);
                            }
                            if (error.size) {
                                showError('.size', error.size);
                            }
                        }
                    }
                });
            })


            ///Update
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

                            if (error.base_sell_price) {
                                showError('.base_sell_price', error.base_sell_price);
                            }
                            if (error.unit) {
                                showError('.unit', error.unit);
                            }
                            if (error.size) {
                                showError('.size', error.size);
                            }
                        }
                    }
                });
            })
        });
    </script>
@endsection
