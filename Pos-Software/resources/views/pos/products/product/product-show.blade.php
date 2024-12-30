@extends('master')
@section('title', '| Product List')
@section('admin')

    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Manage Products</li>
        </ol>
    </nav>
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="card-title">Product Table</h6>
                        <a href="{{ route('product') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Add
                            Product</a>
                    </div>
                    <div class="table-responsive">
                        <table id="example" class="table">
                            <thead>
                                <tr>
                                    <th>SN</th>
                                    <th>Image</th>
                                    <th>Name</th>
                                    @if ($barcode == 1)
                                        <th>Barcode</th>
                                    @endif
                                    <th>Category</th>
                                    <th>Brand</th>
                                    <th>Cost Price</th>
                                    <th>Sale Price</th>
                                    <th>Stock</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($products->count() > 0)
                                    @foreach ($products as $key => $product)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>
                                                <img src="{{ $product->image ? asset('uploads/product/' . $product->image) : asset('dummy/image.jpg') }}"
                                                    alt="product image">
                                            </td>
                                            <td>
                                                <a href="{{ route('product.ledger', $product->id) }}">
                                                    {{ $product->name ?? '' }}
                                                </a>
                                            </td>
                                            @if ($barcode == 1)
                                                <td>{{ $product->barcode }}</td>
                                            @endif
                                            <td>{{ $product->category->name ?? '' }}</td>
                                            <td>{{ $product->brand->name ?? '' }}</td>
                                            <td>{{ $product->cost ?? 0 }}</td>
                                            <td>{{ $product->price ?? 0 }}</td>
                                            <td>{{ $product->stock ?? 0 }} ({{ $product->unit->name ?? '' }})</td>
                                            <td>
                                                @if (Auth::user()->can('products.edit'))
                                                    <a href="{{ route('product.edit', $product->id) }}"
                                                        class="btn btn-primary btn-icon">
                                                        <i data-feather="edit"></i>
                                                    </a>
                                                @endif
                                                @if (Auth::user()->can('products.delete'))
                                                    <a href="{{ route('product.destroy', $product->id) }}"
                                                        class="btn btn-danger btn-icon" id="delete">
                                                        <i data-feather="trash-2"></i>
                                                    </a>
                                                @endif
                                                @if ($barcode == 1)
                                                    <a target="_blank" href="{{ route('product.barcode', $product->id) }}"
                                                        class="btn btn-info btn-icon">
                                                        <i class="fa-solid fa-barcode"></i>
                                                    </a>
                                                @endif
                                                {{-- <a href="#"  data-bs-toggle="modal" data-bs-target="#exampleModal{{$product->id}}" class="input-text btn border-dark">
                                                    <i class="fa-solid fa-barcode"></i>
                                                </a> --}}
                                            </td>
                                        </tr>

                                        {{-- /Modal Start/ --}}
                                        <!-- Button trigger modal -->

                                        <!-- Modal -->
                                        {{-- <div class="modal fade modal-lg" id="exampleModal{{$product->id}}"  tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center border">
                        <div class="row">
                        @for ($i = 0; $i < $product->stock; $i++)
                        <div class="col-md-4">
                        <div class="barcode-container">
                            <span class="dblock">
                            {!! DNS1D::getBarcodeHTML($product->barcode, 'PHARMA') !!}</span><br>
                            <span style="">{{$product->barcode}}</span><br>
                            <span>{{ $product->name ?? '' }} </span><br>
                            <span class="bold">{{ $product->price ?? 0 }}TK</span>
                        </div>
                    </div>
                        @endfor
                    </div>
                </div>
                    <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button onclick="printModalContent('modalContent{{$product->id}}')" class="btn btn-primary">Print</button>
                    </div>
                </div>
                </div>
            </div> --}}
                                        {{-- /Modal End/ --}}
                                    @endforeach
                                @endif

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function printModalContent(modalId) {
            var modalBodyContent = document.getElementById(modalId).getElementsByClassName('modal-body')[0].innerHTML;
            var printWindow = window.open('', '_blank');
            printWindow.document.write(
                '<html><head><title>Print</title><link rel="stylesheet" type="text/css" href="print.css" /></head><body>' +
                modalBodyContent + '</body></html>');
            printWindow.document.close();
            printWindow.print();

        }
    </script>
    <style>
        .barcode-container {
            text-align: center;
            border: 1px solid #e9ecef;
            padding: 10px;
        }

        .dblock {
            display: inline-block;
        }
    </style>
@endsection
