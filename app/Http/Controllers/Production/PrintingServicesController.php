<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PrintingServicesController extends Controller
{
    public function requestPaymentToPrinter()
    {
        return view('production.printing.request-payment-to-printer');
    }
}
