# تحديث هيكل قاعدة البيانات - دعم نظام الشركات والفروع

## نظرة عامة
هذا الملف يوثق التعديلات المطلوبة على هيكل قاعدة البيانات لدعم نظام الشركات والفروع، حيث:
- كل متجر يتبع لشركة (company)
- الشركة يمكن أن يكون لها فرع واحد أو عدة فروع
- الدين يُحسب على مستوى الشركة وليس الفرع
- الدفع يتم على مستوى الشركة

---

## الجداول الجديدة

### 1. companies (جديد)
```
id                  PK
name                VARCHAR(255) NOT NULL
phone               VARCHAR(20) NULLABLE
address             TEXT NULLABLE
is_active           BOOLEAN DEFAULT 1
created_at          TIMESTAMP
updated_at          TIMESTAMP
```

**الغرض:** تجميع الفروع تحت شركة واحدة

**العلاقات:**
- `hasMany` → stores
- `hasMany` → company_debt_ledger
- `hasMany` → company_payments

---

### 2. company_debt_ledger (جديد - بديل store_debt_ledger)
```
id                  PK
company_id          FK → companies.id (NOT NULL)
store_id            FK → stores.id (NULLABLE - NULL في حالة الدفع)
reference_type      ENUM('sales_invoice', 'sales_return', 'company_payment', 'opening_balance')
reference_id        BIGINT UNSIGNED NULLABLE
amount              DECIMAL(10,2) - موجب للدين، سالب للسداد
balance_after       DECIMAL(10,2) - رصيد الشركة الإجمالي بعد الحركة
created_at          TIMESTAMP
```

**الغرض:** تتبع حركات الدين على مستوى الشركة

**ملاحظات:**
- `store_id` يكون NULL عند تسجيل الدفع (لأن الدفع على مستوى الشركة)
- `store_id` يحتوي على رقم الفرع عند تسجيل فاتورة بيع أو إرجاع
- `balance_after` يعكس رصيد الشركة الإجمالي (مجموع كل الفروع)

**العلاقات:**
- `belongsTo` → company
- `belongsTo` → store (nullable)
- `morphTo` → reference (sales_invoice, sales_return, company_payment)

---

### 3. company_payments (تغيير اسم من store_payments)
```
id                  PK
payment_number      VARCHAR(50) UNIQUE
company_id          FK → companies.id (NOT NULL) - تغيير من store_id
marketer_id         FK → users.id
keeper_id           FK → users.id NULLABLE
amount              DECIMAL(10,2)
payment_method      ENUM('cash', 'bank_transfer', 'check')
status              ENUM('pending', 'approved', 'rejected') DEFAULT 'pending'
receipt_image       VARCHAR(255) NULLABLE
notes               TEXT NULLABLE
created_at          TIMESTAMP
updated_at          TIMESTAMP
```

**التغييرات:**
- تغيير الاسم من `store_payments` إلى `company_payments`
- استبدال `store_id` بـ `company_id`
- الدفع يتم على مستوى الشركة وليس الفرع

**العلاقات:**
- `belongsTo` → company
- `belongsTo` → marketer (user)
- `belongsTo` → keeper (user)
- `hasMany` → marketer_commissions

---

## الجداول المعدلة

### 1. stores (تعديل)
```
id                  PK
company_id          FK → companies.id (NOT NULL) - مضاف
name                VARCHAR(255)
owner_name          VARCHAR(255)
phone               VARCHAR(20)
location            VARCHAR(255) NULLABLE
address             TEXT NULLABLE
is_active           BOOLEAN DEFAULT 1
created_at          TIMESTAMP
updated_at          TIMESTAMP
```

**التغييرات:**
- إضافة `company_id` (NOT NULL)
- كل متجر يجب أن يتبع لشركة

**العلاقات الجديدة:**
- `belongsTo` → company

---

### 2. sales_invoices (بدون تغيير مباشر)
```
- يبقى store_id كما هو
- الوصول للشركة يتم عبر: store→company_id
- لا حاجة لإضافة company_id مباشرة
```

---

### 3. sales_returns (بدون تغيير مباشر)
```
- يبقى store_id كما هو
- الوصول للشركة يتم عبر: store→company_id
```

---

### 4. marketer_commissions (تعديل بسيط)
```
payment_id          FK → company_payments.id - تغيير المرجع فقط
```

**التغييرات:**
- المرجع يصبح `company_payments` بدلاً من `store_payments`

---

## الجداول المحذوفة

