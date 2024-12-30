@if ($returns->count() > 0)
    @foreach ($returns as $index => $data)
        <tr>
            <td class="id">{{ $index + 1 }}</td>
            <td>
                <a href="{{ route('return.products.invoice', $data->id) }}">
                    #{{ $data->return_invoice_number ?? 0 }}
                </a>

            </td>
            <td>
                {{-- {{ $data->customer->id ?? '' }} --}}
                <a href="{{ route('customer.profile', $data->customer->id ?? 0) }}">
                    {{ $data->customer->name ?? '' }}
                </a>

            </td>
            <td>

                @php
                    $totalItems = $data->returnItem->count();
                    $displayItems = $data->returnItem->take(5);
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
            <td>{{ $data->return_date ?? 'Date not Available' }}</td>
            <td>৳ {{ $data->refund_amount ?? 0 }}</td>
            <td>{{ $data->return_reason ?? 'Data not Available' }}</td>
            <td>
                ৳ {{ $data->total_return_profit ?? 0 }}
            </td>
            <td>
                ৳ {{ $data->processed_by ?? 0 }}
            </td>

        </tr>
    @endforeach
@else
    <tr>
        <td colspan="9"> No Data Found</td>
    </tr>
@endif
