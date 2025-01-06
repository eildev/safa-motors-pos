<div class="d-flex justify-content-between align-items-center">
    <h6 class="card-title">Bank Account</h6>
    <button class="btn btn-rounded-primary btn-sm" data-bs-toggle="modal" data-bs-target="#stock_add_modal"><i
            data-feather="plus"></i></button>
</div>
<div class="mb-2">
    <p class="">Total Banks: <span class="total_banks"></span></p>
    <p class="">Total Opening Balance: <span class="total_initial_balance"></span></p>
    <p class="">Total Current Balance: <span class="total_current_balance"></span></p>
</div>
<div class="table-responsive">
    <table id="myTableExample" class="table">
        <thead>
            <tr>
                <th>Account Name</th>
                <th>Account Number</th>
                <th>Bank Name</th>
                <th>Branch Name</th>
                <th>Opening Balance</th>
                <th>Current Balance</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody class="show_bank_data">
        </tbody>
    </table>
</div>
