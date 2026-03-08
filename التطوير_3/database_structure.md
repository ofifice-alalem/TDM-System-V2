
========================================
هيكل قاعدة البيانات - TDM System V3
تاريخ التحديث: 2026-03-07
========================================

التحسينات في V3:
✅ إضافة جدول marketers (فصل بيانات المسوقين عن users)
✅ تعديل store_debt_ledger (reference_type + reference_id + balance_after)
✅ تعديل customer_debt_ledger (reference_type + reference_id + balance_after)
✅ إضافة جدول inventory_movements (التتبع الشامل للمخزون)
✅ إضافة جدول inventory_snapshots (لقطات المخزون السنوية)

========================================
جداول أساسية (مشتركة)
========================================

========================================
1. جدول: roles
========================================

  • id
    النوع: bigint unsigned
    المفتاح: PRIMARY KEY
    القيمة الافتراضية: AUTO_INCREMENT

  • name
    النوع: varchar(255)
    القيمة الافتراضية: —

  • display_name
    النوع: varchar(255)
    القيمة الافتراضية: —

  • description
    النوع: text
    القيمة الافتراضية: NULL

  • is_active
    النوع: tinyint(1)
    القيمة الافتراضية: 1

  • created_at
    النوع: timestamp
    القيمة الافتراضية: CURRENT_TIMESTAMP

  • updated_at
    النوع: timestamp
    القيمة الافتراضية: CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

ملاحظة: يحتوي على 4 أدوار (Admin=1, Warehouse=2, Marketer=3, Sales=4)

========================================
2. جدول: users
========================================

  • id
    النوع: bigint unsigned
    المفتاح: PRIMARY KEY
    القيمة الافتراضية: AUTO_INCREMENT

  • username
    النوع: varchar(50)
    القيمة الافتراضية: —

  • password_hash
    النوع: varchar(255)
    القيمة الافتراضية: —

  • remember_token
    النوع: varchar(100)
    القيمة الافتراضية: NULL

  • full_name
    النوع: varchar(100)
    القيمة الافتراضية: —

  • role_id
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    يشير إلى: roles.id
    القيمة الافتراضية: —

  • phone
    النوع: varchar(20)
    القيمة الافتراضية: NULL

  • is_active
    النوع: tinyint(1)
    القيمة الافتراضية: 1

  • created_at
    النوع: timestamp
    القيمة الافتراضية: CURRENT_TIMESTAMP

  • updated_at
    النوع: timestamp
    القيمة الافتراضية: CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

ملاحظة: ✅ تم حذف commission_rate (نقل إلى جدول marketers)

========================================
3. جدول: marketers (جديد في V3)
========================================

  • id
    النوع: bigint unsigned
    المفتاح: PRIMARY KEY
    القيمة الافتراضية: AUTO_INCREMENT

  • user_id
    النوع: bigint unsigned
    المفتاح: UNIQUE
    المفتاح: FOREIGN KEY
    يشير إلى: users.id
    القيمة الافتراضية: —
    ملاحظة: CASCADE ON DELETE

  • commission_rate
    النوع: decimal(5,2)
    القيمة الافتراضية: —

  • address
    النوع: text
    القيمة الافتراضية: NULL

  • notes
    النوع: text
    القيمة الافتراضية: NULL

  • created_at
    النوع: timestamp
    القيمة الافتراضية: CURRENT_TIMESTAMP

  • updated_at
    النوع: timestamp
    القيمة الافتراضية: CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

========================================
4. جدول: products
========================================

  • id
    النوع: bigint unsigned
    المفتاح: PRIMARY KEY
    القيمة الافتراضية: AUTO_INCREMENT

  • name
    النوع: varchar(100)
    القيمة الافتراضية: —

  • barcode
    النوع: varchar(50)
    القيمة الافتراضية: NULL

  • description
    النوع: text
    القيمة الافتراضية: NULL

  • current_price
    النوع: decimal(10,2)
    القيمة الافتراضية: —
    ملاحظة: سعر البيع للمتاجر

  • customer_price
    النوع: decimal(10,2)
    القيمة الافتراضية: NULL
    ملاحظة: سعر البيع للعملاء المباشرين

  • is_active
    النوع: tinyint(1)
    القيمة الافتراضية: 1

  • created_at
    النوع: timestamp
    القيمة الافتراضية: CURRENT_TIMESTAMP

  • updated_at
    النوع: timestamp
    القيمة الافتراضية: CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

========================================
5. جدول: main_stock
========================================

  • product_id
    النوع: bigint unsigned
    المفتاح: PRIMARY KEY
    المفتاح: FOREIGN KEY
    يشير إلى: products.id
    القيمة الافتراضية: —

  • quantity
    النوع: int
    القيمة الافتراضية: 0

  • updated_at
    النوع: timestamp
    القيمة الافتراضية: CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

========================================
6. جدول: stores
========================================

  • id
    النوع: bigint unsigned
    المفتاح: PRIMARY KEY
    القيمة الافتراضية: AUTO_INCREMENT

  • name
    النوع: varchar(100)
    القيمة الافتراضية: —

  • owner_name
    النوع: varchar(100)
    القيمة الافتراضية: —

  • phone
    النوع: varchar(20)
    القيمة الافتراضية: NULL

  • location
    النوع: varchar(200)
    القيمة الافتراضية: NULL

  • address
    النوع: text
    القيمة الافتراضية: NULL

  • is_active
    النوع: tinyint(1)
    القيمة الافتراضية: 1

  • created_at
    النوع: timestamp
    القيمة الافتراضية: CURRENT_TIMESTAMP

