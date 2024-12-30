@if ($products->count() > 0)
    @foreach ($products as $index => $data)
        {{-- @dd($data->damage); --}}
        <tr>
            <td class="id">{{ $index + 1 }}</td>
            <td>
                @if ($data->image > 0)
                    <img src="{{ asset('uploads/product/' . $data->image) }}" alt="product Image">
                @else
                    <img src="{{ asset('dummy/image.jpg') }}" alt="product Image">
                @endif

            </td>
            <td>{{ $data->name ?? '' }}</td>
            <td>
                {{ $data->category->name ?? '' }}
            </td>
            <td>{{ $data->price ?? 0 }}</td>
            {{-- purchase --}}
            @php
                $purchaseItems = App\Models\PurchaseItem::where('product_id', $data->id)->get();
                $totalPurchase = $purchaseItems->sum('quantity');

                $saleItems = App\Models\SaleItem::where('product_id', $data->id)->get();
                $totalSalePrice = $saleItems->sum('sub_total');
                $totalsaleQuantity = $saleItems->sum('qty');
                $totalCost = $data->cost * $totalsaleQuantity;
                $totalProfit = $totalSalePrice - $totalCost;

                $totalDamage = $data->damage->sum('qty');
            @endphp
            <td>
                {{ $totalPurchase ?? 0 }} {{ $data->unit->name }}
            </td>

            {{-- sold  --}}
            <td>
                {{ $data->total_sold ?? 0 }} {{ $data->unit->name }}
            </td>

            {{-- damage  --}}
            <td>
                {{ $totalDamage ?? 0 }} {{ $data->unit->name }}
            </td>
            <td>
                ৳ {{ $data->price ?? 0 }}
            </td>
            <td>
                @if ($data->stock < 10)
                    <span class="text-danger"> {{ $data->stock ?? 0 }} {{ $data->unit->name }}</span>
                @else
                    {{ $data->stock ?? 0 }} {{ $data->unit->name }}
                @endif
            </td>
            <td>
                ৳ {{ $totalSalePrice ?? 0 }}
            </td>
            <td>
                ৳ {{ $totalProfit ?? 0 }}
            </td>
            <td class="id">
                <div class="dropdown">
                    <button class="btn btn-warning dropdown-toggle" type="button" id="dropdownMenuButton1"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Manage
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                        <a class="dropdown-item" href="{{ route('sale.invoice', $data->id) }}"><i
                                class="fa-solid fa-file-invoice me-2"></i> Invoice</a>
                        <a class="dropdown-item " href="{{ route('sale.view.details', $data->id) }}"><i
                                class="fa-solid fa-eye me-2"></i> Show</a>
                        {{-- <a class="dropdown-item" href="#"><i style="transform: rotate(90deg);"
                                class="fa-solid fa-arrow-turn-down me-2"></i></i>
                            Return</a> --}}
                        @if ($data->due > 0)
                            <a class="dropdown-item add_payment" href="#" data-bs-toggle="modal"
                                data-bs-target="#paymentModal" data-id="{{ $data->id }}"><i
                                    class="fa-solid fa-credit-card me-2"></i> Payment</a>
                        @endif
                        {{-- <a class="dropdown-item" href="{{ route('sale.edit', $data->id) }}"><i
                                class="fa-solid fa-pen-to-square me-2"></i> Edit</a> --}}
                        <a class="dropdown-item" id="delete" href="{{ route('sale.destroy', $data->id) }}"><i
                                class="fa-solid fa-trash-can me-2"></i>Delete</a>
                    </div>
                </div>
            </td>
        </tr>
    @endforeach
@else
    <tr>
        <td colspan="9"> No Data Found</td>
    </tr>
@endif
