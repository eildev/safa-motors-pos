@extends('master')
@section('title', '| Product List')
@section('admin')
    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Variation</li>
        </ol>
    </nav>
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="card-title">Product Variation</h6>

                        {{-- <a href="{{ route('product') }}" class="btn btn-primary">
                        <i class="fa-solid fa-plus"></i> Add Product
                    </a> --}}
                        <a href=""></a>
                    </div>
                    <div class="card-body show_ledger">
                        <div class="container-fluid w-100">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <td>Product Name</td>
                                                    <td> {{ $product->name }}</td>
                                                    <td>Cost Price</td>
                                                    <td>{{ $product->cost_price ?? 0 }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Category Name</td>
                                                    <td>{{ $product->category->name ?? '' }}</td>

                                                    <td>Sale Price</td>
                                                    <td>{{ $product->base_sell_price ?? 0 }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Subcategory</td>
                                                    <td>{{ $product->subcategory->name ?? '' }}</td>
                                                    <td>Brand</td>
                                                    <td>{{ $product->brand->name ?? '' }}</td>

                                                </tr>

                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <h4 class="my-3 text-center">

                        Product All Variation
                    </h4>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>SN</th>
                                    <th>Price</th>
                                    <th>Image</th>
                                    <th>Size</th>
                                    <th>Color</th>
                                    <th>Model No</th>
                                    <th>Quality</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($product->productvariation as $key => $variation)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $variation->price }}</td>
                                        <td>
                                            @if ($variation->image)
                                                <img src="{{ asset('uploads/products/' . $variation->image) }}"
                                                    alt="Image" width="50">
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>{{ $variation->size ?? 'N/A' }}</td>
                                        <td>{{ $variation->color ?? 'N/A' }}</td>
                                        <td>{{ $variation->model_no ?? 'N/A' }}</td>
                                        <td>{{ $variation->quality ?? 'N/A' }}</td>
                                        <td>{{ $variation->status ?? '' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No variations found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