========================================
7. جدول: customers
========================================

  • id
    النوع: bigint unsigned
    المفتاح: PRIMARY KEY
    القيمة الافتراضية: AUTO_INCREMENT

  • name
    النوع: varchar(100)
    القيمة الافتراضية: —

  • phone
    النوع: varchar(20)
    المفتاح: INDEX
    القيمة الافتراضية: —

  • address
    النوع: text
    القيمة الافتراضية: NULL

  • id_number
    النوع: varchar(50)
    القيمة الافتراضية: NULL

  • is_active
    النوع: tinyint(1)
    المفتاح: INDEX
    القيمة الافتراضية: 1

  • created_at
    النوع: timestamp
    القيمة الافتراضية: CURRENT_TIMESTAMP

  • updated_at
    النوع: timestamp
    القيمة الافتراضية: CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

========================================
8. جدول: notifications
========================================

  • id
    النوع: bigint unsigned
    المفتاح: PRIMARY KEY
    القيمة الافتراضية: AUTO_INCREMENT

  • user_id
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    يشير إلى: users.id
    القيمة الافتراضية: —

  • type
    النوع: varchar(255)
    القيمة الافتراضية: —

  • title
    النوع: varchar(255)
    القيمة الافتراضية: —

  • message
    النوع: text
    القيمة الافتراضية: —

  • link
    النوع: varchar(255)
    القيمة الافتراضية: NULL

  • is_read
    النوع: tinyint(1)
    القيمة الافتراضية: 0

  • created_at
    النوع: timestamp
    القيمة الافتراضية: CURRENT_TIMESTAMP

  • updated_at
    النوع: timestamp
    القيمة الافتراضية: CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

========================================
A. عملية: فواتير المصنع (إضافة مخزون رئيسي)
========================================

========================================
9. جدول: factory_invoices
========================================

  • id
    النوع: bigint unsigned
    المفتاح: PRIMARY KEY
    القيمة الافتراضية: AUTO_INCREMENT

  • invoice_number
    النوع: varchar(255)
    المفتاح: UNIQUE
    القيمة الافتراضية: —

  • created_by
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    يشير إلى: users.id
    القيمة الافتراضية: —

  • status
    النوع: enum('pending','documented','cancelled')
    القيمة الافتراضية: 'pending'

  • notes
    النوع: text
    القيمة الافتراضية: NULL

  • documented_by
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    يشير إلى: users.id
    القيمة الافتراضية: NULL

  • documented_at
    النوع: timestamp
    القيمة الافتراضية: NULL

  • cancelled_at
    النوع: timestamp
    القيمة الافتراضية: NULL

  • cancelled_by
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    يشير إلى: users.id
    القيمة الافتراضية: NULL

  • cancellation_reason
    النوع: text
    القيمة الافتراضية: NULL

  • stamped_image
    النوع: varchar(255)
    القيمة الافتراضية: NULL

  • created_at
    النوع: timestamp
    القيمة الافتراضية: CURRENT_TIMESTAMP

  • updated_at
    النوع: timestamp
    القيمة الافتراضية: CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

========================================
10. جدول: factory_invoice_items
========================================

  • id
    النوع: bigint unsigned
    المفتاح: PRIMARY KEY
    القيمة الافتراضية: AUTO_INCREMENT

  • invoice_id
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    يشير إلى: factory_invoices.id
    القيمة الافتراضية: —
    ملاحظة: CASCADE ON DELETE

  • product_id
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    يشير إلى: products.id
    القيمة الافتراضية: —

  • quantity
    النوع: int
    القيمة الافتراضية: —

========================================
B. عملية: طلب بضاعة من المسوق
========================================

========================================
11. جدول: marketer_requests
========================================

  • id
    النوع: bigint unsigned
    المفتاح: PRIMARY KEY
    القيمة الافتراضية: AUTO_INCREMENT

  • invoice_number
    النوع: varchar(50)
    المفتاح: UNIQUE
    القيمة الافتراضية: —

  • marketer_id
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    يشير إلى: users.id
    القيمة الافتراضية: —

  • status
    النوع: enum('pending','approved','rejected','cancelled','documented')
    القيمة الافتراضية: 'pending'

  • notes
    النوع: text
    القيمة الافتراضية: NULL

  • approved_by
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    يشير إلى: users.id
    القيمة الافتراضية: NULL

  • approved_at
    النوع: timestamp
    القيمة الافتراضية: NULL

  • rejected_by
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    يشير إلى: users.id
    القيمة الافتراضية: NULL

  • rejected_at
    النوع: timestamp
    القيمة الافتراضية: NULL

  • documented_by
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    يشير إلى: users.id
    القيمة الافتراضية: NULL

  • documented_at
    النوع: timestamp
    القيمة الافتراضية: NULL

  • stamped_image
    النوع: varchar(255)
    القيمة الافتراضية: NULL

  • created_at
    النوع: timestamp
    القيمة الافتراضية: CURRENT_TIMESTAMP

  • updated_at
    النوع: timestamp
    القيمة الافتراضية: CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

========================================
12. جدول: marketer_request_items
========================================

  • id
    النوع: bigint unsigned
    المفتاح: PRIMARY KEY
    القيمة الافتراضية: AUTO_INCREMENT

  • request_id
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    يشير إلى: marketer_requests.id
    القيمة الافتراضية: —

  • product_id
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    يشير إلى: products.id
    القيمة الافتراضية: —

  • quantity
    النوع: int
    القيمة الافتراضية: —

========================================
13. جدول: marketer_reserved_stock
========================================

  • id
    النوع: bigint unsigned
    المفتاح: PRIMARY KEY
    القيمة الافتراضية: AUTO_INCREMENT

  • marketer_id
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    يشير إلى: users.id
    القيمة الافتراضية: —

  • product_id
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    يشير إلى: products.id
    القيمة الافتراضية: —

  • quantity
    النوع: int
    القيمة الافتراضية: 0

  • created_at
    النوع: timestamp
    القيمة الافتراضية: CURRENT_TIMESTAMP

  • updated_at
    النوع: timestamp
    القيمة الافتراضية: CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

