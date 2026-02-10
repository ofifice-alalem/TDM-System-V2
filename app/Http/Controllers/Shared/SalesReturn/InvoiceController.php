<?php

namespace App\Http\Controllers\Shared\SalesReturn;

use App\Http\Controllers\Controller;
use App\Models\SalesReturn;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function generateSalesReturnInvoicePdf(SalesReturn $salesReturn)
    {
        $salesReturn->load(['store', 'marketer', 'salesInvoice', 'items.product', 'keeper']);
        
        $pdf = Pdf::loadView('shared.sales-returns.invoice-pdf', compact('salesReturn'))
            ->setPaper('a4', 'portrait');
        
        return $pdf->stream('sales-return-' . $salesReturn->return_number . '.pdf');
    }
}