### store_debt_ledger ❌
- يتم حذفه بالكامل
- يُستبدل بـ `company_debt_ledger`

---

## سلوك النظام الجديد

### عند إنشاء متجر جديد:
1. إنشاء شركة جديدة بنفس اسم المتجر
2. ربط المتجر بالشركة المنشأة
3. هذا يسمح بإضافة فروع لاحقاً بسهولة

### عند إصدار فاتورة بيع:
1. الفاتورة تُسجل على `store_id` (الفرع)
2. يتم إضافة سجل في `company_debt_ledger`:
   - `company_id` = من خلال store→company_id
   - `store_id` = رقم الفرع الذي أصدر الفاتورة
   - `amount` = موجب (زيادة الدين)
   - `balance_after` = الرصيد الإجمالي للشركة

### عند إرجاع بضاعة:
1. الإرجاع يُسجل على `store_id` (الفرع)
2. يتم إضافة سجل في `company_debt_ledger`:
   - `company_id` = من خلال store→company_id
   - `store_id` = رقم الفرع الذي أرجع البضاعة
   - `amount` = سالب (تخفيض الدين)
   - `balance_after` = الرصيد الإجمالي للشركة

### عند الدفع:
1. الدفع يُسجل في `company_payments` على مستوى `company_id`
2. يتم إضافة سجل في `company_debt_ledger`:
   - `company_id` = رقم الشركة
   - `store_id` = NULL (لأن الدفع على مستوى الشركة)
   - `amount` = سالب (تخفيض الدين)
   - `balance_after` = الرصيد الإجمالي للشركة

---

## حساب balance_after

```php
// عند كل حركة جديدة
$lastBalance = CompanyDebtLedger::where('company_id', $companyId)
    ->latest('id')
    ->value('balance_after') ?? 0;

$newBalance = $lastBalance + $amount; // موجب للدين، سالب للسداد

CompanyDebtLedger::create([
    'company_id' => $companyId,
    'store_id' => $storeId, // أو NULL في حالة الدفع
    'reference_type' => $type,
    'reference_id' => $id,
    'amount' => $amount,
    'balance_after' => $newBalance,
]);
```

---

## مخطط العلاقات

```
companies
    ↓ (company_id)
stores ──────────────────────────────────────────┐
    ↓ (store_id)                                  │
sales_invoices → company_debt_ledger (store_id)   │
sales_returns  → company_debt_ledger (store_id)   │
                                                  │
company_payments (company_id) ────────────────────┘
    ↓
company_debt_ledger (store_id: NULL)
```

---

## الاستعلامات الشائعة

### إجمالي دين شركة معينة:
```php
$totalDebt = CompanyDebtLedger::where('company_id', $companyId)
    ->latest('id')
    ->value('balance_after') ?? 0;
```

### دين فرع معين (للتقارير):
```php
$storeDebt = CompanyDebtLedger::where('company_id', $companyId)
    ->where('store_id', $storeId)
    ->sum('amount');
```

### كل فروع شركة معينة:
```php
$stores = Store::where('company_id', $companyId)->get();
```

### كل الشركات التي لها ديون:
```php
$companies = Company::whereHas('debtLedger', function($q) {
    $q->selectRaw('company_id, MAX(id) as max_id')
      ->groupBy('company_id')
      ->havingRaw('(SELECT balance_after FROM company_debt_ledger WHERE id = max_id) > 0');
})->get();
```

---

## ملاحظات مهمة

1. **كل متجر = شركة افتراضياً**: عند إنشاء متجر جديد، يتم إنشاء شركة تلقائياً بنفس الاسم
2. **الدين على مستوى الشركة**: حتى لو كان للشركة عدة فروع، الدين موحد
3. **الدفع على مستوى الشركة**: الدفع لا يخص فرعاً معيناً
4. **التتبع على مستوى الفرع**: يمكن معرفة أي فرع أصدر أي فاتورة من خلال `store_id` في `company_debt_ledger`
5. **balance_after**: يعكس دائماً رصيد الشركة الإجمالي، وليس رصيد الفرع

---

## Migration Order

1. إنشاء جدول `companies`
2. تعديل جدول `stores` (إضافة `company_id`)
3. إنشاء جدول `company_debt_ledger`
4. إعادة تسمية `store_payments` إلى `company_payments` وتعديل `store_id` إلى `company_id`
5. حذف جدول `store_debt_ledger` (بعد نقل البيانات إن وجدت)

---

## تاريخ التحديث
2026-03-17