========================================
14. جدول: marketer_actual_stock
========================================

  • id
    النوع: bigint unsigned
    المفتاح: PRIMARY KEY
    القيمة الافتراضية: AUTO_INCREMENT

  • marketer_id
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    يشير إلى: users.id
    القيمة الافتراضية: —

  • product_id
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    يشير إلى: products.id
    القيمة الافتراضية: —

  • quantity
    النوع: int
    القيمة الافتراضية: 0

========================================
15. جدول: warehouse_stock_logs
========================================

  • id
    النوع: bigint unsigned
    المفتاح: PRIMARY KEY
    القيمة الافتراضية: AUTO_INCREMENT

  • invoice_type
    النوع: enum('factory','marketer_request','marketer_return','sales_return')
    القيمة الافتراضية: —

  • invoice_id
    النوع: int
    القيمة الافتراضية: —

  • keeper_id
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    يشير إلى: users.id
    القيمة الافتراضية: —

  • action
    النوع: enum('add','withdraw','return')
    القيمة الافتراضية: —

  • created_at
    النوع: timestamp
    القيمة الافتراضية: CURRENT_TIMESTAMP

ملاحظة: يستخدم لتوثيق حركات الفواتير فقط، التتبع التفصيلي في inventory_movements

========================================
C. عملية: إرجاع بضاعة من المسوق
========================================

========================================
16. جدول: marketer_return_requests
========================================

  • id
    النوع: bigint unsigned
    المفتاح: PRIMARY KEY
    القيمة الافتراضية: AUTO_INCREMENT

  • invoice_number
    النوع: varchar(50)
    المفتاح: UNIQUE
    القيمة الافتراضية: —

  • marketer_id
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    يشير إلى: users.id
    القيمة الافتراضية: —

  • status
    النوع: enum('pending','approved','rejected','cancelled','documented')
    القيمة الافتراضية: 'pending'

  • notes
    النوع: text
    القيمة الافتراضية: NULL

  • approved_by
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    يشير إلى: users.id
    القيمة الافتراضية: NULL

  • approved_at
    النوع: timestamp
    القيمة الافتراضية: NULL

  • rejected_by
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    يشير إلى: users.id
    القيمة الافتراضية: NULL

  • rejected_at
    النوع: timestamp
    القيمة الافتراضية: NULL

  • documented_by
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    يشير إلى: users.id
    القيمة الافتراضية: NULL

  • documented_at
    النوع: timestamp
    القيمة الافتراضية: NULL

  • stamped_image
    النوع: varchar(255)
    القيمة الافتراضية: NULL

  • created_at
    النوع: timestamp
    القيمة الافتراضية: CURRENT_TIMESTAMP

  • updated_at
    النوع: timestamp
    القيمة الافتراضية: CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

========================================
17. جدول: marketer_return_items
========================================

  • id
    النوع: bigint unsigned
    المفتاح: PRIMARY KEY
    القيمة الافتراضية: AUTO_INCREMENT

  • return_request_id
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    يشير إلى: marketer_return_requests.id
    القيمة الافتراضية: —

  • product_id
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    يشير إلى: products.id
    القيمة الافتراضية: —

  • quantity
    النوع: int
    القيمة الافتراضية: —

========================================
D. عملية: بيع بضاعة للمتجر
========================================

========================================
18. جدول: product_promotions
========================================

وظيفته:
يحتوي على العروض الترويجية للمنتجات (اشتري X واحصل على Y مجاناً).
⚠️ ملاحظة مهمة: لا يمكن تعديل شروط العرض بعد الإنشاء (للحفاظ على التتبع التاريخي).
للتغيير: قم بتعطيل العرض القديم (is_active = false) وأنشئ عرض جديد.

  • id
    النوع: bigint unsigned
    المفتاح: PRIMARY KEY
    القيمة الافتراضية: AUTO_INCREMENT

  • product_id
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    يشير إلى: products.id
    القيمة الافتراضية: —
    ملاحظة: ❌ لا يمكن تعديله بعد الإنشاء

  • min_quantity
    النوع: int
    القيمة الافتراضية: —
    ملاحظة: ❌ لا يمكن تعديله بعد الإنشاء

  • free_quantity
    النوع: int
    القيمة الافتراضية: —
    ملاحظة: ❌ لا يمكن تعديله بعد الإنشاء

  • start_date
    النوع: date
    القيمة الافتراضية: —
    ملاحظة: ❌ لا يمكن تعديله بعد الإنشاء

  • end_date
    النوع: date
    القيمة الافتراضية: —
    ملاحظة: ❌ لا يمكن تعديله بعد الإنشاء

  • is_active
    النوع: tinyint(1)
    القيمة الافتراضية: 1
    ملاحظة: ✅ يمكن تعديله (تفعيل/تعطيل)

  • created_by
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    يشير إلى: users.id
    القيمة الافتراضية: —

  • created_at
    النوع: timestamp
    القيمة الافتراضية: CURRENT_TIMESTAMP

  • updated_at
    النوع: timestamp
    القيمة الافتراضية: CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

========================================
19. جدول: invoice_discount_tiers
========================================

وظيفته:
يحتوي على قواعد الخصم التلقائية للفواتير.
⚠️ ملاحظة مهمة: لا يمكن تعديل قيم الخصم بعد الإنشاء (للحفاظ على التتبع التاريخي).
للتغيير: قم بتعطيل القاعدة القديمة (is_active = false) وأنشئ قاعدة جديدة.

  • id
    النوع: bigint unsigned
    المفتاح: PRIMARY KEY
    القيمة الافتراضية: AUTO_INCREMENT

  • min_amount
    النوع: decimal(12,2)
    القيمة الافتراضية: —
    ملاحظة: ❌ لا يمكن تعديله بعد الإنشاء

  • discount_type
    النوع: enum('percentage','fixed')
    القيمة الافتراضية: —
    ملاحظة: ❌ لا يمكن تعديله بعد الإنشاء

  • discount_percentage
    النوع: decimal(5,2)
    القيمة الافتراضية: NULL
    ملاحظة: ❌ لا يمكن تعديله بعد الإنشاء

  • discount_amount
    النوع: decimal(12,2)
    القيمة الافتراضية: NULL
    ملاحظة: ❌ لا يمكن تعديله بعد الإنشاء

  • start_date
    النوع: date
    القيمة الافتراضية: —
    ملاحظة: ❌ لا يمكن تعديله بعد الإنشاء

  • end_date
    النوع: date
    القيمة الافتراضية: —
    ملاحظة: ❌ لا يمكن تعديله بعد الإنشاء

  • is_active
    النوع: tinyint(1)
    القيمة الافتراضية: 1
    ملاحظة: ✅ يمكن تعديله (تفعيل/تعطيل)

  • created_by
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    يشير إلى: users.id
    القيمة الافتراضية: —

  • created_at
    النوع: timestamp
    القيمة الافتراضية: CURRENT_TIMESTAMP

  • updated_at
    النوع: timestamp
    القيمة الافتراضية: CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

