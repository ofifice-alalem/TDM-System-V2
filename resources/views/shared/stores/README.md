# صفحة المتاجر - Stores Module

## الملفات المُنشأة

### Controllers
- `app/Http/Controllers/Shared/StoreController.php` - Controller مشترك لجميع المستخدمين

### Views
- `resources/views/shared/stores/index.blade.php` - صفحة عرض قائمة المتاجر
- `resources/views/shared/stores/show.blade.php` - صفحة تفاصيل المتجر

### Models
- `app/Models/SalesReturn.php` - Model للإرجاعات (جديد)
- `app/Models/StorePayment.php` - Model للمدفوعات (جديد)
- تحديث `app/Models/StoreDebtLedger.php` - إضافة العلاقات

### Routes
```php
GET /stores - عرض قائمة المتاجر
GET /stores/{store} - عرض تفاصيل متجر معين
```

## الميزات

### صفحة القائمة (index)
- عرض جميع المتاجر في شكل بطاقات (Cards)
- بحث بالاسم، المالك، أو الموقع
- عرض الرصيد الحالي لكل متجر
- أزرار سريعة للاتصال والرسائل
- تصميم متجاوب (Responsive)

### صفحة التفاصيل (show)
- معلومات المتجر الكاملة
- الملخص المالي (المبيعات، المدفوعات، المرتجعات، الرصيد)
- سجل الحركات المالية (آخر 20 حركة)
- تصميم احترافي مع ألوان مميزة لكل نوع حركة

## الوصول
- متاح لجميع المستخدمين (Marketer, Warehouse, Admin)
- تم إضافة رابط في Sidebar تحت قسم "أخرى"

## التصميم
- متوافق مع التصميم الحالي للنظام
- يدعم الوضع الليلي (Dark Mode)
- استخدام نفس نظام الألوان والأيقونات
- رسوم متحركة سلسة (Animations)
