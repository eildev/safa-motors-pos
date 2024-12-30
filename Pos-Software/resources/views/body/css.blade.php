<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<meta name="description" content="EIL Responsive Admin Dashboard">
<meta name="author" content="NobleUI">
<meta name="keywords"
    content="nobleui, bootstrap, bootstrap 5, bootstrap5, admin, dashboard, template, responsive, css, sass, html, theme, front-end, ui kit, web">
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>{{ $siteTitle }} @yield('title')</title>

<!-- Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
<!-- End fonts -->

<!-- core:css -->
<link rel="stylesheet" href="{{ asset('assets') }}/vendors/core/core.css">
<!-- endinject -->

<!-- Plugin css for this page -->
<link rel="stylesheet" href="{{ asset('assets') }}/vendors/flatpickr/flatpickr.min.css">
<link rel="stylesheet" href="{{ asset('assets') }}/vendors/datatables.net-bs5/dataTables.bootstrap5.css">

<link rel="stylesheet" href="{{ asset('assets') }}/vendors/prismjs/themes/prism.css">
<link rel="stylesheet" href="{{ asset('assets') }}/vendors/select2/select2.min.css">
<link rel="stylesheet" href="{{ asset('assets') }}/vendors/jquery-tags-input/jquery.tagsinput.min.css">
<link rel="stylesheet" href="{{ asset('assets') }}/vendors/dropzone/dropzone.min.css">
<link rel="stylesheet" href="{{ asset('assets') }}/vendors/dropify/dist/dropify.min.css">
<link rel="stylesheet" href="{{ asset('assets') }}/vendors/pickr/themes/classic.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="{{ asset('assets') }}/vendors/sweetalert2/sweetalert2.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.css" />
<!-- End plugin css for this page -->

<!-- inject:css -->
<link rel="stylesheet" href="{{ asset('assets') }}/fonts/feather-font/css/iconfont.css">
<link rel="stylesheet" href="{{ asset('assets') }}/vendors/flag-icon-css/css/flag-icon.min.css">
<link rel="stylesheet" href="{{ asset('assets') }}/vendors/easymde/easymde.min.css">

<!-- endinject -->
@php
    $mode = App\models\PosSetting::all()->first();
@endphp
<!-- Layout styles -->
@if (empty($mode->dark_mode))
    <link rel="stylesheet" href="{{ asset('assets') }}/css/demo1/style.css">
@else
    <link rel="stylesheet" href="{{ asset('assets') }}/css/demo{{ $mode->dark_mode }}/style.css">
@endif



{{-- apex chart  --}}
<script src="{{ asset('assets') }}/vendors/apexcharts/apexcharts.min.js"></script>

<!-- End layout styles -->

<link rel="shortcut icon" href="{{ asset('assets') }}/images/favicon.svg" />
<style>
    .btn-rounded-primary {
        padding: 0.3rem;
        font-size: 8px;
        border-radius: 50%;
        color: #fff;
        background: #6571ff;
    }

    .btn-rounded-primary:hover {
        background: #5660d9;
        color: #fff;
    }

    /* //data table js */
    .dt-buttons {
        width: 60% !important;
        position: absolute !important;
        margin-bottom: 10px !important;
    }

    .dt-search {
        float: right;
    }

    .btn svg,
    .my_nav:hover {
        background: transparent !important;
    }

    .page-breadcrumb .breadcrumb {
        padding: 12px;
    }
</style>
{{-- jquery plugin  --}}

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css">
{{-- ///export CSS// --}}

<link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.dataTables.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.dataTables.css">