========================================
20. جدول: sales_invoices
========================================

  • id
    النوع: bigint unsigned
    المفتاح: PRIMARY KEY
    القيمة الافتراضية: AUTO_INCREMENT

  • invoice_number
    النوع: varchar(50)
    المفتاح: UNIQUE
    القيمة الافتراضية: —

  • marketer_id
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    يشير إلى: users.id
    القيمة الافتراضية: —

  • store_id
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    يشير إلى: stores.id
    القيمة الافتراضية: —

  • total_amount
    النوع: decimal(12,2)
    القيمة الافتراضية: —

  • subtotal
    النوع: decimal(12,2)
    القيمة الافتراضية: —

  • product_discount
    النوع: decimal(12,2)
    القيمة الافتراضية: 0.00

  • invoice_discount_type
    النوع: enum('percentage','fixed')
    القيمة الافتراضية: NULL

  • invoice_discount_value
    النوع: decimal(10,2)
    القيمة الافتراضية: NULL

  • invoice_discount_amount
    النوع: decimal(12,2)
    القيمة الافتراضية: 0.00

  • invoice_discount_tier_id
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    يشير إلى: invoice_discount_tiers.id
    القيمة الافتراضية: NULL

  • status
    النوع: enum('pending','approved','rejected','cancelled')
    القيمة الافتراضية: 'pending'

  • keeper_id
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    يشير إلى: users.id
    القيمة الافتراضية: NULL
    ملاحظة: يسجل من قام بالموافقة أو الرفض (حسب status)

  • stamped_invoice_image
    النوع: varchar(255)
    القيمة الافتراضية: NULL

  • notes
    النوع: text
    القيمة الافتراضية: NULL

  • created_at
    النوع: timestamp
    القيمة الافتراضية: CURRENT_TIMESTAMP

  • updated_at
    النوع: timestamp
    القيمة الافتراضية: CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

========================================
21. جدول: sales_invoice_items
========================================

  • id
    النوع: bigint unsigned
    المفتاح: PRIMARY KEY
    القيمة الافتراضية: AUTO_INCREMENT

  • invoice_id
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    يشير إلى: sales_invoices.id
    القيمة الافتراضية: —

  • product_id
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    يشير إلى: products.id
    القيمة الافتراضية: —

  • quantity
    النوع: int
    القيمة الافتراضية: —

  • free_quantity
    النوع: int
    القيمة الافتراضية: 0

  • unit_price
    النوع: decimal(10,2)
    القيمة الافتراضية: —

  • total_price
    النوع: decimal(12,2)
    القيمة الافتراضية: —

  • promotion_id
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    يشير إلى: product_promotions.id
    القيمة الافتراضية: NULL

========================================
22. جدول: store_pending_stock
========================================

  • id
    النوع: bigint unsigned
    المفتاح: PRIMARY KEY
    القيمة الافتراضية: AUTO_INCREMENT

  • store_id
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    يشير إلى: stores.id
    القيمة الافتراضية: —

  • sales_invoice_id
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    يشير إلى: sales_invoices.id
    القيمة الافتراضية: —

  • product_id
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    يشير إلى: products.id
    القيمة الافتراضية: —

  • quantity
    النوع: int
    القيمة الافتراضية: —

  • created_at
    النوع: timestamp
    القيمة الافتراضية: CURRENT_TIMESTAMP

========================================
23. جدول: store_actual_stock
========================================

  • id
    النوع: bigint unsigned
    المفتاح: PRIMARY KEY
    القيمة الافتراضية: AUTO_INCREMENT

  • store_id
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    يشير إلى: stores.id
    القيمة الافتراضية: —

  • product_id
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    يشير إلى: products.id
    القيمة الافتراضية: —

  • quantity
    النوع: int
    القيمة الافتراضية: 0

========================================
24. جدول: store_debt_ledger (محدث في V3)
========================================

  • id
    النوع: bigint unsigned
    المفتاح: PRIMARY KEY
    القيمة الافتراضية: AUTO_INCREMENT

  • store_id
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    المفتاح: INDEX
    يشير إلى: stores.id
    القيمة الافتراضية: —

  • entry_type
    النوع: enum('sale','return','payment','opening_balance')
    المفتاح: INDEX
    القيمة الافتراضية: —

  • reference_type
    النوع: enum('sales_invoice','sales_return','store_payment','opening_balance')
    المفتاح: INDEX
    القيمة الافتراضية: —

  • reference_id
    النوع: bigint unsigned
    القيمة الافتراضية: NULL

  • amount
    النوع: decimal(12,2)
    القيمة الافتراضية: —

  • balance_after
    النوع: decimal(12,2)
    القيمة الافتراضية: —

  • notes
    النوع: text
    القيمة الافتراضية: NULL

  • created_at
    النوع: timestamp
    المفتاح: INDEX
    القيمة الافتراضية: CURRENT_TIMESTAMP

ملاحظة: ✅ تم استبدال (sales_invoice_id, return_id, payment_id) بـ (reference_type, reference_id)
ملاحظة: ✅ تم إضافة balance_after لتتبع الرصيد بعد كل حركة
ملاحظة: ✅ تم إضافة opening_balance لدعم الأرصدة الافتتاحية

