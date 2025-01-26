<?php

namespace App\Http\Controllers\SaleController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function index()
    {

         return view('pos.safa-sale-page.sale');

    }
}
