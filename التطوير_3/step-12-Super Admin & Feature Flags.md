# Step-12 — Super Admin & Feature Flags

## الهدف
إنشاء دور `super_admin` يتحكم في تفعيل/تعطيل ميزات النظام لكل الأدوار مع دعم التوقيت.

---

## قاعدة البيانات

### جدول `features`
| الحقل | النوع | الوصف |
|-------|-------|-------|
| `id` | bigint PK | |
| `key` | varchar unique | معرف الميزة (مثل `admin.products-pricing`) |
| `label` | varchar | الاسم بالعربي |
| `role` | varchar | الدور (admin / marketer / warehouse / sales) |
| `is_enabled` | boolean default true | الحالة الحالية |
| `mode` | enum | `permanent` / `scheduled_off` / `scheduled_on` |
| `starts_at` | timestamp nullable | بداية التوقيت |
| `ends_at` | timestamp nullable | نهاية التوقيت |
| `timestamps` | | |

### أوضاع التحكم `mode`
- `permanent` — تعطيل/تفعيل دائم بدون وقت
- `scheduled_off` — مفعّل الآن، يُعطَّل تلقائياً من `starts_at` إلى `ends_at`
- `scheduled_on` — معطَّل الآن، يُفعَّل تلقائياً من `starts_at` إلى `ends_at`

---

## الميزات المُدارة

### Admin
| key | label |
|-----|-------|
| `admin.users` | إدارة المستخدمين |
| `admin.products` | إدارة المنتجات |
| `admin.factory-invoices` | فواتير المصنع |
| `admin.main-stock` | المخزون الرئيسي |
| `admin.stores` | إدارة المتاجر |
| `admin.discounts` | خصومات الفواتير |
| `admin.promotions` | العروض الترويجية |
| `admin.withdrawals` | طلبات السحب |
| `admin.old-debts` | ديون المتاجر السابقة |
| `admin.old-customer-debts` | ديون العملاء السابقة |
| `admin.customer-merge` | دمج العملاء |
| `admin.store-merge` | دمج المتاجر |
| `admin.statistics` | إحصائيات المتاجر |
| `admin.customer-statistics` | إحصائيات العملاء |
| `admin.combined-summary` | الملخص الشامل |
| `admin.products-pricing` | تسعير المنتجات |
| `admin.backups` | النسخ الاحتياطي |

### Marketer
| key | label |
|-----|-------|
| `marketer.stock` | مخزوني |
| `marketer.requests` | طلبات البضاعة |
| `marketer.returns` | إرجاعات البضاعة |
| `marketer.sales` | فواتير البيع |
| `marketer.payments` | إيصالات القبض |
| `marketer.commissions` | العمولات |
| `marketer.withdrawals` | طلبات السحب |
| `marketer.sales-returns` | مرتجعات المتاجر |
| `marketer.stores` | المتاجر |
| `marketer.discounts` | الخصومات |
| `marketer.promotions` | العروض |
| `marketer.main-stock` | المخزون الرئيسي |

### Warehouse
| key | label |
|-----|-------|
| `warehouse.requests` | طلبات المسوقين |
| `warehouse.returns` | إرجاعات المسوقين |
| `warehouse.sales` | فواتير البيع |
| `warehouse.payments` | إيصالات القبض |
| `warehouse.sales-returns` | مرتجعات المتاجر |
| `warehouse.stores` | المتاجر |
| `warehouse.main-stock` | المخزون الرئيسي |
| `warehouse.factory-invoices` | فواتير المصنع |

### Sales
| key | label |
|-----|-------|
| `sales.customers` | العملاء |
| `sales.invoices` | الفواتير |
| `sales.payments` | المدفوعات |
| `sales.returns` | المرتجعات |
| `sales.statistics` | الإحصائيات |
| `sales.main-stock` | المخزون الرئيسي |

---

## الملفات المُنشأة

### Migration
- `database/migrations/2026_03_XX_create_features_table.php`

### Model
- `app/Models/Feature.php`

### Seeder
- `database/seeders/FeatureSeeder.php` — يُدرج كل الميزات بحالة مفعّلة

### Middleware
- `app/Http/Middleware/CheckFeature.php`
- يتحقق من الميزة المقابلة للـ route الحالي
- يحسب الحالة الفعلية بناءً على `mode` + `starts_at` + `ends_at`
- عند التعطيل: redirect إلى `/feature-disabled`

### Controllers
- `app/Http/Controllers/SuperAdmin/FeatureController.php`
- `app/Http/Controllers/SuperAdmin/DashboardController.php`

### Views
- `resources/views/super-admin/features/index.blade.php`
- `resources/views/errors/feature-disabled.blade.php`

### Routes
- `routes/super-admin.php` — middleware: `auth`, `role:super_admin`
- مضاف في `routes/web.php`

### Role
- `role_id = 5` — super_admin
- مستخدم النظام: `username: super_admin`

---

## سلوك Middleware
```
1. جلب الميزة من DB بناءً على route name
2. إذا لم توجد → السماح بالمرور
3. حساب الحالة الفعلية:
   - permanent: is_enabled مباشرة
   - scheduled_off: is_enabled=true إلا في نطاق starts_at→ends_at
   - scheduled_on: is_enabled=false إلا في نطاق starts_at→ends_at
4. إذا معطّل → redirect('/feature-disabled')
```

---

## واجهة Super Admin
- جدول بكل الميزات مجمّعة حسب الدور
- لكل ميزة: toggle + اختيار mode + تحديد starts_at/ends_at
- تحديث فوري بدون reload (Alpine.js + Axios)