========================================
E. عملية: إيصال القبض (تسديد دين المتجر)
========================================

========================================
25. جدول: store_payments
========================================

  • id
    النوع: bigint unsigned
    المفتاح: PRIMARY KEY
    القيمة الافتراضية: AUTO_INCREMENT

  • payment_number
    النوع: varchar(50)
    المفتاح: UNIQUE
    القيمة الافتراضية: —

  • store_id
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    يشير إلى: stores.id
    القيمة الافتراضية: —

  • marketer_id
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    يشير إلى: users.id
    القيمة الافتراضية: —

  • keeper_id
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    يشير إلى: users.id
    القيمة الافتراضية: —

  • amount
    النوع: decimal(12,2)
    القيمة الافتراضية: —

  • payment_method
    النوع: enum('cash','transfer','certified_check')
    القيمة الافتراضية: —

  • status
    النوع: enum('pending','approved','rejected','cancelled')
    القيمة الافتراضية: 'pending'

  • receipt_image
    النوع: varchar(255)
    القيمة الافتراضية: NULL

  • confirmed_at
    النوع: timestamp
    القيمة الافتراضية: NULL

  • notes
    النوع: text
    القيمة الافتراضية: NULL

  • created_at
    النوع: timestamp
    القيمة الافتراضية: CURRENT_TIMESTAMP

========================================
26. جدول: marketer_commissions
========================================

  • id
    النوع: bigint unsigned
    المفتاح: PRIMARY KEY
    القيمة الافتراضية: AUTO_INCREMENT

  • marketer_id
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    يشير إلى: users.id
    القيمة الافتراضية: —

  • store_id
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    يشير إلى: stores.id
    القيمة الافتراضية: —

  • keeper_id
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    يشير إلى: users.id
    القيمة الافتراضية: —

  • payment_amount
    النوع: decimal(12,2)
    القيمة الافتراضية: —

  • payment_id
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    يشير إلى: store_payments.id
    القيمة الافتراضية: —

  • commission_rate
    النوع: decimal(5,2)
    القيمة الافتراضية: —

  • commission_amount
    النوع: decimal(12,2)
    القيمة الافتراضية: —

  • created_at
    النوع: timestamp
    القيمة الافتراضية: CURRENT_TIMESTAMP

========================================
F. عملية: سحب أرباح (عمولات) المسوق
========================================

========================================
27. جدول: marketer_withdrawal_requests
========================================

  • id
    النوع: bigint unsigned
    المفتاح: PRIMARY KEY
    القيمة الافتراضية: AUTO_INCREMENT

  • marketer_id
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    يشير إلى: users.id
    القيمة الافتراضية: —

  • requested_amount
    النوع: decimal(12,2)
    القيمة الافتراضية: —

  • status
    النوع: enum('pending','approved','rejected','cancelled')
    القيمة الافتراضية: 'pending'

  • approved_by
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    يشير إلى: users.id
    القيمة الافتراضية: NULL

  • approved_at
    النوع: timestamp
    القيمة الافتراضية: NULL

  • rejected_by
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    يشير إلى: users.id
    القيمة الافتراضية: NULL

  • rejected_at
    النوع: timestamp
    القيمة الافتراضية: NULL

  • signed_receipt_image
    النوع: varchar(255)
    القيمة الافتراضية: NULL

  • notes
    النوع: text
    القيمة الافتراضية: NULL

  • created_at
    النوع: timestamp
    القيمة الافتراضية: CURRENT_TIMESTAMP

  • updated_at
    النوع: timestamp
    القيمة الافتراضية: CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

========================================
G. عملية: إرجاع بضاعة من المتجر إلى المسوق
========================================

========================================
28. جدول: sales_returns
========================================

  • id
    النوع: bigint unsigned
    المفتاح: PRIMARY KEY
    القيمة الافتراضية: AUTO_INCREMENT

  • return_number
    النوع: varchar(50)
    المفتاح: UNIQUE
    القيمة الافتراضية: —

  • sales_invoice_id
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    يشير إلى: sales_invoices.id
    القيمة الافتراضية: —

  • store_id
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    يشير إلى: stores.id
    القيمة الافتراضية: —

  • marketer_id
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    يشير إلى: users.id
    القيمة الافتراضية: —

  • total_amount
    النوع: decimal(12,2)
    القيمة الافتراضية: —

  • status
    النوع: enum('pending','approved','rejected','cancelled')
    القيمة الافتراضية: 'pending'

  • keeper_id
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    يشير إلى: users.id
    القيمة الافتراضية: NULL

  • stamped_image
    النوع: varchar(255)
    القيمة الافتراضية: NULL

  • confirmed_at
    النوع: timestamp
    القيمة الافتراضية: NULL

  • notes
    النوع: text
    القيمة الافتراضية: NULL

  • created_at
    النوع: timestamp
    القيمة الافتراضية: CURRENT_TIMESTAMP

  • updated_at
    النوع: timestamp
    القيمة الافتراضية: CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

========================================
29. جدول: sales_return_items
========================================

  • id
    النوع: bigint unsigned
    المفتاح: PRIMARY KEY
    القيمة الافتراضية: AUTO_INCREMENT

  • return_id
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    يشير إلى: sales_returns.id
    القيمة الافتراضية: —

  • sales_invoice_item_id
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    يشير إلى: sales_invoice_items.id
    القيمة الافتراضية: —

  • product_id
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    يشير إلى: products.id
    القيمة الافتراضية: —

  • quantity
    النوع: int
    القيمة الافتراضية: —

  • unit_price
    النوع: decimal(10,2)
    القيمة الافتراضية: —

