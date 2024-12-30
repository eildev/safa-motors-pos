<div class="row">
    <div class="col-md-12 grid-margin stretch-card">

        <div class="card">
            <div class="card-body">
                <h6>Product Info</h6> <br>
                <div class="table-responsive">
                    <table id="example" class="table" style="padding-top:10px">
                        <thead>
                            <tr>
                                <th>SN</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Barcode No</th>
                                <th>Category</th>
                                <th>Brand</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Unit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($productInfo->count() > 0)
                                @foreach ($productInfo as $key => $product)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>
                                            <img src="{{ $product->image ? asset('uploads/product/' . $product->image) : asset('dummy/image.jpg') }}"
                                                alt="product image">
                                        </td>
                                        <td>{{ $product->name ?? '' }}</td>
                                        <td>{{$product->barcode}}</td>
                                        <td>{{ $product->category->name ?? '' }}</td>
                                        <td>{{ $product->brand->name ?? '' }}</td>
                                        <td>{{ $product->price ?? 0 }}</td>
                                        <td>{{ $product->stock ?? 0 }}</td>
                                        <td>{{ $product->unit->name ?? '' }}</td>
                                    </tr>


                                @endforeach
                                @else
                                <tr>
                                    <td colspan="12">
                                        <div class="text-center text-warning mb-2">Data Not Found</div>
                                    </td>
                                </tr>
                            @endif

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
