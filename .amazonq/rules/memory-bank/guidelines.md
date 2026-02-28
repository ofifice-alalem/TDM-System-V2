# Development Guidelines

## Code Quality Standards

### PHP Code Formatting
- **PSR-12 Compliance**: Follow PSR-12 coding standards (enforced by Laravel Pint)
- **Namespace Organization**: Controllers organized by role (Admin, Warehouse, Marketer, Sales, Shared)
- **Type Declarations**: Use strict typing where appropriate (return types, parameter types)
- **Modern PHP Features**: Utilize PHP 8.2+ features (match expressions, named arguments, null-safe operators)

### Naming Conventions
- **Controllers**: Descriptive names with Controller suffix (e.g., StatisticsController, BackupController)
- **Methods**: Camel case, descriptive action names (e.g., getMarketerStores, exportToExcel, restoreLargeSQL)
- **Variables**: Camel case for local variables, snake_case for database columns
- **Constants**: UPPER_SNAKE_CASE for constants
- **Arabic Variables**: Use English variable names even when dealing with Arabic content

### Code Structure
- **Single Responsibility**: Each method should have one clear purpose
- **Method Length**: Keep methods focused; extract complex logic into private helper methods
- **Class Organization**: Public methods first, followed by private helper methods
- **Consistent Indentation**: 4 spaces (standard PHP/Laravel convention)

## Architectural Patterns

### Controller Patterns

**Thin Controllers with Service Delegation**
```php
// Controllers handle HTTP concerns, delegate business logic to services
public function index(Request $request)
{
    $stores = Store::where('is_active', true)->get();
    $results = null;
    
    if ($request->filled(['stat_type', 'from_date', 'to_date'])) {
        $results = $this->getStatistics($request, $request->has('export'));
        
        if ($results && $request->has('export')) {
            return $this->exportToExcel($results, $request);
        }
    }
    
    return view('shared.statistics.index', compact('stores', 'results'));
}
```

**Match Expressions for Conditional Logic**
```php
// Prefer match over switch for cleaner, type-safe conditionals
$query = match($request->operation) {
    'sales' => SalesInvoice::with('marketer', 'store'),
    'payments' => StorePayment::with('marketer', 'store', 'keeper'),
    'returns' => SalesReturn::with('marketer', 'store'),
    default => null
};

$statusName = match($request->status) {
    'pending' => 'معلق',
    'approved' => 'موثق',
    'cancelled' => 'ملغي',
    'rejected' => 'مرفوض',
    default => 'الكل'
};
```

**Private Helper Methods for Complex Operations**
```php
// Extract complex logic into well-named private methods
private function getStatistics($request, $forExport = false)
{
    // Complex query building logic
}

private function exportToExcel($results, $request)
{
    // Excel generation logic
}

private function restoreLargeSQL($sqlFile)
{
    // Database restoration logic
}
```

### Query Building Patterns

**Eager Loading Relationships**
```php
// Always eager load relationships to avoid N+1 queries
$query = SalesInvoice::with('marketer', 'store');
$query = StorePayment::with('marketer', 'store', 'keeper', 'commission');
```

**Conditional Query Building**
```php
// Build queries conditionally based on request parameters
if ($request->store_id && $request->store_id !== 'all') {
    $query->where('store_id', $request->store_id);
}

if ($request->filled('status')) {
    $query->where('status', $request->status);
}

$query->whereDate('created_at', '>=', $request->from_date)
      ->whereDate('created_at', '<=', $request->to_date);
```

**Pagination vs Full Results**
```php
// Use pagination for views, full results for exports
$data = $forExport ? $query->latest()->get() : $query->latest()->paginate(50);
```

**Closure-Based Conditional Queries**
```php
// Use closures for reusable query conditions
$storeQuery = $request->store_id !== 'all' 
    ? fn($q) => $q->where('store_id', $request->store_id) 
    : fn($q) => $q;

$totalSales = SalesInvoice::where('status', 'approved')
    ->when($request->store_id !== 'all', $storeQuery)
    ->sum('total_amount');
```

### Database Operations

**Transaction Management**
```php
// Wrap multi-step operations in transactions
DB::statement('SET FOREIGN_KEY_CHECKS=0;');
// ... operations ...
DB::statement('SET FOREIGN_KEY_CHECKS=1;');
```

**Batch Processing for Large Datasets**
```php
// Process large datasets in chunks to avoid memory issues
DB::table($tableName)->orderBy(DB::raw('1'))->chunk(500, function($rows) use (&$insertValues, $tableName, &$sql) {
    foreach ($rows as $row) {
        // Process each row
    }
});
```