========================================
30. جدول: store_return_pending_stock
========================================

  • id
    النوع: bigint unsigned
    المفتاح: PRIMARY KEY
    القيمة الافتراضية: AUTO_INCREMENT

  • return_id
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    يشير إلى: sales_returns.id
    القيمة الافتراضية: —

  • store_id
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    يشير إلى: stores.id
    القيمة الافتراضية: —

  • product_id
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    يشير إلى: products.id
    القيمة الافتراضية: —

  • quantity
    النوع: int
    القيمة الافتراضية: —

  • created_at
    النوع: timestamp
    القيمة الافتراضية: CURRENT_TIMESTAMP

========================================
H. نظام العملاء (المبيعات المباشرة)
========================================

========================================
31. جدول: customer_invoices
========================================

  • id
    النوع: bigint unsigned
    المفتاح: PRIMARY KEY
    القيمة الافتراضية: AUTO_INCREMENT

  • invoice_number
    النوع: varchar(50)
    المفتاح: UNIQUE
    القيمة الافتراضية: —

  • customer_id
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    المفتاح: INDEX
    يشير إلى: customers.id
    القيمة الافتراضية: —
    ملاحظة: RESTRICT ON DELETE, CASCADE ON UPDATE

  • sales_user_id
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    المفتاح: INDEX
    يشير إلى: users.id
    القيمة الافتراضية: —
    ملاحظة: RESTRICT ON DELETE, CASCADE ON UPDATE

  • subtotal
    النوع: decimal(12,2)
    القيمة الافتراضية: —

  • discount_amount
    النوع: decimal(12,2)
    القيمة الافتراضية: 0.00

  • total_amount
    النوع: decimal(12,2)
    القيمة الافتراضية: —

  • payment_type
    النوع: enum('cash','credit','partial')
    المفتاح: INDEX
    القيمة الافتراضية: —

  • status
    النوع: enum('completed','cancelled')
    المفتاح: INDEX
    القيمة الافتراضية: 'completed'

  • notes
    النوع: text
    القيمة الافتراضية: NULL

  • created_at
    النوع: timestamp
    المفتاح: INDEX
    القيمة الافتراضية: CURRENT_TIMESTAMP

  • updated_at
    النوع: timestamp
    القيمة الافتراضية: CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

========================================
32. جدول: customer_invoice_items
========================================

  • id
    النوع: bigint unsigned
    المفتاح: PRIMARY KEY
    القيمة الافتراضية: AUTO_INCREMENT

  • invoice_id
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    المفتاح: INDEX
    يشير إلى: customer_invoices.id
    القيمة الافتراضية: —
    ملاحظة: CASCADE ON DELETE, CASCADE ON UPDATE

  • product_id
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    المفتاح: INDEX
    يشير إلى: products.id
    القيمة الافتراضية: —
    ملاحظة: RESTRICT ON DELETE, CASCADE ON UPDATE

  • quantity
    النوع: int
    القيمة الافتراضية: —

  • unit_price
    النوع: decimal(10,2)
    القيمة الافتراضية: —

  • total_price
    النوع: decimal(12,2)
    القيمة الافتراضية: —

  • created_at
    النوع: timestamp
    القيمة الافتراضية: CURRENT_TIMESTAMP

========================================
33. جدول: customer_payments
========================================

  • id
    النوع: bigint unsigned
    المفتاح: PRIMARY KEY
    القيمة الافتراضية: AUTO_INCREMENT

  • payment_number
    النوع: varchar(50)
    المفتاح: UNIQUE
    القيمة الافتراضية: —

  • customer_id
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    المفتاح: INDEX
    يشير إلى: customers.id
    القيمة الافتراضية: —
    ملاحظة: RESTRICT ON DELETE, CASCADE ON UPDATE

  • sales_user_id
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    المفتاح: INDEX
    يشير إلى: users.id
    القيمة الافتراضية: —
    ملاحظة: RESTRICT ON DELETE, CASCADE ON UPDATE

  • amount
    النوع: decimal(12,2)
    القيمة الافتراضية: —

  • payment_method
    النوع: enum('cash','transfer','check')
    القيمة الافتراضية: —

  • status
    النوع: enum('completed','cancelled')
    القيمة الافتراضية: 'completed'

  • notes
    النوع: text
    القيمة الافتراضية: NULL

  • created_at
    النوع: timestamp
    المفتاح: INDEX
    القيمة الافتراضية: CURRENT_TIMESTAMP

  • updated_at
    النوع: timestamp
    القيمة الافتراضية: CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

========================================
34. جدول: customer_returns
========================================

  • id
    النوع: bigint unsigned
    المفتاح: PRIMARY KEY
    القيمة الافتراضية: AUTO_INCREMENT

  • return_number
    النوع: varchar(50)
    المفتاح: UNIQUE
    القيمة الافتراضية: —

  • invoice_id
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    المفتاح: INDEX
    يشير إلى: customer_invoices.id
    القيمة الافتراضية: —
    ملاحظة: RESTRICT ON DELETE, CASCADE ON UPDATE

  • customer_id
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    المفتاح: INDEX
    يشير إلى: customers.id
    القيمة الافتراضية: —
    ملاحظة: RESTRICT ON DELETE, CASCADE ON UPDATE

  • sales_user_id
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    المفتاح: INDEX
    يشير إلى: users.id
    القيمة الافتراضية: —
    ملاحظة: RESTRICT ON DELETE, CASCADE ON UPDATE

  • total_amount
    النوع: decimal(12,2)
    القيمة الافتراضية: —

  • status
    النوع: enum('completed','cancelled')
    المفتاح: INDEX
    القيمة الافتراضية: 'completed'

  • notes
    النوع: text
    القيمة الافتراضية: NULL

  • created_at
    النوع: timestamp
    القيمة الافتراضية: CURRENT_TIMESTAMP

  • updated_at
    النوع: timestamp
    القيمة الافتراضية: CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

