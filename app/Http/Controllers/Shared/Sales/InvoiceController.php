<?php

namespace App\Http\Controllers\Shared\Sales;

use App\Http\Controllers\Controller;
use App\Models\SalesInvoice;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function generateSalesInvoicePdf(SalesInvoice $invoice)
    {
        $invoice->load('items.product', 'store', 'marketer');
        
        $pdf = Pdf::loadView('shared.sales.invoice-pdf', compact('invoice'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream("invoice-{$invoice->invoice_number}.pdf");
    }
}
