@if ($sales->count() > 0)
    @foreach ($sales as $index => $data)
        <tr>
            <td class="id">{{ $index + 1 }}</td>
            <td>
                <a href="{{ route('sale.invoice', $data->id) }}">
                    #{{ $data->invoice_number ?? 0 }}
                </a>
            </td>
            <td>
                <a href="{{ route('customer.profile', $data->customer->id) }}">
                    {{ $data->customer->name ?? '' }}
                </a>
            </td>
            <td>
                @php
                    $totalItems = $data->saleItem->count();
                    $displayItems = $data->saleItem->take(5);
                    $remainingItems = $totalItems - 5;
                @endphp
                <ul>
                    @foreach ($displayItems as $items)
                        <li>
                            <a
                                href="{{ route('product.ledger', $items->product_id) }}">{{ $items->product->name ?? '' }}</a>
                        </li>
                    @endforeach

                    @if ($totalItems > 5)
                        <li>and more {{ $remainingItems }}...</li>
                    @endif
                </ul>
            </td>
            <td>{{ $data->quantity ?? 0 }}</td>
            <td>{{ $data->sale_date ?? 0 }}</td>
            <td>৳ {{ $data->total ?? 0 }}</td>
            <td>৳ {{ $data->actual_discount ?? 0 }}</td>
            <td>৳
                {{ $data->receivable - $data->change_amount ?? 0 }}
            </td>
            <td>
                ৳ {{ $data->receivable ?? 0 }}
            </td>

            <td>
                ৳ {{ $data->paid ?? 0 }}
            </td>
            <td>
                @if ($data->returned > 0)
                    Yes <br>
                    {{ $data->returned }}
                @else
                    No
                @endif
            </td>
            <td>
                @if ($data->due > 0)
                    <span class="text-danger">৳ {{ $data->due ?? 0 }}</span>
                @else
                    ৳ {{ $data->due ?? 0 }}
                @endif
            </td>
            <td> ৳
                @php
                    $totalCost = 0;
                @endphp
                @foreach ($data->saleItem as $item)
                    @php
                        $totalCost += $item->product->cost ?? 0;
                    @endphp
                @endforeach
                {{ $totalCost }}
            </td>
            <td>
                ৳ @php
                    $totalSale = 0;
                @endphp
                @foreach ($data->saleItem as $item)
                    @php
                        $totalSale += $item->product->price ?? 0;
                    @endphp
                @endforeach
                {{ $totalSale - $totalCost }}
            </td>
            <td>
                @if ($data->due <= 0)
                    <span class="badge bg-success">Paid</span>
                @else
                    <span class="badge bg-warning">Unpaid</span>
                @endif
            </td>
            <td class="id">
                <div class="dropdown">
                    <button class="btn btn-warning dropdown-toggle" type="button" id="dropdownMenuButton1"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Manage
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                        @if (Auth::user()->can('pos-manage.invoice'))
                            <a class="dropdown-item" href="{{ route('sale.invoice', $data->id) }}"><i
                                    class="fa-solid fa-file-invoice me-2"></i> Invoice</a>
                        @endif
                        @if ($data->returned == 0)
                            <a class="dropdown-item" href="{{ route('return', $data->id) }}"><i
                                    style="transform: rotate(90deg);" class="fa-solid fa-arrow-turn-down me-2"></i></i>
                                Return</a>
                        @endif
                        {{-- @if (Auth::user()->can('pos-manage.delete'))
                            <a class="dropdown-item" id="delete" href="{{ route('sale.destroy', $data->id) }}"><i
                                    class="fa-solid fa-trash-can me-2"></i>Delete</a>
                        @endif --}}
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
