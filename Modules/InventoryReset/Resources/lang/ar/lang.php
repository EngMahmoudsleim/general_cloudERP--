<?php

return [
    // Module Information
    'module_name' => 'إعادة تعيين المخزون',
    'module_description' => 'إعادة تعيين جميع كميات المخزون إلى الصفر بنقرات قليلة فقط',

    // Dashboard
    'dashboard_subtitle' => 'إدارة وتتبع عمليات إعادة تعيين المخزون',
    'inventory_reset' => 'إعادة تعيين المخزون',

    // Statistics
    'total_products' => 'إجمالي المنتجات',
    'products_with_stock' => 'المنتجات ذات المخزون غير الصفري',
    'products_without_stock' => 'المنتجات ذات المخزون الصفري',
    'total_stock_value' => 'إجمالي قيمة المخزون',
    'last_reset_date' => 'تاريخ آخر إعادة تعيين',
    'never' => 'لم يحدث',

    // Quick Actions
    'products_with_stock_warning' => 'لديك :count منتجات بمخزون غير صفري (موجب أو سالب) يمكن إعادة تعيينها.',
    'all_stock_zero_info' => 'جميع المنتجات حالياً لديها مخزون صفري. يمكنك لا تزال إجراء إعادة تعيين لإنشاء سجل مراجعة.',

    // Reset History
    'recent_reset_history' => 'تاريخ إعادة التعيين الحديث',
    'no_reset_history' => 'لا يوجد تاريخ إعادة تعيين',
    'no_reset_history_desc' => 'لم تقم بأي عمليات إعادة تعيين مخزون بعد.',
    'reason' => 'السبب',
    'items_reset' => 'العناصر المعاد تعيينها',
    'filter_by_user' => 'تصفية حسب المستخدم',
    'filter_by_status' => 'تصفية حسب الحالة',
    'filter_by_type' => 'تصفية حسب النوع',
	'filter_by_reset_mode' => 'تصفية حسب وضع إعادة الضبط',
    'filter_by_operation_type' => 'تصفية حسب نوع العملية',
    'clear_filters' => 'مسح المرشحات',

    // Reset Form
    'inventory_reset_form' => 'نموذج إعادة تعيين المخزون',
    'reset_type' => 'نوع إعادة التعيين',
    'reset_mode' => 'وضع إعادة التعيين',

    // Form Fields
    'select_location' => 'اختيار الموقع',
    'select_products' => 'اختيار المنتجات',
    'selected_products' => 'المنتجات المحددة',
    'sku' => 'رمز المنتج',
    'reason_placeholder' => 'تقديم سبب لإعادة تعيين المخزون هذه (مثل: الجرد السنوي، مراجعة المخزون، ترحيل النظام)...',
    'reason_help' => 'سيتم تسجيل هذا السبب وسيكون مرئياً في التقارير لأغراض المراجعة.',
    'confirm_reset_checkbox' => 'أفهم أن هذا الإجراء سيعيد تعيين جميع المخزون المحدد إلى الصفر بشكل دائم ولا يمكن التراجع عنه.',

    // Stock Fix Options
    'fix_stock_mismatches' => 'إصلاح عدم تطابق المخزون قبل إعادة التعيين',
    'fix_stock_mismatches_desc' => 'تصحيح تناقضات المخزون تلقائياً قبل إجراء إعادة التعيين',
    'fix_stock_mismatches_help' => 'هذا سيضمن أن جميع المخزون المحسوب يطابق المخزون الفعلي قبل إعادة التعيين',

    // Summary Panel
    'execute_reset' => 'تنفيذ إعادة التعيين',

    // Help
    'help' => 'المساعدة والنصائح',
    'help_tip_1' => 'تأكد من نسخ احتياطي لبياناتك قبل إجراء إعادة التعيين.',
    'help_tip_2' => 'سيتم إنشاء تعديلات المخزون لتتبع إعادة التعيين.',
    'help_tip_3' => 'يمكنك عرض تقارير مفصلة لجميع عمليات إعادة التعيين.',

    // Danger Zone
    'danger_zone_description' => 'عملية إعادة تعيين المخزون ستعين بشكل دائم جميع كميات المنتجات المحددة إلى الصفر عبر المواقع المحددة.',
    'warning_irreversible' => 'هذا الإجراء غير قابل للإلغاء!',

    // Processing
    'processing_status' => 'معالجة إعادة التعيين...',
    'final_confirmation' => 'هل أنت متأكد تماماً من أنك تريد المتابعة مع إعادة تعيين المخزون هذه؟ لا يمكن التراجع عن هذا الإجراء.',

    // Messages
    'reset_completed_successfully' => 'تمت إعادة تعيين المخزون بنجاح!',
    'reset_failed' => 'فشلت إعادة تعيين المخزون',
    'success' => 'نجح',
    'error' => 'خطأ',

    // Reset Details
    'reset_details' => 'تفاصيل إعادة التعيين',
    'reset_information' => 'معلومات إعادة التعيين',
    'reset_statistics' => 'إحصائيات إعادة التعيين',
    'reset_id' => 'معرف إعادة التعيين',
    'performed_by' => 'تم تنفيذه بواسطة',
    'started_at' => 'بدأ في',
    'completed_at' => 'اكتمل في',
    'location' => 'الموقع',
    'status' => 'الحالة',
    'error_message' => 'رسالة الخطأ',
    'items_processed' => 'العناصر المعالجة',
    'total_quantity_reset' => 'إجمالي الكمية',
    'unique_products' => 'المنتجات الفريدة',
    'duration' => 'المدة',
    'back_to_dashboard' => 'العودة إلى لوحة التحكم',

    // Reset Items Table
    'reset_items_details' => 'تفاصيل عناصر إعادة التعيين',
    'product' => 'المنتج',
    'quantity_before' => 'الكمية قبل',
    'quantity_after' => 'الكمية بعد',
    'quantity_reset' => 'كمية إعادة التعيين',
    'variable_product' => 'منتج متغير',

    // Permissions
    'inventory_reset_module' => 'وحدة إعادة تعيين المخزون',
    'access_inventory_reset' => 'الوصول إلى وحدة إعادة تعيين المخزون',
    'view_inventory_reset_history' => 'عرض تاريخ إعادة تعيين المخزون',
    'perform_inventory_reset' => 'تنفيذ إعادة تعيين المخزون',
    'delete_inventory_reset_records' => 'حذف سجلات إعادة تعيين المخزون',
    'inventory_management' => 'إدارة المخزون',
    'perform_reset' => 'تنفيذ إعادة التعيين',

    // Configuration
    'default_reset_reason' => 'سبب إعادة التعيين الافتراضي',
    'default_reset_reason_desc' => 'نص السبب الافتراضي الذي سيتم ملؤه مسبقاً في نماذج إعادة التعيين',
    'require_confirmation' => 'يتطلب تأكيد',
    'require_confirmation_desc' => 'يتطلب من المستخدمين تحديد مربع تأكيد قبل تنفيذ إعادة التعيين',
    'auto_create_adjustments' => 'إنشاء تعديلات المخزون تلقائياً',
    'auto_create_adjustments_desc' => 'إنشاء معاملات تعديل المخزون تلقائياً لإعادة التعيين',
    'max_products_per_reset' => 'الحد الأقصى للمنتجات لكل إعادة تعيين',
    'max_products_per_reset_desc' => 'الحد الأقصى لعدد المنتجات التي يمكن إعادة تعيينها في عملية واحدة',

    // Installation
    'module_installed_successfully' => 'تم تثبيت وحدة إعادة تعيين المخزون بنجاح!',
    'module_updated_successfully' => 'تم تحديث وحدة إعادة تعيين المخزون بنجاح إلى الإصدار ',
    'module_uninstalled_successfully' => 'تم إلغاء تثبيت وحدة إعادة تعيين المخزون بنجاح!',

    // Status
    'completed' => 'مكتمل',
    'processing' => 'معالجة',
    'failed' => 'فشل',
    'pending' => 'معلق',

    // Alert
    'info_alert' => 'أنت على وشك :operation لـ :type مع :mode في :location.',
    'confirm_alert' => 'هذا الإجراء سيؤثر على جميع المنتجات المطابقة في مخزونك ولا يمكن التراجع عنه!',
    'final_confirmation_alert' => 'مطلوب تأكيد نهائي',
    'type_reset_hint' => 'اكتب RESET لتأكيد هذا الإجراء المدمر:',
    'reset_placeholder' => 'اكتب RESET هنا...',
    'confirm_reset' => 'تأكيد إعادة التعيين',
    'must_type_reset_error' => 'يجب كتابة RESET للتأكيد!',
    'processing_plz_wait' => 'يرجى الانتظار بينما نعيد تعيين مخزونك...',
    'reload_page' => 'إعادة تحميل الصفحة',
    'select_atleast_product' => 'يرجى اختيار منتج واحد على الأقل لإعادة التعيين.',

    // Negative Inventory Display
    'products_with_negative_inventory' => 'المنتجات ذات المخزون السالب',
    'loading_negative_products' => 'تحميل منتجات المخزون السالب...',
    'no_negative_inventory_found' => 'لم يتم العثور على مخزون سالب! جميع المنتجات لديها كميات غير سالبة.',
    'error_loading_negative_products' => 'خطأ في تحميل منتجات المخزون السالب',
    'negative_quantity' => 'كمية سالبة',
    'locations' => 'المواقع',
    'products_with_negative_inventory_count' => 'منتجات موجودة بمخزون سالب',
    'select_specific_products_above' => 'يرجى اختيار منتجات محددة أعلاه، ثم سيتم عرض المخزون السالب للمنتجات المحددة فقط.',
    'no_products_have_negative' => 'لا توجد منتجات محددة لديها مخزون سالب.',

    // Reset Type Section
    'all_products' => 'جميع المنتجات',
    'all_products_desc' => 'تطبيق العملية على جميع المنتجات في العمل/الموقع',
    'selected_products_type' => 'المنتجات المحددة',
    'selected_products_type_desc' => 'تطبيق العملية فقط على المنتجات المحددة تحديداً',

    // Reset Mode Section
    'all_stock_levels' => 'جميع مستويات المخزون',
    'all_stock_levels_desc' => 'تعديل أي مستوى مخزون (موجب أو سالب أو صفر)',
    'positive_stock_only' => 'المخزون الموجب فقط',
    'positive_stock_only_desc' => 'تعديل المنتجات ذات كميات المخزون الموجبة فقط',
    'negative_stock_only' => 'المخزون السالب فقط',
    'negative_stock_only_desc' => 'تعديل المنتجات ذات كميات المخزون السالبة فقط',
    'zero_stock_only' => 'المخزون الصفر فقط',
    'zero_stock_only_desc' => 'تعديل المنتجات ذات المخزون الصفر بالضبط',

    // Operation Type Section
    'operation_type' => 'نوع العملية',
    'reset_to_zero' => 'إعادة تعيين إلى الصفر',
    'reset_to_zero_desc' => 'تعيين جميع كميات المخزون المطابقة إلى صفر',
    'set_to_quantity' => 'تعيين إلى كمية مخصصة',
    'set_to_quantity_desc' => 'تعيين جميع المخزون المطابق إلى كمية محددة',

    // Target Quantity Section
    'target_quantity' => 'الكمية المستهدفة:',
    'target_quantity_placeholder' => 'أدخل الكمية المراد تعيينها',
    'target_quantity_desc' => 'سيتم تعيين جميع المنتجات المطابقة إلى هذه الكمية',
    'error_fetching_negative_products' => 'خطأ في جلب منتجات المخزون السالب',

    // Display Labels
    'stock_adjustment' => 'تعديل المخزون',
    'inventory_reset_label' => 'إعادة تعيين المخزون',
    'not_available' => 'غير متوفر',
];