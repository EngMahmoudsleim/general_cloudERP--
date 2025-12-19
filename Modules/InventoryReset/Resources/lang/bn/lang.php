<?php

return [
    // Module Information
    'module_name' => 'ইনভেন্টরি রিসেট',
    'module_description' => 'মাত্র কয়েকটি ক্লিকে আপনার সমস্ত ইনভেন্টরি পরিমাণ শূন্যে রিসেট করুন',

    // Dashboard
    'dashboard_subtitle' => 'আপনার ইনভেন্টরি রিসেট অপারেশন ম্যানেজ করুন এবং ট্র্যাক করুন',
    'inventory_reset' => 'ইনভেন্টরি রিসেট',

    // Statistics
    'total_products' => 'মোট প্রডাক্ট',
    'products_with_stock' => 'নন-জিরো স্টক সহ পণ্য',
    'products_without_stock' => 'জিরো স্টক সহ পণ্য',
    'total_stock_value' => 'মোট স্টক মূল্য',
    'last_reset_date' => 'শেষ রিসেট তারিখ',
    'never' => 'কখনো না',

    // Quick Actions
    'products_with_stock_warning' => 'আপনার :count টি পণ্য আছে যার নন-জিরো স্টক (পজিটিভ বা নেগেটিভ) রয়েছে যা রিসেট করা যেতে পারে।',
    'all_stock_zero_info' => 'সমস্ত পণ্যের বর্তমানে জিরো স্টক রয়েছে। আপনি এখনও একটি অডিট ট্রেইল রেকর্ড তৈরি করতে রিসেট করতে পারেন।',

    // Reset History
    'recent_reset_history' => 'সাম্প্রতিক রিসেট ইতিহাস',
    'no_reset_history' => 'কোন রিসেট ইতিহাস নেই',
    'no_reset_history_desc' => 'আপনি এখনও কোন ইনভেন্টরি রিসেট করেননি।',
    'reason' => 'কারণ',
    'items_reset' => 'রিসেট করা আইটেম',
    'filter_by_user' => 'ব্যবহারকারী দ্বারা ফিল্টার করুন',
    'filter_by_status' => 'স্ট্যাটাস দ্বারা ফিল্টার করুন',
    'filter_by_type' => 'টাইপ দ্বারা ফিল্টার করুন',
	'filter_by_reset_mode' => 'রিসেট মোড দ্বারা ফিল্টার করুন',
	'filter_by_operation_type' => 'অপারেশনের ধরণ দ্বারা ফিল্টার করুন',
    'clear_filters' => 'ফিল্টার পরিষ্কার করুন',

    // Reset Form
    'inventory_reset_form' => 'ইনভেন্টরি রিসেট ফর্ম',
    'reset_type' => 'রিসেট টাইপ',
    'reset_mode' => 'রিসেট মোড',

    // Form Fields
    'select_location' => 'অবস্থান নির্বাচন করুন',
    'select_products' => 'প্রডাক্ট নির্বাচন করুন',
    'selected_products' => 'নির্বাচিত প্রডাক্ট',
    'sku' => 'SKU',
    'reason_placeholder' => 'এই ইনভেন্টরি রিসেটের জন্য একটি কারণ প্রদান করুন (যেমন, বার্ষিক ইনভেন্টরি, স্টক অডিট, সিস্টেম মাইগ্রেশন)...',
    'reason_help' => 'এই কারণটি লগ করা হবে এবং অডিট উদ্দেশ্যে রিপোর্টে দৃশ্যমান হবে।',
    'confirm_reset_checkbox' => 'আমি বুঝতে পারছি যে এই ক্রিয়াটি স্থায়ীভাবে সমস্ত নির্বাচিত ইনভেন্টরি শূন্যে রিসেট করবে এবং পূর্বাবস্থায় ফেরানো যাবে না।',

    // Stock Fix Options
    'fix_stock_mismatches' => 'রিসেটের আগে স্টক অমিল ঠিক করুন',
    'fix_stock_mismatches_desc' => 'রিসেট করার আগে স্বয়ংক্রিয়ভাবে স্টক বৈষম্য সংশোধন করুন',
    'fix_stock_mismatches_help' => 'এটি নিশ্চিত করবে যে রিসেট করার আগে সমস্ত গণনা করা স্টক প্রকৃত স্টকের সাথে মেলে',

    // Summary Panel
    'execute_reset' => 'রিসেট সম্পাদন করুন',

    // Help
    'help' => 'সাহায্য এবং টিপস',
    'help_tip_1' => 'রিসেট করার আগে আপনার ডেটা ব্যাকআপ নিশ্চিত করুন।',
    'help_tip_2' => 'রিসেট ট্র্যাক করতে স্টক অ্যাডজাস্টমেন্ট তৈরি করা হবে।',
    'help_tip_3' => 'আপনি সমস্ত রিসেট অপারেশনের বিস্তারিত রিপোর্ট দেখতে পারেন।',

    // Danger Zone
    'danger_zone_description' => 'ইনভেন্টরি রিসেট অপারেশন স্থায়ীভাবে নির্দিষ্ট অবস্থানে সমস্ত নির্বাচিত পণ্যের পরিমাণ শূন্যে সেট করবে।',
    'warning_irreversible' => 'এই ক্রিয়াটি অপরিবর্তনীয়!',

    // Processing
    'processing_status' => 'রিসেট প্রক্রিয়াকরণ...',
    'final_confirmation' => 'আপনি কি সত্যিই এই ইনভেন্টরি রিসেটের সাথে এগিয়ে যেতে চান? এই ক্রিয়াটি পূর্বাবস্থায় ফেরানো যাবে না।',

    // Messages
    'reset_completed_successfully' => 'ইনভেন্টরি রিসেট সফলভাবে সম্পন্ন!',
    'reset_failed' => 'ইনভেন্টরি রিসেট ব্যর্থ',
    'success' => 'সফল',
    'error' => 'ত্রুটি',

    // Reset Details
    'reset_details' => 'রিসেট বিবরণ',
    'reset_information' => 'রিসেট তথ্য',
    'reset_statistics' => 'রিসেট পরিসংখ্যান',
    'reset_id' => 'রিসেট আইডি',
    'performed_by' => 'দ্বারা সম্পাদিত',
    'started_at' => 'শুরুর সময়',
    'completed_at' => 'সমাপ্তির সময়',
    'location' => 'অবস্থান',
    'status' => 'স্ট্যাটাস',
    'error_message' => 'ত্রুটি বার্তা',
    'items_processed' => 'প্রক্রিয়াকৃত আইটেম',
    'total_quantity_reset' => 'মোট পরিমাণ',
    'unique_products' => 'অনন্য পণ্য',
    'duration' => 'সময়কাল',
    'back_to_dashboard' => 'ড্যাশবোর্ডে ফিরে যান',

    // Reset Items Table
    'reset_items_details' => 'রিসেট আইটেম বিবরণ',
    'product' => 'পণ্য',
    'quantity_before' => 'আগের পরিমাণ',
    'quantity_after' => 'পরের পরিমাণ',
    'quantity_reset' => 'রিসেট পরিমাণ',
    'variable_product' => 'পরিবর্তনশীল পণ্য',

    // Permissions
    'inventory_reset_module' => 'ইনভেন্টরি রিসেট মডিউল',
    'access_inventory_reset' => 'ইনভেন্টরি রিসেট মডিউল অ্যাক্সেস করুন',
    'view_inventory_reset_history' => 'ইনভেন্টরি রিসেট ইতিহাস দেখুন',
    'perform_inventory_reset' => 'ইনভেন্টরি রিসেট সম্পাদন করুন',
    'delete_inventory_reset_records' => 'ইনভেন্টরি রিসেট রেকর্ড মুছুন',
    'inventory_management' => 'ইনভেন্টরি ব্যবস্থাপনা',
    'perform_reset' => 'রিসেট সম্পাদন করুন',

    // Configuration
    'default_reset_reason' => 'ডিফল্ট রিসেট কারণ',
    'default_reset_reason_desc' => 'ডিফল্ট কারণ টেক্সট যা রিসেট ফর্মে পূর্বভর্তি হবে',
    'require_confirmation' => 'নিশ্চিতকরণ প্রয়োজন',
    'require_confirmation_desc' => 'রিসেট সম্পাদন করার আগে ব্যবহারকারীদের একটি নিশ্চিতকরণ বক্স চেক করতে হবে',
    'auto_create_adjustments' => 'স্বয়ংক্রিয়ভাবে স্টক অ্যাডজাস্টমেন্ট তৈরি করুন',
    'auto_create_adjustments_desc' => 'রিসেটের জন্য স্বয়ংক্রিয়ভাবে স্টক অ্যাডজাস্টমেন্ট লেনদেন তৈরি করুন',
    'max_products_per_reset' => 'প্রতি রিসেটে সর্বোচ্চ পণ্য',
    'max_products_per_reset_desc' => 'একটি একক অপারেশনে রিসেট করা যেতে পারে এমন পণ্যের সর্বোচ্চ সংখ্যা',

    // Installation
    'module_installed_successfully' => 'ইনভেন্টরি রিসেট মডিউল সফলভাবে ইনস্টল হয়েছে!',
    'module_updated_successfully' => 'ইনভেন্টরি রিসেট মডিউল সফলভাবে সংস্করণে আপডেট হয়েছে ',
    'module_uninstalled_successfully' => 'ইনভেন্টরি রিসেট মডিউল সফলভাবে আনইনস্টল হয়েছে!',

    // Status
    'completed' => 'সম্পন্ন',
    'processing' => 'প্রক্রিয়াকরণ',
    'failed' => 'ব্যর্থ',
    'pending' => 'পেন্ডিং',

    // Alert
    'info_alert' => 'আপনি :location এ :mode সহ :type এর জন্য :operation করতে যাচ্ছেন।',
    'confirm_alert' => 'এই ক্রিয়াটি আপনার ইনভেন্টরিতে সমস্ত মিলিত পণ্যকে প্রভাবিত করবে এবং পূর্বাবস্থায় ফেরানো যাবে না!',
    'final_confirmation_alert' => 'চূড়ান্ত নিশ্চিতকরণ প্রয়োজন',
    'type_reset_hint' => 'এই ধ্বংসাত্মক ক্রিয়া নিশ্চিত করতে RESET টাইপ করুন:',
    'reset_placeholder' => 'এখানে RESET টাইপ করুন...',
    'confirm_reset' => 'রিসেট নিশ্চিত করুন',
    'must_type_reset_error' => 'নিশ্চিত করতে আপনাকে RESET টাইপ করতে হবে!',
    'processing_plz_wait' => 'আমরা আপনার ইনভেন্টরি রিসেট করার সময় অনুগ্রহ করে অপেক্ষা করুন...',
    'reload_page' => 'পেজ পুনরায় লোড করুন',
    'select_atleast_product' => 'রিসেট করার জন্য অনুগ্রহ করে কমপক্ষে একটি পণ্য নির্বাচন করুন।',

    // Negative Inventory Display
    'products_with_negative_inventory' => 'নেগেটিভ ইনভেন্টরি সহ পণ্য',
    'loading_negative_products' => 'নেগেটিভ ইনভেন্টরি পণ্য লোড করা হচ্ছে...',
    'no_negative_inventory_found' => 'কোন নেগেটিভ ইনভেন্টরি পাওয়া যায়নি! সমস্ত পণ্যের অ-ঋণাত্মক পরিমাণ রয়েছে।',
    'error_loading_negative_products' => 'নেগেটিভ ইনভেন্টরি পণ্য লোড করতে ত্রুটি',
    'negative_quantity' => 'নেগেটিভ পরিমাণ',
    'locations' => 'অবস্থান',
    'products_with_negative_inventory_count' => 'নেগেটিভ ইনভেন্টরি সহ পণ্য পাওয়া গেছে',
    'select_specific_products_above' => 'অনুগ্রহ করে উপরে নির্দিষ্ট পণ্য নির্বাচন করুন, তারপর নেগেটিভ স্টক শুধুমাত্র নির্বাচিত পণ্যের জন্য দেখানো হবে।',
    'no_products_have_negative' => 'নির্বাচিত পণ্যগুলির কোনটিতে নেগেটিভ ইনভেন্টরি নেই।',

    // Reset Type Section
    'all_products' => 'সমস্ত পণ্য',
    'all_products_desc' => 'ব্যবসা/অবস্থানের সমস্ত পণ্যে অপারেশন প্রয়োগ করুন',
    'selected_products_type' => 'নির্বাচিত পণ্য',
    'selected_products_type_desc' => 'শুধুমাত্র নির্দিষ্টভাবে নির্বাচিত পণ্যগুলিতে অপারেশন প্রয়োগ করুন',

    // Reset Mode Section
    'all_stock_levels' => 'সমস্ত স্টক স্তর',
    'all_stock_levels_desc' => 'যেকোনো স্টক স্তর পরিবর্তন করুন (পজিটিভ, নেগেটিভ, বা শূন্য)',
    'positive_stock_only' => 'শুধুমাত্র পজিটিভ স্টক',
    'positive_stock_only_desc' => 'শুধুমাত্র পজিটিভ স্টক পরিমাণ সহ পণ্য পরিবর্তন করুন',
    'negative_stock_only' => 'শুধুমাত্র নেগেটিভ স্টক',
    'negative_stock_only_desc' => 'শুধুমাত্র নেগেটিভ স্টক পরিমাণ সহ পণ্য পরিবর্তন করুন',
    'zero_stock_only' => 'শুধুমাত্র শূন্য স্টক',
    'zero_stock_only_desc' => 'শুধুমাত্র ঠিক শূন্য স্টক সহ পণ্য পরিবর্তন করুন',

    // Operation Type Section
    'operation_type' => 'অপারেশন টাইপ',
    'reset_to_zero' => 'শূন্যে রিসেট করুন',
    'reset_to_zero_desc' => 'সমস্ত মিলিত ইনভেন্টরি পরিমাণ শূন্যে সেট করুন',
    'set_to_quantity' => 'কাস্টম পরিমাণে সেট করুন',
    'set_to_quantity_desc' => 'সমস্ত মিলিত ইনভেন্টরি একটি নির্দিষ্ট পরিমাণে সেট করুন',

    // Target Quantity Section
    'target_quantity' => 'লক্ষ্য পরিমাণ:',
    'target_quantity_placeholder' => 'সেট করার জন্য পরিমাণ প্রবেশ করুন',
    'target_quantity_desc' => 'সমস্ত মিলিত পণ্য এই পরিমাণে সেট করা হবে',
    'error_fetching_negative_products' => 'নেগেটিভ ইনভেন্টরি পণ্য আনতে ত্রুটি',

    // Display Labels
    'stock_adjustment' => 'Stock Adjustment',
    'inventory_reset_label' => 'Inventory Reset',
    'not_available' => 'N/A',
];