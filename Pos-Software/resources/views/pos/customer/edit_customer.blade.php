@extends('master')
@section('title', '| Edit Customer')
@section('admin')
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card d-flex justify-content-end">
            <div class="">
                <h4 class="text-right"><a href="{{ route('customer.view') }}" class="btn btn-info">View All Customer</a></h4>
            </div>
        </div>
        <div class="col-md-12 stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title text-info">Edit Customer</h6>
                    <form id="myValidForm" action="{{ route('customer.update', $customer->id) }}" method="post">
                        @csrf
                        <div class="row">
                            <!-- Col -->
                            <div class="col-sm-4">
                                <div class="mb-3 form-valid-groups">
                                    <label class="form-label"> Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" value="{{ $customer->name }}"
                                        class="form-control field_required" placeholder="Enter Customer name">
                                </div>
                            </div><!-- Col -->
                            <div class="col-sm-4">
                                <div class="mb-3 form-valid-groups">
                                    <label class="form-label">Phone<span class="text-danger">*</span></label>
                                    <input type="text" name="phone" value="{{ $customer->phone }}" class="form-control"
                                        placeholder="Enter Customer Phone">
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="mb-3 form-valid-groups">
                                    <label class="form-label">Business Name</label>
                                    <input type="text" name="business_name" class="form-control"
                                        placeholder="Enter Business Name" value="{{ $customer->business_name }}">
                                </div>
                            </div><!-- Col -->
                            <div class="col-sm-4">
                                <div class="mb-3">
                                    <label class="form-label">Email address</label>
                                    <input type="email" name="email" value="{{ $customer->email }}" class="form-control"
                                        placeholder="Enter Customer email">
                                </div>
                            </div><!-- Col -->
                            <div class="col-sm-4">
                                <div class="mb-3">
                                    <label class="form-label">Customer Address</label>
                                    <input name="address" class="form-control" placeholder="Write Customer Address"
                                        value="{{ $customer->address }}" />
                                </div>
                            </div><!-- Col -->
                            <div class="col-sm-4">
                                <div class="mb-3">
                                    <label class="form-label">Customer Type <span
                                            class="text-danger">*</span></label></label>
                                    <select name="customer_type" class="form-control">
                                        <option value="">Select Customer Type</option>
                                        <option value="transport_owner"
                                            {{ $customer->customer_type == 'transport_owner' ? 'selected' : '' }}>Transport
                                            Owner</option>
                                        <option value="technician"
                                            {{ $customer->customer_type == 'technician' ? 'selected' : '' }}>Technician
                                        </option>
                                        <option value="floating"
                                            {{ $customer->customer_type == 'floating' ? 'selected' : '' }}>Floating</option>
                                    </select>
                                </div>
                            </div>
                        </div><!-- Row -->
                        <input type="submit" class="btn btn-primary submit" value="Update">
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#myValidForm').validate({
                rules: {
                    name: {
                        required: true,
                    },
                    phone: {
                        required: true,
                    },
                    customer_type: {
                        required: true,
                    },
                },
                messages: {
                    name: {
                        required: 'Please Enter Customer Name',
                    },
                    phone: {
                        required: 'Please Enter Customer Phone Number',
                    },
                    customer_type: {
                        required: 'Please Select Customer Type',
                    },
                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-valid-groups').append(error);
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                    $(element).addClass('is-valid');
                },
            });
        });
    </script>
@endsection