========================================
35. جدول: customer_return_items
========================================

  • id
    النوع: bigint unsigned
    المفتاح: PRIMARY KEY
    القيمة الافتراضية: AUTO_INCREMENT

  • return_id
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    المفتاح: INDEX
    يشير إلى: customer_returns.id
    القيمة الافتراضية: —
    ملاحظة: CASCADE ON DELETE, CASCADE ON UPDATE

  • invoice_item_id
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    يشير إلى: customer_invoice_items.id
    القيمة الافتراضية: —
    ملاحظة: RESTRICT ON DELETE, CASCADE ON UPDATE

  • product_id
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    المفتاح: INDEX
    يشير إلى: products.id
    القيمة الافتراضية: —
    ملاحظة: RESTRICT ON DELETE, CASCADE ON UPDATE

  • quantity
    النوع: int
    القيمة الافتراضية: —

  • unit_price
    النوع: decimal(10,2)
    القيمة الافتراضية: —

  • total_price
    النوع: decimal(12,2)
    القيمة الافتراضية: —

  • created_at
    النوع: timestamp
    القيمة الافتراضية: CURRENT_TIMESTAMP

========================================
36. جدول: customer_debt_ledger (محدث في V3)
========================================

  • id
    النوع: bigint unsigned
    المفتاح: PRIMARY KEY
    القيمة الافتراضية: AUTO_INCREMENT

  • customer_id
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    المفتاح: INDEX
    يشير إلى: customers.id
    القيمة الافتراضية: —
    ملاحظة: RESTRICT ON DELETE, CASCADE ON UPDATE

  • entry_type
    النوع: enum('sale','return','payment','opening_balance')
    المفتاح: INDEX
    القيمة الافتراضية: —

  • reference_type
    النوع: enum('customer_invoice','customer_return','customer_payment','opening_balance')
    المفتاح: INDEX
    القيمة الافتراضية: —

  • reference_id
    النوع: bigint unsigned
    القيمة الافتراضية: NULL

  • amount
    النوع: decimal(12,2)
    القيمة الافتراضية: —

  • balance_after
    النوع: decimal(12,2)
    القيمة الافتراضية: —

  • notes
    النوع: text
    القيمة الافتراضية: NULL

  • created_at
    النوع: timestamp
    المفتاح: INDEX
    القيمة الافتراضية: CURRENT_TIMESTAMP

  • updated_at
    النوع: timestamp
    القيمة الافتراضية: CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

ملاحظة: ✅ تم استبدال (invoice_id, return_id, payment_id) بـ (reference_type, reference_id)
ملاحظة: ✅ تم إضافة balance_after لتتبع الرصيد بعد كل حركة
ملاحظة: ✅ تم إضافة opening_balance لدعم الأرصدة الافتتاحية

========================================
I. جداول التتبع والأرشفة (جديدة في V3)
========================================

========================================
37. جدول: inventory_movements (جديد في V3)
========================================

وظيفته:
تتبع شامل لكل حركات المخزون في النظام من أي مصدر إلى أي وجهة.
يسجل كل عملية نقل منتج بين المخزن الرئيسي والمسوقين والمتاجر.

  • id
    النوع: bigint unsigned
    المفتاح: PRIMARY KEY
    القيمة الافتراضية: AUTO_INCREMENT

  • product_id
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    المفتاح: INDEX
    يشير إلى: products.id
    القيمة الافتراضية: —

  • from_type
    النوع: enum('external','main_stock','marketer','store')
    المفتاح: INDEX
    القيمة الافتراضية: —
    ملاحظة: external = من خارج النظام (فواتير افتتاحية)

  • from_id
    النوع: bigint unsigned
    القيمة الافتراضية: NULL
    ملاحظة: marketer_id أو store_id (NULL للمخزن الرئيسي أو external)

  • to_type
    النوع: enum('main_stock','marketer','store','external')
    المفتاح: INDEX
    القيمة الافتراضية: —
    ملاحظة: external = خارج النظام (تلف، هدايا، إلخ)

  • to_id
    النوع: bigint unsigned
    القيمة الافتراضية: NULL
    ملاحظة: marketer_id أو store_id (NULL للمخزن الرئيسي أو external)

  • quantity
    النوع: int
    القيمة الافتراضية: —

  • reference_type
    النوع: enum('opening_balance','factory_invoice','marketer_request','marketer_return','sales_invoice','sales_return')
    المفتاح: INDEX
    القيمة الافتراضية: —

  • reference_id
    النوع: bigint unsigned
    القيمة الافتراضية: NULL

  • created_at
    النوع: timestamp
    المفتاح: INDEX
    القيمة الافتراضية: CURRENT_TIMESTAMP

========================================
38. جدول: inventory_snapshots (جديد في V3)
========================================

وظيفته:
حفظ لقطة للمخزون في نهاية كل سنة مالية للتقارير والمقارنات.
يستخدم للتدقيق ومقارنة النمو/النقص بين السنوات.

  • id
    النوع: bigint unsigned
    المفتاح: PRIMARY KEY
    القيمة الافتراضية: AUTO_INCREMENT

  • fiscal_year
    النوع: int
    المفتاح: INDEX
    القيمة الافتراضية: —

  • snapshot_date
    النوع: date
    القيمة الافتراضية: —

  • stock_type
    النوع: enum('main','marketer_actual','marketer_reserved','store_actual','store_pending')
    المفتاح: INDEX
    القيمة الافتراضية: —

  • entity_id
    النوع: bigint unsigned
    القيمة الافتراضية: NULL
    ملاحظة: marketer_id أو store_id (NULL للمخزن الرئيسي)

  • product_id
    النوع: bigint unsigned
    المفتاح: FOREIGN KEY
    المفتاح: INDEX
    يشير إلى: products.id
    القيمة الافتراضية: —

  • quantity
    النوع: int
    القيمة الافتراضية: —

  • created_at
    النوع: timestamp
    القيمة الافتراضية: CURRENT_TIMESTAMP

========================================
J. جداول النظام (Laravel Framework)
========================================

