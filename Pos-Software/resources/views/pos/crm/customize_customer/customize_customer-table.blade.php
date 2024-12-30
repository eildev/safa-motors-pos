<div class="row">
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
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Address</th>
                                        <th>Opening Receivable</th>
                                        <th>Opening Payable</th>
                                        <th>Wallet Balance</th>
                                        <th>Total Receivable</th>
                                        <th>Total Payable</th>
                                    </tr>
                                </thead>
                                <tbody class="showData">
                                @if ($customer->count() > 0)
                                @foreach ($customer as $key => $customer)
                                    <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $customer->name ?? ''}}</td>
                                    <td>{{ $customer->email ?? ''}}</td>
                                    <td>{{ $customer->phone ?? ''}}</td>
                                    <td>{{ $customer->address ?? ''}}</td>
                                    <td>{{ $customer->opening_receivable ?? ''}}</td>
                                    <td>{{ $customer->opening_payable ?? ''}}</td>
                                    <td>{{ $customer->wallet_balance ?? ''}}</td>
                                    <td>{{ $customer->total_receivable ?? ''}}</td>
                                    <td>{{ $customer->total_payable ?? ''}}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="12">
                                    <div class="text-center text-warning mb-2">Data Not Found</div>
                                    <div class="text-center">
                                       Data Not Found
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
