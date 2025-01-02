@extends('master')
@section('title', '| Customer List')
@section('admin')

    <div class="row">
        @if (Auth::user()->can('customer.add'))
            <div class="col-md-12 grid-margin stretch-card d-flex justify-content-end">
                <div class="">
                    <h4 class="text-right"><a href="{{ route('customer.add') }}" class="btn btn-info">Add New Customer</a></h4>
                </div>
            </div>
        @endif
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title text-info">View Customer List</h6>

                    <div id="" class="table-responsive">
                        <table id="example" class="table">
                            <thead>
                                <tr>
                                    <th>SN</th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Bussiness Name</th>
                                    <th>Due</th>
                                    <th>Customer Type</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="showData">
                                @if ($customers->count() > 0)
                                    @foreach ($customers as $key => $customer)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>
                                                <a href="{{ route('customer.profile', $customer->slug) }}">
                                                    {{ $customer->name ?? '' }}
                                                </a>
                                            </td>
                                            <td>{{ $customer->phone ?? '' }}</td>
                                            <td>{{ $customer->business_name ?? '' }}</td>
                                            <td>
                                                <span>
                                                    {{ abs($customer->due_balance) ?? 0 }}<br>
                                                    @if ($customer->due_balance > 0)
                                                        আপনি কাস্টমার থেকে পাবেন।
                                                    @elseif ($customer->due_balance < 0)
                                                        আপনার থেকে কাস্টমার পাবেন।
                                                    @else
                                                        --
                                                    @endif
                                                </span>
                                            </td>
                                            <td>{{ $customer->customer_type ?? '' }}</td>
                                            <td>
                                                @if (Auth::user()->can('customer.edit'))
                                                    <a href="{{ route('customer.edit', $customer->slug) }}"
                                                        class="btn btn-sm btn-primary btn-icon">
                                                        <i data-feather="edit"></i>
                                                    </a>
                                                @endif
                                                @if (Auth::user()->can('customer.delete'))
                                                    <a href="{{ route('customer.delete', $customer->id) }}" id="delete"
                                                        class="btn btn-sm btn-danger btn-icon">
                                                        <i data-feather="trash-2"></i>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="12">
                                            <div class="text-center text-warning mb-2">Data Not Found</div>
                                            <div class="text-center">
                                                <a href="{{ route('customer.add') }}" class="btn btn-primary">Add
                                                    Customer<i data-feather="plus"></i></a>
                                            </div>
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
@endsection