========================================
39. جدول: cache
========================================

  • key
    النوع: varchar(255)
    المفتاح: PRIMARY KEY
    القيمة الافتراضية: —

  • value
    النوع: mediumtext
    القيمة الافتراضية: —

  • expiration
    النوع: int
    القيمة الافتراضية: —

========================================
40. جدول: cache_locks
========================================

  • key
    النوع: varchar(255)
    المفتاح: PRIMARY KEY
    القيمة الافتراضية: —

  • owner
    النوع: varchar(255)
    القيمة الافتراضية: —

  • expiration
    النوع: int
    القيمة الافتراضية: —

========================================
41. جدول: jobs
========================================

  • id
    النوع: bigint unsigned
    المفتاح: PRIMARY KEY
    القيمة الافتراضية: AUTO_INCREMENT

  • queue
    النوع: varchar(255)
    المفتاح: INDEX
    القيمة الافتراضية: —

  • payload
    النوع: longtext
    القيمة الافتراضية: —

  • attempts
    النوع: tinyint unsigned
    القيمة الافتراضية: —

  • reserved_at
    النوع: int unsigned
    القيمة الافتراضية: NULL

  • available_at
    النوع: int unsigned
    القيمة الافتراضية: —

  • created_at
    النوع: int unsigned
    القيمة الافتراضية: —

========================================
42. جدول: job_batches
========================================

  • id
    النوع: varchar(255)
    المفتاح: PRIMARY KEY
    القيمة الافتراضية: —

  • name
    النوع: varchar(255)
    القيمة الافتراضية: —

  • total_jobs
    النوع: int
    القيمة الافتراضية: —

  • pending_jobs
    النوع: int
    القيمة الافتراضية: —

  • failed_jobs
    النوع: int
    القيمة الافتراضية: —

  • failed_job_ids
    النوع: longtext
    القيمة الافتراضية: —

  • options
    النوع: mediumtext
    القيمة الافتراضية: NULL

  • cancelled_at
    النوع: int
    القيمة الافتراضية: NULL

  • created_at
    النوع: int
    القيمة الافتراضية: —

  • finished_at
    النوع: int
    القيمة الافتراضية: NULL

========================================
43. جدول: failed_jobs
========================================

  • id
    النوع: bigint unsigned
    المفتاح: PRIMARY KEY
    القيمة الافتراضية: AUTO_INCREMENT

  • uuid
    النوع: varchar(255)
    المفتاح: UNIQUE
    القيمة الافتراضية: —

  • connection
    النوع: text
    القيمة الافتراضية: —

  • queue
    النوع: text
    القيمة الافتراضية: —

  • payload
    النوع: longtext
    القيمة الافتراضية: —

  • exception
    النوع: longtext
    القيمة الافتراضية: —

  • failed_at
    النوع: timestamp
    القيمة الافتراضية: CURRENT_TIMESTAMP

========================================
ملخص إحصائي
========================================

إجمالي الجداول: 43 جدول

التصنيف حسب الوظيفة:
- جداول أساسية: 8 جداول (roles, users, marketers, products, main_stock, stores, customers, notifications)
- نظام المصنع: 2 جدول (factory_invoices, factory_invoice_items)
- نظام المسوقين: 7 جداول (marketer_requests, marketer_request_items, marketer_reserved_stock, marketer_actual_stock, marketer_return_requests, marketer_return_items, warehouse_stock_logs)
- نظام المتاجر: 11 جدول (sales_invoices, sales_invoice_items, store_pending_stock, store_actual_stock, store_debt_ledger, store_payments, marketer_commissions, marketer_withdrawal_requests, sales_returns, sales_return_items, store_return_pending_stock)
- نظام العملاء: 6 جداول (customer_invoices, customer_invoice_items, customer_payments, customer_returns, customer_return_items, customer_debt_ledger)
- نظام الخصومات: 2 جدول (product_promotions, invoice_discount_tiers)
- التتبع والأرشفة: 2 جدول (inventory_movements, inventory_snapshots)
- جداول Laravel: 5 جداول (cache, cache_locks, jobs, job_batches, failed_jobs)

========================================
التحسينات الرئيسية في V3
========================================

✅ 1. فصل بيانات المسوقين:
   - إضافة جدول marketers منفصل
   - حذف commission_rate من جدول users
   - تجنب NULL values في 75% من السجلات

✅ 2. تحسين جداول الديون:
   - استبدال (invoice_id, return_id, payment_id) بـ (reference_type, reference_id)
   - إضافة balance_after لتتبع الرصيد بعد كل حركة
   - دعم الأرصدة الافتتاحية (opening_balance)
   - تطبيق على store_debt_ledger و customer_debt_ledger

✅ 3. التتبع الشامل للمخزون:
   - إضافة جدول inventory_movements
   - تسجيل كل حركة مخزون من أي مصدر لأي وجهة
   - دعم الفواتير الافتتاحية

✅ 4. لقطات المخزون السنوية:
   - إضافة جدول inventory_snapshots
   - حفظ صورة للمخزون في نهاية كل سنة
   - تسهيل التقارير والمقارنات السنوية

========================================
نظام الأرشفة السنوية
========================================

استراتيجية الأرشفة:
- السنة الحالية: تبقى في الجداول الأصلية
- السنوات السابقة: تنقل إلى قواعد بيانات منفصلة

مثال:
tdm_2025 (أرشيف 2025)
tdm_2026 (السنة الحالية)
tdm_2027 (السنة القادمة)

عملية التدوير السنوية:
1. إلغاء الطلبات المعلقة وإرجاع المخزون المحجوز
2. إنشاء فواتير افتتاحية:
   - فاتورة مصنع افتتاحية للمخزن الرئيسي
   - فواتير طلبات افتتاحية لمخزون المسوقين
3. إنشاء حركات افتتاحية في جداول الديون
4. التقاط snapshot للمخزون
5. نقل بيانات السنة المنتهية إلى قاعدة بيانات الأرشيف

========================================
انتهى الاستعلام
تم التوثيق بتاريخ: 2026-03-07
========================================
