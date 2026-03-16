<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\Customer;
use App\Models\SalesInvoice;
use App\Models\StorePayment;
use App\Models\SalesReturn;
use App\Models\CustomerInvoice;
use App\Models\CustomerPayment;
use App\Models\CustomerReturn;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class CombinedSummaryController extends Controller
{
    public function index(Request $request)
    {
        $fromDate = $request->input('from_date', now()->startOfMonth()->format('Y-m-d'));
        $toDate   = $request->input('to_date', now()->format('Y-m-d'));

        $rows = $this->buildRows($fromDate, $toDate);

        if ($request->has('export')) {
            return $this->export($rows, $fromDate, $toDate);
        }

        $grandInvoices = $rows->sum('total_invoices');
        $grandPayments = $rows->sum('total_payments');
        $grandReturns  = $rows->sum('total_returns');
        $grandDebt     = $rows->sum('total_debt');

        return view('admin.combined-summary.index', compact(
            'rows', 'fromDate', 'toDate',
            'grandInvoices', 'grandPayments', 'grandReturns', 'grandDebt'
        ));
    }

    private function buildRows($fromDate, $toDate)
    {
        $rows = collect();

        // المتاجر
        foreach (Store::orderBy('name')->get() as $store) {
            $invoices = SalesInvoice::where('store_id', $store->id)->where('status', 'approved')
                ->whereDate('created_at', '>=', $fromDate)->whereDate('created_at', '<=', $toDate)
                ->sum('total_amount');
            $payments = StorePayment::where('store_id', $store->id)->where('status', 'approved')
                ->whereDate('created_at', '>=', $fromDate)->whereDate('created_at', '<=', $toDate)
                ->sum('amount');
            $returns = SalesReturn::where('store_id', $store->id)->where('status', 'approved')
                ->whereDate('created_at', '>=', $fromDate)->whereDate('created_at', '<=', $toDate)
                ->sum('total_amount');

            if ($invoices == 0 && $payments == 0 && $returns == 0) continue;

            $rows->push((object)[
                'name'           => $store->name,
                'type'           => 'متجر',
                'total_invoices' => $invoices,
                'total_payments' => $payments,
                'total_returns'  => $returns,
                'total_debt'     => $invoices - $payments - $returns,
            ]);
        }

        // العملاء
        foreach (Customer::orderBy('name')->get() as $customer) {
            $invoices = CustomerInvoice::where('customer_id', $customer->id)->where('status', 'completed')
                ->whereDate('created_at', '>=', $fromDate)->whereDate('created_at', '<=', $toDate)
                ->sum('total_amount');
            $payments = CustomerPayment::where('customer_id', $customer->id)->where('status', 'completed')
                ->whereDate('created_at', '>=', $fromDate)->whereDate('created_at', '<=', $toDate)
                ->sum('amount');
            $returns = CustomerReturn::where('customer_id', $customer->id)->where('status', 'completed')
                ->whereDate('created_at', '>=', $fromDate)->whereDate('created_at', '<=', $toDate)
                ->sum('total_amount');

            if ($invoices == 0 && $payments == 0 && $returns == 0) continue;

            $rows->push((object)[
                'name'           => $customer->name,
                'type'           => 'عميل',
                'total_invoices' => $invoices,
                'total_payments' => $payments,
                'total_returns'  => $returns,
                'total_debt'     => $invoices - $payments - $returns,
            ]);
        }

        return $rows->sortByDesc('total_debt')->values();
    }

    private function export($rows, $fromDate, $toDate)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setRightToLeft(true);

        $row = 1;

        // معلومات
        foreach ([['من تاريخ', $fromDate], ['إلى تاريخ', $toDate]] as $info) {
            $sheet->setCellValue('A' . $row, $info[0]);
            $sheet->setCellValue('B' . $row, $info[1]);
            $sheet->getStyle('A' . $row . ':B' . $row)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E3F2FD']],
                'font' => ['bold' => true, 'size' => 12],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
            $row++;
        }

        $row++;

        // رؤوس الأعمدة
        $headers = ['الاسم', 'النوع', 'إجمالي الفواتير', 'إجمالي المدفوعات', 'إجمالي المرتجعات', 'الدين الحالي'];
        $sheet->fromArray($headers, null, 'A' . $row);
        $sheet->getStyle('A' . $row . ':F' . $row)->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1565C0']],
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $row++;

        // البيانات
        foreach ($rows as $item) {
            $typeColor = $item->type === 'متجر' ? 'E3F2FD' : 'F3E5F5';
            $debtColor = $item->total_debt > 0 ? 'FFCDD2' : ($item->total_debt < 0 ? 'C8E6C9' : 'F5F5F5');

            $sheet->fromArray([
                $item->name,
                $item->type,
                number_format($item->total_invoices, 2),
                number_format($item->total_payments, 2),
                number_format($item->total_returns, 2),
                number_format($item->total_debt, 2),
            ], null, 'A' . $row);

            $sheet->getStyle('A' . $row . ':F' . $row)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
            $sheet->getStyle('B' . $row)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $typeColor]],
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
            $sheet->getStyle('F' . $row)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $debtColor]],
                'font' => ['bold' => true],
            ]);
            $row++;
        }

        // الإجماليات
        $row++;
        $sheet->fromArray([
            'الإجمالي', '',
            number_format($rows->sum('total_invoices'), 2),
            number_format($rows->sum('total_payments'), 2),
            number_format($rows->sum('total_returns'), 2),
            number_format($rows->sum('total_debt'), 2),
        ], null, 'A' . $row);
        $sheet->getStyle('A' . $row . ':F' . $row)->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E8EAF6']],
            'font' => ['bold' => true, 'size' => 12],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'الملخص_المالي_الشامل_' . $fromDate . '_' . $toDate . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        (new Xlsx($spreadsheet))->save('php://output');
        exit;
    }
}
