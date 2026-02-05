<?php

namespace App\Http\Controllers\Marketer;

use App\Http\Controllers\Controller;
use App\Models\MarketerRequest;
use App\Models\Product;
use App\Services\Marketer\MarketerRequestService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class MarketerRequestController extends Controller
{
    public function __construct(private MarketerRequestService $service) 
    {
        // Temporary: Auto-login as marketer (ID=3)
        if (!Auth::check()) {
            Auth::loginUsingId(3);
        }
    }

    public function index()
    {
        $requests = MarketerRequest::with('items.product')
            ->where('marketer_id', auth()->id())
            ->latest()
            ->paginate(20);

        return view('marketer.requests.index', compact('requests'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)
            ->leftJoin('main_stock', 'products.id', '=', 'main_stock.product_id')
            ->select('products.*', 'main_stock.quantity as stock')
            ->get();
        return view('marketer.requests.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $marketerRequest = $this->service->createRequest(
            auth()->id(),
            $validated['items'],
            $validated['notes'] ?? null
        );

        return redirect()->route('marketer.requests.show', $marketerRequest)
            ->with('success', 'تم إنشاء الطلب بنجاح');
    }

    public function show(MarketerRequest $request)
    {
        return view('marketer.requests.show', ['request' => $request->load('items.product', 'approver', 'documenter')]);
    }

    public function cancel(MarketerRequest $request)
    {
        $this->service->cancelRequest($request->id, auth()->id());

        return redirect()->route('marketer.requests.index')
            ->with('success', 'تم إلغاء الطلب بنجاح');
    }

    public function pdf(MarketerRequest $request)
    {
        $request->load('items.product', 'marketer', 'approver');
        
        $arabic = new \ArPHP\I18N\Arabic();
        
        $toEnglishNumbers = function($str) {
            $western = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
            $eastern = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
            return str_replace($eastern, $western, $str);
        };

        $data = [
            'invoiceNumber' => $request->invoice_number,
            'date' => $request->created_at->format('Y-m-d H:i'),
            'marketerName' => $arabic->utf8Glyphs($request->marketer->full_name),
            'approvedBy' => $request->approver ? $arabic->utf8Glyphs($request->approver->full_name) : null,
            'items' => $request->items->map(function($item) use ($arabic, $toEnglishNumbers) {
                return (object)[
                    'name' => $toEnglishNumbers($arabic->utf8Glyphs($item->product->name)),
                    'quantity' => $item->quantity
                ];
            }),
            'title' => $arabic->utf8Glyphs('طلب بضاعة'),
            'labels' => [
                'marketer' => $arabic->utf8Glyphs('المسوق'),
                'date' => $arabic->utf8Glyphs('التاريخ'),
                'status' => $arabic->utf8Glyphs('الحالة'),
                'approved' => $arabic->utf8Glyphs('تم الموافقة'),
                'approvedBy' => $arabic->utf8Glyphs('اعتمد بواسطة'),
                'keeper' => $arabic->utf8Glyphs('أمين المخزن'),
                'product' => $arabic->utf8Glyphs('المنتج'),
                'quantity' => $arabic->utf8Glyphs('الكمية'),
                'total' => $arabic->utf8Glyphs('الإجمالي'),
            ]
        ];

        $pdf = Pdf::loadView('marketer.requests.invoice-pdf', $data)->setPaper('a4');
        return $pdf->download('request-' . $request->invoice_number . '.pdf');
    }
}