**Optimized Bulk Inserts**
```php
// Collect insert values and batch them
$invoiceItems = [];
foreach ($tempItems as $item) {
    $invoiceItems[] = [
        'invoice_id' => $invoiceId,
        'product_id' => $item['product_id'],
        'quantity' => $item['quantity'],
        // ... other fields
    ];
}

if (!empty($invoiceItems)) {
    DB::table('sales_invoice_items')->insert($invoiceItems);
}
```

### Resource Management

**Memory and Time Limits for Heavy Operations**
```php
// Set appropriate limits for backup/restore operations
set_time_limit(300);
ini_set('memory_limit', '512M');

// For very large operations
set_time_limit(0);
ini_set('memory_limit', '1G');
ini_set('max_execution_time', '0');
```

**File Handling Best Practices**
```php
// Always clean up temporary files
$zip->close();
if (file_exists($sqlFile)) unlink($sqlFile);

// Use proper file iteration for directory operations
$files = new \RecursiveIteratorIterator(
    new \RecursiveDirectoryIterator($path),
    \RecursiveIteratorIterator::SELF_FIRST
);
```

## Excel Export Patterns

### PhpSpreadsheet Usage

**RTL Support for Arabic**
```php
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setRightToLeft(true);
```

**Consistent Styling**
```php
// Header styling
$sheet->getStyle('A' . $row . ':' . $lastCol . $row)->applyFromArray([
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4CAF50']],
    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12],
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
]);

// Data styling with status colors
$statusColor = match($item->status) {
    'pending' => 'FFA726',
    'approved' => '42A5F5',
    'documented' => '66BB6A',
    'cancelled' => '9E9E9E',
    'rejected' => 'EF5350',
    default => 'FFFFFF'
};
```

**Auto-sizing Columns**
```php
foreach (range('A', 'E') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}
```

**Arabic-Friendly Filenames**
```php
$filename = date('Y-m-d') . '__' . $operationName . '__' . $entityName . '.xlsx';
```

## Validation and Input Handling

### Request Validation
```php
// Validate at controller entry point
$request->validate([
    'note' => 'nullable|string|max:500',
    'backup_file' => 'required|file|mimes:zip|max:512000'
]);
```

### Safe Input Checking
```php
// Use filled() for checking non-empty values
if ($request->filled(['stat_type', 'from_date', 'to_date'])) {
    // Process request
}

// Use has() for checking parameter existence
if ($request->has('export')) {
    return $this->exportToExcel($results, $request);
}
```

## Arabic Language Support

### Blade Templates
```php
// Use Arabic text directly in views and responses
return view('shared.statistics.index', compact('stores', 'marketers', 'results'));
```

### Status Translations
```php
// Consistent Arabic translations for statuses
$status = match($item->status) {
    'pending' => 'معلق',
    'approved' => 'موثق',
    'documented' => 'موثق',
    'cancelled' => 'ملغي',
    'rejected' => 'مرفوض',
    default => $item->status
};
```

### Number Formatting
```php
// Always format numbers with 2 decimal places for currency
number_format($amount, 2)
number_format($results['total'], 2) . ' دينار'
```

## Error Handling

### Graceful Degradation
```php
// Continue on error for non-critical operations
try {
    $sql = "INSERT INTO `{$table}` VALUES " . implode(',', $values) . ';';
    DB::unprepared($sql);
} catch (\Exception $e) {
    // Continue on error
}
```

### User-Friendly Messages
```php
// Return Arabic error messages
return back()->with('error', 'الملف غير موجود');
return back()->with('success', 'تم استعادة النسخة الاحتياطية بنجاح');
```

### File Existence Checks
```php
// Always verify file existence before operations
if (!file_exists($zipFile)) {
    return back()->with('error', 'الملف غير موجود');
}
```

## Performance Optimization

### Query Optimization
- Use `select()` to limit columns when not all fields are needed
- Eager load relationships to prevent N+1 queries
- Use `whereDate()` for date filtering instead of `where()`
- Add indexes on frequently queried columns (foreign keys, status, dates)

### Caching Strategies
```php
// Cache frequently accessed data
$stores = Cache::remember('active_stores', 3600, function () {
    return Store::where('is_active', true)->get();
});
```

### Batch Operations
```php
// Process in batches for large datasets
for ($batch = 0; $batch < $totalInvoices / $batchSize; $batch++) {
    // Process batch
    echo "Batch " . ($batch + 1) . " completed\n";
}
```

## Testing Patterns

