# Step 4: إيصال القبض (تسديد دين المتجر) ✅

## 📋 نظرة عامة
تم تنفيذ عملية إيصال القبض (تسديد دين المتجر) بالكامل وفقاً للمواصفات في `step 4-عملية_إيصال_القبض_تسديد.txt`

---

## 📁 الملفات المُنشأة

### 1️⃣ Models
- ✅ `app/Models/StorePayment.php` (موجود مسبقاً)
- ✅ `app/Models/MarketerCommission.php` (جديد)

### 2️⃣ Services
- ✅ `app/Services/Marketer/PaymentService.php`
- ✅ `app/Services/Warehouse/WarehousePaymentService.php`

### 3️⃣ Controllers
- ✅ `app/Http/Controllers/Marketer/PaymentController.php`
- ✅ `app/Http/Controllers/Warehouse/WarehousePaymentController.php`

### 4️⃣ Views - Shared
- ✅ `resources/views/shared/payments/_payment-card.blade.php`
- ✅ `resources/views/shared/payments/_status-tabs.blade.php`
- ✅ `resources/views/shared/payments/_timeline-guide.blade.php`

### 5️⃣ Views - Marketer
- ✅ `resources/views/marketer/payments/index.blade.php`
- ✅ `resources/views/marketer/payments/create.blade.php`
- ✅ `resources/views/marketer/payments/show.blade.php`

### 6️⃣ Views - Warehouse
- ✅ `resources/views/warehouse/payments/index.blade.php`
- ✅ `resources/views/warehouse/payments/show.blade.php`

### 7️⃣ Routes
- ✅ `routes/marketer.php` (تم التحديث)
- ✅ `routes/warehouse.php` (تم التحديث)

---

## 🔄 سير العملية (Status Flow)

```
pending → approved / rejected / cancelled
```

### المراحل:

1. **إنشاء (pending)** - المسوق
   - تسجيل الإيصال فقط
   - لا يتأثر الدين

2. **توثيق (approved)** - أمين المخزن
   - خصم من `store_debt_ledger` (amount سالب)
   - إضافة عمولة في `marketer_commissions`
   - رفع صورة الإيصال المختوم

3. **رفض (rejected)** - أمين المخزن
   - لا يتأثر الدين

4. **إلغاء (cancelled)** - المسوق
   - فقط في حالة pending
   - لا يتأثر الدين

---

## 🎯 الوظائف الرئيسية

### المسوق (Marketer):
- ✅ عرض قائمة الإيصالات (مع فلترة حسب الحالة)
- ✅ إنشاء إيصال قبض جديد
- ✅ عرض تفاصيل الإيصال
- ✅ إلغاء الإيصال (pending فقط)
- ✅ عرض الدين الحالي للمتجر

### أمين المخزن (Warehouse):
- ✅ عرض قائمة جميع الإيصالات
- ✅ عرض تفاصيل الإيصال
- ✅ توثيق الإيصال (رفع صورة + حساب العمولة)
- ✅ رفض الإيصال

---

## 💾 الجداول المتأثرة

### عند الإنشاء (pending):
- `store_payments` (INSERT)

### عند التوثيق (approved):
- `store_payments` (UPDATE status, receipt_image, confirmed_at)
- `store_debt_ledger` (INSERT - amount سالب)
- `marketer_commissions` (INSERT - إذا كانت نسبة العمولة > 0)

### عند الرفض/الإلغاء:
- `store_payments` (UPDATE status, notes)

---

## 🎨 التصميم

- ✅ نفس نمط التصميم المستخدم في Sales/Requests
- ✅ دعم Dark Mode
- ✅ Responsive Design
- ✅ استخدام Lucide Icons
- ✅ Animations (fade-in, slide-up)

---

## 📊 حساب العمولة

```php
commission_rate = users.commission_rate (من جدول المستخدمين)
commission_amount = payment_amount × (commission_rate / 100)
```

- ✅ يتم الحساب تلقائياً عند التوثيق
- ✅ إذا كانت نسبة العمولة = 0، لا تُسجل عمولة

---

## 🔐 الصلاحيات

| الدور | الصلاحيات |
|------|-----------|
| المسوق | إنشاء، عرض، إلغاء (pending فقط) |
| أمين المخزن | عرض، توثيق، رفض |

---

## 🚀 Routes

### Marketer:
```
GET  /marketer/payments              - index
GET  /marketer/payments/create       - create
POST /marketer/payments              - store
GET  /marketer/payments/{payment}    - show
PATCH /marketer/payments/{payment}/cancel - cancel
GET  /marketer/payments/store/{storeId}/debt - getStoreDebt
```

### Warehouse:
```
GET  /warehouse/payments             - index
GET  /warehouse/payments/{payment}   - show
POST /warehouse/payments/{id}/approve - approve
PATCH /warehouse/payments/{id}/reject - reject
```

---

## ✅ الميزات الإضافية

1. **عرض الدين الحالي** عند اختيار المتجر في صفحة الإنشاء
2. **التحقق من المبلغ** لا يمكن أن يكون أكبر من الدين
3. **Status Tabs** للفلترة السريعة
4. **Timeline Guide** لشرح سير العملية
5. **Responsive Cards** مع ألوان مميزة لكل حالة

---

## 📝 ملاحظات

- ✅ تم اتباع نفس البنية المعمارية (architecture.md)
- ✅ كود مختصر وفعال (minimal code)
- ✅ لا منطق أعمال في Controllers
- ✅ جميع العمليات في Services
- ✅ استخدام DB Transactions

---

## 🎯 الخطوة التالية

**Step 5: سحب أرباح المسوق** 🚀
