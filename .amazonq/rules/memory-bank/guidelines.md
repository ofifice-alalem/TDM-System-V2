# TDM System V2 - Development Guidelines

## Architecture Patterns

### Service Layer (Mandatory)
All business logic lives in `app/Services/{Role}/`. Controllers are thin wrappers.

```php
// Controller: inject service via constructor property promotion
class SalesController extends Controller
{
    public function __construct(private SalesService $service) {}

    public function store(Request $request)
    {
        $validated = $request->validate([...]);
        try {
            $result = $this->service->createInvoice(...);
            return redirect()->route('...')->with('success', 'رسالة نجاح');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }
}
```

### DB Transactions (Always for multi-table writes)
Every service method that touches multiple tables uses `DB::transaction()`:

```php
public function approveRequest($requestId, $keeperId)
{
    return DB::transaction(function () use ($requestId, $keeperId) {
        $record = Model::where('id', $requestId)
            ->where('status', 'pending')
            ->firstOrFail();
        // ... multi-table updates
        return $record;
    });
}
```

### Stock Manipulation Pattern
Use `DB::table()` (query builder, not Eloquent) for stock increment/decrement operations:

```php
DB::table('marketer_actual_stock')
    ->where('marketer_id', $marketerId)
    ->where('product_id', $productId)
    ->decrement('quantity', $qty);

DB::table('marketer_actual_stock')->updateOrInsert(
    ['marketer_id' => $marketerId, 'product_id' => $productId],
    ['quantity' => DB::raw("quantity + {$qty}")]
);
```

### Notification Pattern
Always notify affected users after status changes via `NotificationService`:

```php
$this->notificationService->create(
    $userId,
    'event_type_snake_case',
    'عنوان الإشعار',
    'نص الإشعار مع رقم ' . $record->invoice_number,
    route('role.resource.show', $record->id)
);
```

## Code Quality Standards

### Validation
Always validate in the controller before calling the service:

```php
$validated = $request->validate([
    'store_id'              => 'required|exists:stores,id',
    'items'                 => 'required|array|min:1',
    'items.*.product_id'    => 'required|exists:products,id',
    'items.*.quantity'      => 'required|integer|min:1',
    'notes'                 => 'nullable|string',
]);
```

### Authorization
Manual ownership checks in controllers (no Gates/Policies used):

```php
if ($sale->marketer_id != auth()->id()) {
    abort(403, 'غير مصرح لك بالوصول لهذه الفاتورة');
}
```

### Error Messages
Business logic errors are thrown as plain `\Exception` with Arabic messages:

```php
throw new \Exception("المنتج {$product->name} غير متوفر بالكمية المطلوبة (متوفر: {$available})");
```

### Model Conventions
- Use `$fillable` (not `$guarded`)
- Cast monetary values as `'decimal:2'`
- Cast booleans explicitly: `'is_active' => 'boolean'`
- Use `SoftDeletes` only where explicitly needed (currently: `users` table)
- Custom auth password field: `password_hash` (not `password`); override `getAuthPassword()`

```php
protected $casts = [
    'current_price' => 'decimal:2',
    'is_active'     => 'boolean',
];
```

### Relationship Naming
- `belongsTo` → singular snake_case: `role()`, `store()`, `marketer()`
- `hasMany` → plural snake_case: `items()`, `marketerRequests()`
- `hasOne` with constraints → named descriptively: `activePromotion()`

## Routing Conventions

### Route File Structure
Each role has its own route file, included from `web.php`:

```php
// routes/web.php
require __DIR__.'/admin.php';
require __DIR__.'/marketer.php';
require __DIR__.'/warehouse.php';
require __DIR__.'/sales.php';
```

### Route Group Pattern
```php
Route::middleware(['web', 'auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::prefix('resource')->name('resource.')->group(function () {
            Route::get('/', [Controller::class, 'index'])->name('index');
            Route::get('/create', [Controller::class, 'create'])->name('create');
            Route::post('/', [Controller::class, 'store'])->name('store');
            Route::get('/{model}', [Controller::class, 'show'])->name('show');
            Route::get('/{model}/edit', [Controller::class, 'edit'])->name('edit');
            Route::patch('/{model}', [Controller::class, 'update'])->name('update');
            Route::delete('/{model}', [Controller::class, 'destroy'])->name('destroy');
        });
    });
```

### Named Route Convention
`{role}.{resource}.{action}` — e.g., `marketer.sales.show`, `admin.users.edit`

## Invoice Number Generation
```php
private function generateInvoiceNumber(): string
{
    return 'SI-' . date('Ymd') . '-' . str_pad(Model::count() + 1, 5, '0', STR_PAD_LEFT);
}
```
Prefix varies by type: `SI-` (Sales Invoice), use similar pattern for other types.

## Discount & Promotion Logic

### Active Promotion Query Pattern
```php
Model::where('is_active', true)
    ->whereDate('start_date', '<=', now())
    ->whereDate('end_date', '>=', now())
    ->first();
```

### Tiered Discount Selection
```php
InvoiceDiscountTier::where('min_amount', '<=', $subtotal)
    ->where('is_active', true)
    ->whereDate('start_date', '<=', now())
    ->whereDate('end_date', '>=', now())
    ->orderBy('min_amount', 'desc')
    ->first();
```

## Frontend Conventions

### JavaScript
- Alpine.js for all UI reactivity (no Vue/React)
- Axios for AJAX (configured in `bootstrap.js` with CSRF token)
- No jQuery

### CSS
- Tailwind CSS utility classes only
- `@tailwindcss/forms` plugin for form elements
- RTL layout (Arabic UI) — use `dir="rtl"` on HTML elements

### Asset Bundling
- Entry: `resources/js/app.js` + `resources/css/app.css`
- Build: `npm run build` (Vite)
- Dev: `npm run dev` (HMR)

## PDF Generation
Use `barryvdh/laravel-dompdf` with Cairo font for Arabic text:
- Font files in `public/fonts/` (Cairo-Regular.ttf, Cairo-Bold.ttf, Cairo-ExtraBold.ttf)
- PDF views in `resources/views/shared/` subdirectories
- Access via `Shared\{Type}\InvoiceController`

## Pagination
Use `->paginate(20)->withQueryString()` to preserve filter parameters across pages.

## Query Filtering Pattern (Controllers)
```php
$hasFilter = $request->filled('field1') || $request->filled('field2');

if ($request->filled('field1')) {
    $query->where('column', 'like', '%' . $request->field1 . '%');
}

if ($request->filled('date_field')) {
    try {
        $date = \Carbon\Carbon::parse($request->date_field)->format('Y-m-d');
        $query->whereDate('created_at', '>=', $date);
    } catch (\Exception $e) {}
}
```

## Language & Localization
- All UI text and error messages are in **Arabic**
- Error messages thrown from services are in Arabic
- Flash messages (`with('success', ...)` / `with('error', ...)`) are in Arabic
- Route/variable names remain in English
- Comments in services may be Arabic (e.g., `// إرسال إشعار للمسوق`)