### Seeder Structure
```php
// Provide progress feedback for long-running seeders
echo "Creating sales invoices (this will take a while)...\\n";
// ... operations ...
if ($i % 500 == 0) {
    echo "Created {$i} payments\\n";
}
```

### Realistic Test Data
```php
// Generate realistic data with proper relationships
$marketerId = $marketerIds[array_rand($marketerIds)];
$storeId = $storeIds[array_rand($storeIds)];
$status = $statuses[array_rand($statuses)];
$createdAt = $startDate->copy()->addMinutes(rand(0, 259200));
```

## Configuration Best Practices

### Environment-Based Configuration
```php
// Use environment variables for configuration
'default' => env('LOG_CHANNEL', 'stack'),
'level' => env('LOG_LEVEL', 'debug'),
'days' => env('LOG_DAILY_DAYS', 14),
```

### Sensible Defaults
```php
// Always provide fallback values
'channels' => explode(',', (string) env('LOG_STACK', 'single')),
```

## Security Practices

### SQL Injection Prevention
- Always use query builder or Eloquent ORM
- Use prepared statements for raw queries
- Escape user input when building dynamic SQL

### Authentication Checks
```php
// Verify authentication before sensitive operations
if (!auth()->check()) {
    return redirect()->route('login');
}
```

### Role-Based Access Control
```php
// Use middleware for role-based routing
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Admin routes
});
```

## Documentation Standards

### Method Documentation
```php
/**
 * Export statistics to Excel format
 * 
 * @param array $results Statistics data to export
 * @param Request $request HTTP request with filters
 * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
 */
private function exportToExcel($results, $request)
{
    // Implementation
}
```

### Inline Comments
```php
// Use comments to explain complex logic, not obvious code
// Calculate totals by status
$statusTotals = [
    'pending' => 0,
    'approved' => 0,
    'cancelled' => 0,
    'rejected' => 0,
    'total' => 0
];
```

## Common Idioms

### Null Coalescing
```php
// Use null coalescing for default values
$entityName = $entity->name ?? '';
$commissionRate = $marketer->commission_rate ?? rand(5, 15);
```

### Ternary for Simple Conditions
```php
// Use ternary for simple conditional assignments
$keeper_id = $status === 'approved' ? 2 : null;
$confirmed_at = $status === 'approved' ? $createdAt : null;
```

### Array Destructuring
```php
// Use array destructuring for cleaner code
[$totalSales, $totalPayments, $totalReturns] = $this->calculateTotals($request);
```

### Collection Methods
```php
// Leverage Laravel collection methods
$stores = $salesStores->merge($paymentStores)->unique('id')->values();
$statusTotals['total'] = array_sum([$statusTotals['pending'], $statusTotals['approved']]);
```

## Blade Template Patterns

### Conditional Rendering
```php
@if (Route::has('login'))
    <nav>
        @auth
            <a href="{{ url('/dashboard') }}">Dashboard</a>
        @else
            <a href="{{ route('login') }}">Log in</a>
        @endauth
    </nav>
@endif
```

### Asset Management
```php
// Use Vite for asset compilation
@vite(['resources/css/app.css', 'resources/js/app.js'])
```

### Inline Styles for Fallback
```php
// Provide inline styles when build assets unavailable
@if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@else
    <style>/* Inline Tailwind CSS */</style>
@endif
```

## API Response Patterns

### JSON Responses
```php
// Return structured JSON for AJAX requests
return response()->json([
    'discount_amount' => round($discountAmount, 2),
    'discount_type' => $tier->discount_type,
    'discount_value' => $tier->discount_type === 'percentage' 
        ? $tier->discount_percentage 
        : $tier->discount_amount
]);
```

### File Downloads
```php
// Use response()->download() for file downloads
return response()->download($zipFile);
return response()->download($file);
```

## Frequently Used Annotations

### Route Definitions
```php
// Group routes by middleware and prefix
Route::middleware('auth')->group(function () {
    Route::get('/profile', function() { return view('profile.edit'); })->name('profile.edit');
});
```

### Model Relationships
```php
// Define relationships in models
public function marketer()
{
    return $this->belongsTo(User::class, 'marketer_id');
}

public function items()
{
    return $this->hasMany(SalesInvoiceItem::class, 'invoice_id');
}
```

### Validation Rules
```php
// Use Laravel validation rules
'note' => 'nullable|string|max:500',
'backup_file' => 'required|file|mimes:zip|max:512000',
'amount' => 'required|numeric|min:0',
'status' => 'required|in:pending,approved,rejected,cancelled'
```
