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
                                <input class="form-control name" name="name" type="text" onkeyup="errorRemove(this);"
                                    onchange="errorRemove(this);" value="{{ old('name') }}">
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
                                <label for="ageSelect" class="form-label">Purchase Unit <span class="text-danger">*</span></label>
                                <select class="js-example-basic-single form-select purchase_unit" name="purchase_unit"
                                    onchange="errorRemove(this);">
                                    @if ($units->count() > 0)
                                        <option selected disabled>Select Purchase  Unit</option>
                                        @foreach ($units as $unit)
                                            <option value="{{ $unit->id }}">{{ $unit->name }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option selected disabled>Please Add Unit</option>
                                    @endif
                                </select>
                                <span class="text-danger purchase_unit_error"></span>
                            </div>
                            <div class="mb-3 col-md-4">
                                <label for="ageSelect" class="form-label">Sale Unit <span class="text-danger">*</span></label>
                                <select class="js-example-basic-single form-select sale_unit" name="sale_unit"
                                    onchange="errorRemove(this);">
                                    @if ($units->count() > 0)
                                        <option selected disabled>Select Sale Unit</option>
                                        @foreach ($units as $unit)
                                            <option value="{{ $unit->id }}">{{ $unit->name }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option selected disabled>Please Add Sale Unit</option>
                                    @endif
                                </select>
                                <span class="text-danger sale_unit_error"></span>
                            </div>
                            <div class="mb-3 col-md-4">
                                <label for="ageSelect" class="form-label">Model No </label>
                                <input type="text" class="form-control" name="model_no">
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="password" class="form-label">Cost Price</label>
                                <input class="form-control" name="cost_price"  type='number'
                                    placeholder="00.00" />
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="password" class="form-label">Sale Price <span
                                        class="text-danger">*</span></label>
                                <input class="form-control base_sell_price" name="base_sell_price"
                                    type='number' placeholder="00.00" onkeyup="errorRemove(this);"
                                    onblur="errorRemove(this);" />
                                <span class="text-danger base_sell_price_error"></span>
                            </div>
                            <div class="mb-3 col-12">
                                <label for="" class="form-label">Description</label>
                                <textarea class="form-control" name="description" rows="3"></textarea>
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

                                <input type="color" class="form-control"  name="color"
                                    id="">
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="ageSelect" class="form-label">Quality</label>
                                <select class="form-control js-example-basic-single " name="quality">
                                    <option selected disabled>Select Quality</option>
                                    <option value="grade-a">Grade A</option>
                                    <option value="grade-b">Grade B</option>
                                    <option value="grade-c">Grade C</option>
                                </select>
                            </div>
                            <div class="mb-3 col-md-12">
                                @php
                                    $tags = App\Models\Tags::where('status','active')->get();
                                @endphp
                                <label class="form-label">Tags </label>

                                <select name="tag_id[]" class="compose-multiple-select form-select form-control"
                                    multiple="multiple">
                                    @if ($tags->count() > 0)

                                        @foreach ($tags as $tag)
                                            <option value="{{ $tag->id }}">{{ $tag->name }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option  disabled>Please Add Tags</option>
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
{{-- ///////////////////////////////////////////////Variation Create Code ///////////////////////////////// --}}
<div>
    <div class="row">

        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <form action="" id="serviceForm">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="card-title">New Variation Add </h6>

                        </div>
                        <div id="" class="table-responsive">
                            <div class="bill-header">
                                <div class="row no-gutters">
                                    {{-- <div class="col-md-2">
                                        <strong>Customer Name:</strong>
                                    </div> --}}

                                    {{-- <div class="col-md-4">
                                        <select class="form-control js-example-basic-single " name="customer_id"
                                            id="customer-select">
                                            <option selected disabled>Select Name</option>
                                            @foreach ($customers as $customer)
                                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                            @endforeach
                                        </select>
                                    </div> --}}

                                </div>
                            </div>

                            <!-- /////////Tabing Start//// -->
                            <div class="row">
                                <div class="col-md-12 grid-margin stretch-card">
                                    <div class="example w-100">
                                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                                            <!---First li--->
                                            {{-- <li class="nav-item">
                                        <a class="nav-link active" id="serviceSale-tab" data-bs-toggle="tab"
                                            href="#serviceSale" role="tab" aria-controls="serviceSale"
                                            aria-selected="true">Movement Costs
                                        </a>
                                         </li> --}}

                                        </ul>
                                        <!--First Tab  Start-->

                                        <div class="tab-content border border-top-0 p-3" id="myTabContent">

                                            <div class="tab-pane fade show active" id="serviceSale" role="tabpanel"
                                                aria-labelledby="serviceSale-tab">
                                                <div class="col-md-12 serviceSale">

                                                    <table id="variationTable">
                                                        <thead>
                                                            <tr>
                                                                <th><button type="button" class="form-control"
                                                                        id="addVariationRowBtn">+
                                                                    </button></th>
                                                                <th>Price</th>
                                                                <th>Size</th>
                                                                <th>Color</th>
                                                                <th>Model No</th>
                                                                <th>Quality</th>
                                                                <th>Image</th>

                                                            </tr>
                                                        </thead>
                                                        <tbody>

                                                        </tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <td colspan="4" class="text-end"><strong>Total
                                                                        Amount</strong>
                                                                </td>
                                                                <td><strong id="totalAmount">00</strong></td>

                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                                <button type="submit" class="btn btn-md float-end serviceSaleAdd"
                                                    style="border:1px solid #6587ff ">Submit</button>

                                            </div>
                                            <!--First Tab End -->
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- ///////////////////////////////////////////////Variation Create Code ///////////////////////////////// --}}

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
                            // $('.productForm')[0].reset();
                            toastr.success(res.message);
                            // window.location.href = "{{ route('product.view') }}";
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
                            if (error.purchase_unit) {
                                showError('.purchase_unit', error.purchase_unit);
                            }
                            if (error.sale_unit) {
                                showError('.sale_unit', error.sale_unit);
                            }
                            if (error.size) {
                                showError('.size', error.size);
                            }
                        }
                    }
                });
            })
        });
        /////////////////////////////////////////////////Variation Create Code ///////////////////////////////////
        document.getElementById('addVariationRowBtn').addEventListener('click', function() {
            let tableBody = document.querySelector('#variationTable tbody');
            let totalAmountElement = document.getElementById('totalAmount');
            let grandTotalAmountElement = document.getElementById('grandTotalDisplay');
            let newRow = document.createElement('tr');

            // Create new row with input fields
            newRow.innerHTML = `
              <td><button type="button" class="removeVariationRowBtn form-control text-danger btn-xs btn-danger">
                <i class="fa-solid fa-trash-can "></i></button></td>
                <td> <input type="number" class="input-group flatpickr form-control" name="price[]" placeholder="Service Name" value=""></td>

                <td>
                    <input type="number" class="form-control volume-input"  name="volume[]" placeholder="Volume">
                    </td>

                <td>
                   <input type="color" class="form-control"  name="color" >
                </td>
                <td><input type="text" class="form-control  total-input" readonly name="model_no[]" value=""  placeholder="Model No"></td>
                <td>
                    <select class="form-control js-example-basic-single " name="quality">
                                    <option selected disabled>Select Quality</option>
                                    <option value="grade-a">Grade A</option>
                                    <option value="grade-b">Grade B</option>
                                    <option value="grade-c">Grade C</option>
                                </select>

                </td>
                <td><input type="file" class="form-control  total-input" readonly name="image[]" value=""  ></td>

            `;
            // Append the new row to the table body
            tableBody.appendChild(newRow);

            let volumeInput = newRow.querySelector('.volume-input');
            let priceInput = newRow.querySelector('.price-input');
            let totalInput = newRow.querySelector('.total-input');
            [volumeInput, priceInput].forEach(input => {
                input.addEventListener('input', function() {
                    if (this.value < 0) {
                        this.value = '';
                        toastr.warning('⚠️ Negative values are not allowed.');
                    }
                });
            });

            function calculateTotal() {
                let volume = parseFloat(volumeInput.value) || 0;
                let price = parseFloat(priceInput.value) || 0;
                totalInput.value = (volume * price).toFixed(2);
                calculateTotalSum();
            }
            volumeInput.addEventListener('input', calculateTotal);
            priceInput.addEventListener('input', calculateTotal);

            function calculateTotalSum() {
                let totalInputs = document.querySelectorAll('.total-input');
                let grandTotal = 0;

                totalInputs.forEach(input => {
                    grandTotal += parseFloat(input.value) || 0;
                });

                totalAmountElement.textContent = grandTotal.toFixed(2);
                grandTotalAmountElement.textContent = grandTotal.toFixed(2);
            }
            newRow.querySelector('.removeVariationRowBtn').addEventListener('click', function() {
                newRow.remove();
                calculateTotalSum();
            });
        });
        //Validate
        const serviceSaleAdd = document.querySelector('.serviceSaleAdd');
        const serviceForm = document.getElementById('serviceForm');
        serviceSaleAdd.addEventListener('click', function(e) {
            e.preventDefault();
            ///////////////Validation Start /////////////
            const rows = document.querySelectorAll('#variationTable tbody tr');

            // Initialize validation variables
            let allFieldsFilled = true;
            let errorMessages = [];

            // Loop through each row and validate inputs
            rows.forEach(function(row) {
                let serviceName = row.querySelector('input[name="serviceName[]"]').value.trim();
                let volume = row.querySelector('input[name="volume[]"]').value.trim();
                let price = row.querySelector('input[name="price[]"]').value.trim();

                // Validate Service Name
                if (!serviceName) {
                    errorMessages.push('⚠️ Service Name field is required.');
                    allFieldsFilled = false;
                }

                // Validate Volume
                if (!volume) {
                    errorMessages.push('⚠️ Volume field is required.');
                    allFieldsFilled = false;
                } else if (isNaN(volume) || volume <= 0) {
                    errorMessages.push('⚠️ Volume must be a positive number.');
                    allFieldsFilled = false;
                }

                // Validate Price
                if (!price) {
                    errorMessages.push('⚠️ Price field is required.');
                    allFieldsFilled = false;
                } else if (isNaN(price) || price <= 0) {
                    errorMessages.push('⚠️ Price must be a positive number.');
                    allFieldsFilled = false;
                }
            });

            // If validation fails, display error messages
            if (!allFieldsFilled) {
                toastr.warning(errorMessages.join('<br>'));
                return;
            }


        });
        /////////////////////////////////////////////////Variation Create Code ///////////////////////////////////
    </script>
@endsection
