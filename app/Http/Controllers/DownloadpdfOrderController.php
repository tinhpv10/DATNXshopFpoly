<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class DownloadpdfOrderController extends Controller
{
    public function index(Order $record)
    {
        set_time_limit(120); // Tăng thời gian thực thi lên 120 giây

        $pdf = Pdf::loadView('invoice-pdf.invoice',compact('record'));
        return $pdf->stream();
    }
}
