<nav class="sidebar">
    <div class="sidebar-header">
        {{-- <a href="{{ route('dashboard') }}" class="sidebar-brand">
            @if (!empty($logo))
                <img src="{{ asset('/') . $logo }}" alt="" height="40">
            @else
                EIL<span>POS</span>
            @endif --}}
        <img src="{{ asset('/Logo-2.png') }}" alt="" height="40">
        </a>
        <div class="sidebar-toggler not-active">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>

    <style>
        .nav_active {
            background: #00a9f1;
            border-radius: 5px;
            color: #fff !important;
        }

        .nav-link.nav_active .link-icon,
        .nav-link.nav_active .link-title,
        .nav-link.nav_active {
            color: #ffffff !important;

        }

        .sub-menu .nav-item .nav-link {
            padding-left: 5px !important;
        }
    </style>
    <div class="sidebar-body">
        <ul class="nav">
            <li class="nav-item nav-category">theme</li>
            <li class="nav-item">
                <div class="form-valid-groups">
                    @php
                        $mode = App\models\PosSetting::all()->first();
                    @endphp
                    <form action="{{ route('switch_mode') }}" method="POST" id="darkModeForm">
                        @csrf
                        <div class="form-check form-switch">
                            <input class="form-check-input flexSwitchCheckDefault" type="checkbox"
                                {{ $mode->dark_mode == 2 ? 'checked' : '' }} name="dark_mode" role="switch"
                                id="flexSwitchCheckDefault">
                            <label class="form-check-label" for="flexSwitchCheckDefault">Dark Mode</label>
                        </div>
                    </form>
                </div>
            </li>
            <li class="nav-item nav-category">Main</li>

            <li class="nav-item">
                <a href="{{ route('dashboard') }}"
                    class="nav-link {{ request()->routeIs('dashboard') ? 'nav_active' : '' }}">
                    <i class="ms-2 link-icon" data-feather="home"></i>
                    <span class="link-title">Dashboard</span>
                </a>
            </li>

            @if (Auth::user()->can('pos.menu'))
                <li class="nav-item">
                    <a href="{{ route('sale') }}" class="nav-link {{ request()->routeIs('sale') ? 'nav_active' : '' }}">
                        <i class="ms-2 link-icon" data-feather="shopping-cart"></i>
                        <span class="link-title">POS</span>
                    </a>
                </li>
            @endif
            @if (Auth::user()->can('pos-manage.menu'))
                <li class="nav-item">
                    <a href="{{ route('sale.view') }}"
                        class="nav-link {{ request()->routeIs('sale.view') ? 'nav_active' : '' }}">
                        <i class="ms-2 link-icon" data-feather="shopping-bag"></i>
                        <span class="link-title">POS Manage</span>
                    </a>
                </li>
            @endif
            @if (Auth::user()->can('products.menu'))
                <li class="nav-item nav-category">Products</li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('product*') ? '' : 'collapsed' }}"
                        data-bs-toggle="collapse" href="#emails" role="button" aria-expanded="false"
                        aria-controls="emails">
                        <i class="ms-2 link-icon" data-feather="mail"></i>
                        <span class="link-title">Products</span>
                        <i class="link-arrow" data-feather="chevron-down"></i>
                    </a>
                    <div class="collapse {{ request()->routeIs('product*') ? 'show' : '' }}" id="emails">
                        <ul class="nav sub-menu">
                            @if (Auth::user()->can('products.add'))
                                <li class="nav-item ">
                                    <a href="{{ route('product') }}"
                                        class="nav-link {{ request()->routeIs('product') ? 'nav_active' : '' }}">Add
                                        Product</a>
                                </li>
                            @endif
                            @if (Auth::user()->can('products.list'))
                                <li class="nav-item">
                                    <a href="{{ route('product.view') }}"
                                        class="nav-link {{ request()->routeIs('product.view') ? 'nav_active' : '' }}">Manage
                                        Products</a>
                                </li>
                            @endif
                            <li class="nav-item">
                                <a href="{{ route('product.via') }}"
                                    class="nav-link {{ request()->routeIs('product.via') ? 'nav_active' : '' }}">Via
                                    Products</a>
                            </li>
                            @if (Auth::user()->can('category.menu'))
                                <li class="nav-item">
                                    <a href="{{ route('product.category') }}"
                                        class="nav-link {{ request()->routeIs('product.category') ? 'nav_active' : '' }}">Category</a>
                                </li>
                            @endif
                            @if (Auth::user()->can('subcategory.menu'))
                                <li class="nav-item">
                                    <a href="{{ route('product.subcategory') }}"
                                        class="nav-link {{ request()->routeIs('product.subcategory') ? 'nav_active' : '' }}">Sub
                                        Category</a>
                                </li>
                            @endif
                            @if (Auth::user()->can('brand.menu'))
                                <li class="nav-item">
                                    <a href="{{ route('product.brand') }}"
                                        class="nav-link {{ request()->routeIs('product.brand') ? 'nav_active' : '' }}">Brand</a>
                                </li>
                            @endif
                            @if (Auth::user()->can('unit.menu'))
                                <li class="nav-item">
                                    <a href="{{ route('product.unit') }}"
                                        class="nav-link {{ request()->routeIs('product.unit') ? 'nav_active' : '' }}">Unit</a>
                                </li>
                            @endif
                            @if (Auth::user()->can('products-size.menu'))
                                <li class="nav-item">
                                    <a href="{{ route('product.size.view') }}"
                                        class="nav-link {{ request()->routeIs('product.size.view') ? 'nav_active' : '' }}">Product
                                        Size</a>
                                </li>
                            @endif
                            @if (Auth::user()->can('tax.menu'))
                                <li class="nav-item">
                                    <a href="{{ route('product.tax.add') }}"
                                        class="nav-link {{ request()->routeIs('product.tax.add') ? 'nav_active' : '' }}">Tax</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </li>
            @endif

            <li class="nav-item nav-category">Inventory</li>

            @if (Auth::user()->can('supplier.menu'))
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('supplier') ? 'nav_active' : '' }}"
                        href="{{ route('supplier') }}" role="button" aria-controls="general-pages">
                        <i class="ms-2 fa-solid fa-handshake link-icon"></i>
                        <span class="link-title">Supplier</span>
                    </a>
                </li>
            @endif
            @if (Auth::user()->can('purchase.menu'))
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('purchase*') ? '' : 'collapsed' }}"
                        data-bs-toggle="collapse" href="#uiComponen" role="button" aria-expanded="false"
                        aria-controls="uiComponen">
                        <i class="ms-2 fa-solid fa-cart-arrow-down link-icon"></i>
                        <span class="link-title">Purchase</span>
                        <i class="link-arrow" data-feather="chevron-down"></i>
                    </a>
                    <div class="collapse {{ request()->routeIs('purchase*') ? 'show' : '' }}" id="uiComponen">
                        <ul class="nav sub-menu">
                            @if (Auth::user()->can('purchase.add'))
                                <li class="nav-item">
                                    <a href="{{ route('purchase') }}"
                                        class="nav-link {{ request()->routeIs('purchase') ? 'nav_active' : '' }}">Add
                                        Purchase</a>
                                </li>
                            @endif
                            @if (Auth::user()->can('purchase.list'))
                                <li class="nav-item">
                                    <a href="{{ route('purchase.view') }}"
                                        class="nav-link {{ request()->routeIs('purchase.view') ? 'nav_active' : '' }}">Manage
                                        Purchase</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </li>
            @endif
            <li class="nav-item">
                <a href="{{ route('via.sale') }}"
                    class="nav-link {{ request()->routeIs('via.sale') ? 'nav_active' : '' }}">
                    <i class="ms-2 link-icon" data-feather="columns"></i>
                    <span class="link-title">Via Sale</span>
                </a>
            </li>
            @if (Auth::user()->can('promotion.menu'))
                <li class="nav-item">
                    <a href="{{ route('promotion.view') }}"
                        class="nav-link {{ request()->routeIs('promotion.view') ? 'nav_active' : '' }}">
                        <i class="ms-2 fa-solid fa-tag link-icon"></i>
                        <span class="link-title">Promotion</span>
                    </a>
                </li>
            @endif
            @if (Auth::user()->can('promotion-details.menu'))
                <li class="nav-item">
                    <a href="{{ route('promotion.details.view') }}"
                        class="nav-link {{ request()->routeIs('promotion.details.view') ? 'nav_active' : '' }}">
                        <i class="ms-2 fa-solid fa-tags link-icon"></i>
                        <span class="link-title">Promotion Details</span>
                    </a>
                </li>
            @endif
            @if (Auth::user()->can('damage.menu'))
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('damage') ? 'nav_active' : '' }}"
                        href="{{ route('damage') }}" role="button" aria-controls="general-pages">
                        <i class="ms-2 link-icon" data-feather="book"></i>
                        <span class="link-title">Damage</span>
                    </a>
                </li>
            @endif

            @if (Auth::user()->can('return.menu'))
                <li class="nav-item">
                    <a href="{{ route('return.products.list') }}"
                        class="nav-link {{ request()->routeIs('return.products.list') ? 'nav_active' : '' }}">
                        <i class="ms-2 link-icon" data-feather="corner-down-right"></i>
                        <span class="link-title">All
                            return</span>
                    </a>
                </li>
            @endif
            @if (Auth::user()->can('bank.menu'))
                <li class="nav-item">
                    <a href="{{ route('bank') }}"
                        class="nav-link {{ request()->routeIs('bank') ? 'nav_active' : '' }}">
                        <i class="ms-2 fa-solid fa-building-columns link-icon"></i>
                        <span class="link-title">Bank</span>
                    </a>
                </li>
            @endif
            @if (Auth::user()->can('expense.menu'))
                <li class="nav-item">
                    <a href="{{ route('expense.view') }}"
                        class="nav-link {{ request()->routeIs('expense.view') ? 'nav_active' : '' }}">
                        <i class="ms-2 fa-solid fa-money-bill-transfer link-icon"></i>
                        <span class="link-title">Expense</span>
                    </a>
                </li>
            @endif
            @if (Auth::user()->can('transaction.menu'))
                <li class="nav-item">
                    <a href="{{ route('transaction.add') }}"
                        class="nav-link {{ request()->routeIs('transaction.add') ? 'nav_active' : '' }}">
                        <i class="ms-2 fa-solid fa-receipt link-icon"></i>
                        <span class="link-title">Transaction</span>
                    </a>
                </li>
            @endif
            <li class="nav-item nav-category">PEOPLES</li>
            @if (Auth::user()->can('customer.menu'))
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('customer.view') ? 'nav_active' : '' }}"
                        href="{{ route('customer.view') }}" role="button" aria-controls="general-pages">
                        <i class="ms-2 link-icon" data-feather="users"></i>
                        <span class="link-title">Customer</span>
                    </a>
                </li>
            @endif
            <li class="nav-item">
                @if (Auth::user()->can('employee.menu'))
                    <a class="nav-link {{ request()->routeIs('employee*') ? '' : 'collapsed' }}"
                        data-bs-toggle="collapse" href="#employee" role="button" aria-expanded="false"
                        aria-controls="emails">
                        <i class="ms-2 link-icon" data-feather="mail"></i>
                        <span class="link-title">Employee</span>
                        <i class="link-arrow" data-feather="chevron-down"></i>
                    </a>
                    <div class="collapse {{ request()->routeIs('employee*') ? 'show' : '' }}" id="employee">
                        <ul class="nav sub-menu">

                            <li class="nav-item ">
                                <a href="{{ route('employee.add') }}"
                                    class="nav-link {{ request()->routeIs('employee.add') ? 'nav_active' : '' }}">Add
                                    Employee</a>
                            </li>

                            @if (Auth::user()->can('employee-salary.menu'))
                                <li class="nav-item">
                                    <a href="{{ route('employee.salary.add') }}"
                                        class="nav-link {{ request()->routeIs('employee.salary.add') ? 'nav_active' : '' }}">Add
                                        Employee Salary</a>
                                </li>
                            @endif
                            {{-- @if (Auth::user()->can('advanced-employee-salary.menu'))
                            <li class="nav-item">
                                <a href="{{ route('advanced.employee.salary.add') }}"
                                    class="nav-link {{ request()->routeIs('advanced.employee.salary.add') ? 'nav_active' : '' }}">Add
                                    Advanced
                                    Employee Salary</a>
                            </li>
                        @endif --}}
                        </ul>
                    </div>
                @endif
            </li>

            @if (Auth::user()->can('crm.menu'))
                <li class="nav-item nav-category">Customer Info. Management</li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('crm*') ? '' : 'collapsed' }}" data-bs-toggle="collapse"
                        href="#advancedUI" role="button" aria-expanded="false" aria-controls="advancedUI">
                        <i class="ms-2 link-icon" data-feather="anchor"></i>
                        <span class="link-title">CRM</span>
                        <i class="link-arrow" data-feather="chevron-down"></i>
                    </a>
                    <div class="collapse {{ request()->routeIs('crm*') ? 'show' : '' }}" id="advancedUI">
                        <ul class="nav sub-menu">
                            @if (Auth::user()->can('crm.customize-customer'))
                                <li class="nav-item">
                                    <a href="{{ route('crm.customer.list.view') }}"
                                        class="nav-link {{ request()->routeIs('crm.customer.list.view') ? 'nav_active' : '' }}">Customize
                                        Customer</a>
                                </li>
                            @endif
                            @if (Auth::user()->can('crm.email-marketing'))
                                <li class="nav-item">
                                    <a href="{{ route('crm.email.To.Customer.Page') }}"
                                        class="nav-link {{ request()->routeIs('crm.email.To.Customer.Page') ? 'nav_active' : '' }}">Email
                                        Marketing</a>
                                </li>
                            @endif
                            @if (Auth::user()->can('crm.sms-marketing'))
                                <li class="nav-item">
                                    <a href="{{ route('crm.sms.To.Customer.Page') }}"
                                        class="nav-link {{ request()->routeIs('crm.sms.To.Customer.Page') ? 'nav_active' : '' }}">SMS
                                        Marketing</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </li>
            @endif
            @if (Auth::user()->can('report.menu'))
                <li class="nav-item nav-category">All Reports</li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('report*') ? '' : 'collapsed' }}"
                        data-bs-toggle="collapse" href="#majid" role="button" aria-expanded="false"
                        aria-controls="forms">
                        <i class="ms-2 link-icon" data-feather="inbox"></i>
                        <span class="link-title">Reports</span>
                        <i class="link-arrow" data-feather="chevron-down"></i>
                    </a>
                    <div class="collapse {{ request()->routeIs('report*') ? 'show' : '' }}" id="majid">
                        <ul class="nav sub-menu">
                            <li class="nav-item">
                                <a id="report" href="{{ route('report.today') }}"
                                    class="nav-link {{ request()->routeIs('report.today') ? 'nav_active' : '' }}">Today
                                    Report</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('report.product.info') }}"
                                    class="nav-link {{ request()->routeIs('report.product.info') ? 'nav_active' : '' }}">Product
                                    Info
                                    Report</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('report.summary') }}"
                                    class="nav-link {{ request()->routeIs('report.summary') ? 'nav_active' : '' }}">Summary
                                    Report</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('report.customer.due') }}"
                                    class="nav-link {{ request()->routeIs('report.customer.due') ? 'nav_active' : '' }}">Customer
                                    Due
                                    Report</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('report.supplier.due') }}"
                                    class="nav-link {{ request()->routeIs('report.supplier.due') ? 'nav_active' : '' }}">Supplier
                                    Due
                                    Report</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('report.low.stock') }}"
                                    class="nav-link {{ request()->routeIs('report.low.stock') ? 'nav_active' : '' }}">Low
                                    Stock Report</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('report.top.products') }}"
                                    class="nav-link {{ request()->routeIs('report.top.products') ? 'nav_active' : '' }}">Top
                                    Products</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('report.purchase') }}"
                                    class="nav-link {{ request()->routeIs('report.purchase') ? 'nav_active' : '' }}">Purchase
                                    Report</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('report.customer.ledger') }}"
                                    class="nav-link {{ request()->routeIs('report.customer.ledger') ? 'nav_active' : '' }}">Customer
                                    Ledger</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('report.suppliers.ledger') }}"
                                    class="nav-link {{ request()->routeIs('report.suppliers.ledger') ? 'nav_active' : '' }}">Supplier
                                    Ledger</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('report.account.transaction') }}"
                                    class="nav-link {{ request()->routeIs('report.account.transaction') ? 'nav_active' : '' }}">Account
                                    Transaction</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('report.expense') }}"
                                    class="nav-link {{ request()->routeIs('report.expense') ? 'nav_active' : '' }}">Expense
                                    Report</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('report.employee.salary.view') }}"
                                    class="nav-link {{ request()->routeIs('report.employee.salary.view') ? 'nav_active' : '' }}">Employee
                                    Salary
                                    Report</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('report.stock') }}"
                                    class="nav-link {{ request()->routeIs('report.stock') ? 'nav_active' : '' }}">Stock
                                    Report</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('report.damage') }}"
                                    class="nav-link {{ request()->routeIs('report.damage') ? 'nav_active' : '' }}">Damage
                                    Report</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('report.sms') }}"
                                    class="nav-link {{ request()->routeIs('report.sms') ? 'nav_active' : '' }}">Sms
                                    Report</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('report.monthly') }}"
                                    class="nav-link {{ request()->routeIs('report.monthly') ? 'nav_active' : '' }}">Monthly
                                    Report</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('report.yearly') }}"
                                    class="nav-link {{ request()->routeIs('report.yearly') ? 'nav_active' : '' }}">Yearly
                                    Report</a>
                            </li>
                        </ul>
                    </div>
                </li>
            @endif

            <li class="nav-item nav-category">SETTING & CUSTOMIZE</li>
            <!---Role & Permission--->
            @if (Auth::user()->can('role-and-permission.menu'))
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('role*') ? 'collapsed' : '' }}"
                        data-bs-toggle="collapse" href="#role_permission" role="button" aria-expanded="false"
                        aria-controls="role_permission">
                        <i class="ms-2 fa-solid fa-users-gear link-icon"></i>
                        <span class="link-title">Role & Permission</span>
                        <i class="link-arrow" data-feather="chevron-down"></i>
                    </a>
                    <div class="collapse {{ request()->routeIs('role*') ? 'show' : '' }}" id="role_permission">
                        <ul class="nav sub-menu">
                            @if (Auth::user()->can('role-and-permission.all-permission'))
                                <li class="nav-item">
                                    <a href="{{ route('all.permission') }}"
                                        class="nav-link {{ request()->routeIs('all.permission') ? 'nav_active' : '' }}">All
                                        Permisiion</a>
                                </li>
                            @endif
                            @if (Auth::user()->can('role-and-permission.all-role'))
                                <li class="nav-item">
                                    <a href="{{ route('all.role') }}"
                                        class="nav-link {{ request()->routeIs('all.role') ? 'nav_active' : '' }}">All
                                        Role</a>
                                </li>
                            @endif
                            @if (Auth::user()->can('role-and-permission.role-in-permission'))
                                <li class="nav-item">
                                    <a href="{{ route('add.role.permission') }}"
                                        class="nav-link {{ request()->routeIs('add.role.permission') ? 'nav_active' : '' }}">Role
                                        In
                                        Permission</a>
                                </li>
                            @endif
                            @if (Auth::user()->can('role-and-permission-check-role-permission'))
                                <li class="nav-item">
                                    <a href="{{ route('all.role.permission') }}"
                                        class="nav-link {{ request()->routeIs('all.role.permission') ? 'nav_active' : '' }}">Check
                                        All Role
                                        Permission</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </li>
            @endif

            <!---Admin Manage--->
            @if (Auth::user()->can('admin-manage.menu'))
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin*') ? '' : 'collapsed' }}"
                        data-bs-toggle="collapse" href="#admin-manage" role="button" aria-expanded="false"
                        aria-controls="emails">
                        <i class="ms-2 fa-solid fa-users-gear link-icon"></i>
                        <span class="link-title">Admin Manage</span>
                        <i class="link-arrow" data-feather="chevron-down"></i>
                    </a>
                    <div class="collapse {{ request()->routeIs('admin*') ? 'show' : '' }}" id="admin-manage">
                        <ul class="nav sub-menu">
                            @if (Auth::user()->can('admin-manage.list'))
                                <li class="nav-item">
                                    <a href="{{ route('admin.all') }}"
                                        class="nav-link {{ request()->routeIs('admin.all') ? 'nav_active' : '' }}">All
                                        Admin</a>
                                </li>
                            @endif
                            @if (Auth::user()->can('admin-manage.add'))
                                <li class="nav-item">
                                    <a href="{{ route('admin.add') }}"
                                        class="nav-link {{ request()->routeIs('admin.add') ? 'nav_active' : '' }}">Add
                                        Admin</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </li>
            @endif
            <!---Admin Manage--->
            @if (Auth::user()->can('settings.menu'))
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('setting*') ? '' : 'collapsed' }}"
                        data-bs-toggle="collapse" href="#setting-manage" role="button" aria-expanded="false"
                        aria-controls="emails">
                        <i class="ms-2 link-icon" data-feather="settings"></i>
                        <span class="link-title">Setting Manage</span>
                        <i class="link-arrow" data-feather="chevron-down"></i>
                    </a>
                    <div class="collapse {{ request()->routeIs('setting*') ? 'show' : '' }}" id="setting-manage">
                        <ul class="nav sub-menu">
                            <li class="nav-item">
                                <a href="{{ route('pos.settings.add') }}"
                                    class="nav-link {{ request()->routeIs('pos.settings.add') ? 'nav_active' : '' }}">
                                    <span class="link-title">Settings</span>
                                </a>
                            </li>
                            <!--<li class="nav-item">-->
                            <!--    <a href="{{ route('invoice.settings') }}"-->
                            <!--        class="nav-link {{ request()->routeIs('invoice.settings') ? 'nav_active' : '' }}">-->
                            <!--        <span class="link-title">Invoice-1</span>-->
                            <!--    </a>-->
                            <!--</li>-->
                            <!--<li class="nav-item">-->
                            <!--    <a href="{{ route('invoice2.settings') }}"-->
                            <!--        class="nav-link {{ request()->routeIs('invoice2.settings') ? 'nav_active' : '' }}">-->
                            <!--        <span class="link-title">Invoice-2</span>-->
                            <!--    </a>-->
                            <!--</li>-->
                            <!--<li class="nav-item">-->
                            <!--    <a href="{{ route('invoice3.settings') }}"-->
                            <!--        class="nav-link {{ request()->routeIs('invoice3.settings') ? 'nav_active' : '' }}">-->
                            <!--        <span class="link-title">Invoice-3</span>-->
                            <!--    </a>-->
                            <!--</li>-->
                            <!--<li class="nav-item">-->
                            <!--    <a href="{{ route('invoice4.settings') }}"-->
                            <!--        class="nav-link {{ request()->routeIs('invoice4.settings') ? 'nav_active' : '' }}">-->
                            <!--        <span class="link-title">Invoice-4</span>-->
                            <!--    </a>-->
                            <!--</li>-->

                        </ul>
                    </div>
                </li>
            @endif

            @if (Auth::user()->can('branch.menu'))
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('branch.view') ? 'nav_active' : '' }}"
                        href="{{ route('branch.view') }}" role="button" aria-controls="general-pages">
                        <i class="ms-2 link-icon" data-feather="sliders"></i>
                        <span class="link-title">Branches</span>
                    </a>
                </li>
            @endif
        </ul>
    </div>
</nav>
