<?php

return [
    // Module Information
    'module_name' => 'इन्वेंटरी रीसेट',
    'module_description' => 'सभी इन्वेंटरी मात्राओं को कुछ ही क्लिक में शून्य पर रीसेट करें',

    // Dashboard
    'dashboard_subtitle' => 'अपने इन्वेंटरी रीसेट ऑपरेशन का प्रबंधन और ट्रैकिंग करें',
    'inventory_reset' => 'इन्वेंटरी रीसेट',

    // Statistics
    'total_products' => 'कुल उत्पाद',
    'products_with_stock' => 'गैर-शून्य स्टॉक वाले उत्पाद',
    'products_without_stock' => 'शून्य स्टॉक वाले उत्पाद',
    'total_stock_value' => 'कुल स्टॉक मूल्य',
    'last_reset_date' => 'अंतिम रीसेट दिनांक',
    'never' => 'कभी नहीं',

    // Quick Actions
    'products_with_stock_warning' => 'आपके पास :count उत्पाद हैं जिनमें गैर-शून्य स्टॉक (सकारात्मक या नकारात्मक) है जिसे रीसेट किया जा सकता है।',
    'all_stock_zero_info' => 'सभी उत्पादों में वर्तमान में शून्य स्टॉक है। आप अभी भी ऑडिट ट्रेल रिकॉर्ड बनाने के लिए रीसेट कर सकते हैं।',

    // Reset History
    'recent_reset_history' => 'हाल का रीसेट इतिहास',
    'no_reset_history' => 'कोई रीसेट इतिहास नहीं',
    'no_reset_history_desc' => 'आपने अभी तक कोई इन्वेंटरी रीसेट नहीं किया है।',
    'reason' => 'कारण',
    'items_reset' => 'रीसेट किए गए आइटम',
    'filter_by_user' => 'उपयोगकर्ता द्वारा फिल्टर करें',
    'filter_by_status' => 'स्थिति द्वारा फिल्टर करें',
    'filter_by_type' => 'प्रकार द्वारा फिल्टर करें',
	'filter_by_reset_mode' => 'रीसेट मोड द्वारा फ़िल्टर करें',
	'filter_by_operation_type' => 'ऑपरेशन प्रकार द्वारा फ़िल्टर करें',
    'clear_filters' => 'फिल्टर साफ़ करें',

    // Reset Form
    'inventory_reset_form' => 'इन्वेंटरी रीसेट फॉर्म',
    'reset_type' => 'रीसेट प्रकार',
    'reset_mode' => 'रीसेट मोड',

    // Form Fields
    'select_location' => 'स्थान चुनें',
    'select_products' => 'उत्पाद चुनें',
    'selected_products' => 'चयनित उत्पाद',
    'sku' => 'SKU',
    'reason_placeholder' => 'इस इन्वेंटरी रीसेट का कारण प्रदान करें (जैसे, वार्षिक इन्वेंटरी, स्टॉक ऑडिट, सिस्टम माइग्रेशन)...',
    'reason_help' => 'यह कारण लॉग किया जाएगा और ऑडिट उद्देश्यों के लिए रिपोर्ट में दिखाई देगा।',
    'confirm_reset_checkbox' => 'मैं समझता हूं कि यह कार्य सभी चयनित इन्वेंटरी को स्थायी रूप से शून्य पर रीसेट कर देगा और इसे पूर्ववत नहीं किया जा सकता।',

    // Stock Fix Options
    'fix_stock_mismatches' => 'रीसेट से पहले स्टॉक बेमेल ठीक करें',
    'fix_stock_mismatches_desc' => 'रीसेट करने से पहले स्टॉक विसंगतियों को स्वचालित रूप से सुधारें',
    'fix_stock_mismatches_help' => 'यह सुनिश्चित करेगा कि रीसेट करने से पहले सभी गणना किए गए स्टॉक वास्तविक स्टॉक से मेल खाते हैं',

    // Summary Panel
    'execute_reset' => 'रीसेट निष्पादित करें',

    // Help
    'help' => 'सहायता और सुझाव',
    'help_tip_1' => 'रीसेट करने से पहले अपने डेटा का बैकअप लेना सुनिश्चित करें।',
    'help_tip_2' => 'रीसेट को ट्रैक करने के लिए स्टॉक समायोजन बनाए जाएंगे।',
    'help_tip_3' => 'आप सभी रीसेट ऑपरेशन की विस्तृत रिपोर्ट देख सकते हैं।',

    // Danger Zone
    'danger_zone_description' => 'इन्वेंटरी रीसेट ऑपरेशन निर्दिष्ट स्थानों पर सभी चयनित उत्पाद मात्राओं को स्थायी रूप से शून्य पर सेट कर देगा।',
    'warning_irreversible' => 'यह कार्य अपरिवर्तनीय है!',

    // Processing
    'processing_status' => 'रीसेट प्रसंस्करण...',
    'final_confirmation' => 'क्या आप वास्तव में इस इन्वेंटरी रीसेट के साथ आगे बढ़ना चाहते हैं? यह कार्य पूर्ववत नहीं किया जा सकता।',

    // Messages
    'reset_completed_successfully' => 'इन्वेंटरी रीसेट सफलतापूर्वक पूर्ण!',
    'reset_failed' => 'इन्वेंटरी रीसेट विफल',
    'success' => 'सफलता',
    'error' => 'त्रुटि',

    // Reset Details
    'reset_details' => 'रीसेट विवरण',
    'reset_information' => 'रीसेट जानकारी',
    'reset_statistics' => 'रीसेट आंकड़े',
    'reset_id' => 'रीसेट ID',
    'performed_by' => 'द्वारा किया गया',
    'started_at' => 'प्रारंभ समय',
    'completed_at' => 'पूर्ण समय',
    'location' => 'स्थान',
    'status' => 'स्थिति',
    'error_message' => 'त्रुटि संदेश',
    'items_processed' => 'प्रसंस्कृत आइटम',
    'total_quantity_reset' => 'कुल मात्रा',
    'unique_products' => 'अनूठे उत्पाद',
    'duration' => 'अवधि',
    'back_to_dashboard' => 'डैशबोर्ड पर वापस',

    // Reset Items Table
    'reset_items_details' => 'रीसेट आइटम विवरण',
    'product' => 'उत्पाद',
    'quantity_before' => 'पहले मात्रा',
    'quantity_after' => 'बाद मात्रा',
    'quantity_reset' => 'रीसेट मात्रा',
    'variable_product' => 'परिवर्तनीय उत्पाद',

    // Permissions
    'inventory_reset_module' => 'इन्वेंटरी रीसेट मॉड्यूल',
    'access_inventory_reset' => 'इन्वेंटरी रीसेट मॉड्यूल एक्सेस करें',
    'view_inventory_reset_history' => 'इन्वेंटरी रीसेट इतिहास देखें',
    'perform_inventory_reset' => 'इन्वेंटरी रीसेट करें',
    'delete_inventory_reset_records' => 'इन्वेंटरी रीसेट रिकॉर्ड हटाएं',
    'inventory_management' => 'इन्वेंटरी प्रबंधन',
    'perform_reset' => 'रीसेट करें',

    // Configuration
    'default_reset_reason' => 'डिफ़ॉल्ट रीसेट कारण',
    'default_reset_reason_desc' => 'डिफ़ॉल्ट कारण टेक्स्ट जो रीसेट फॉर्म में पहले से भरा होगा',
    'require_confirmation' => 'पुष्टि आवश्यक',
    'require_confirmation_desc' => 'रीसेट निष्पादित करने से पहले उपयोगकर्ताओं को पुष्टि बॉक्स चेक करना आवश्यक है',
    'auto_create_adjustments' => 'स्टॉक समायोजन स्वचालित रूप से बनाएं',
    'auto_create_adjustments_desc' => 'रीसेट के लिए स्वचालित रूप से स्टॉक समायोजन लेनदेन बनाएं',
    'max_products_per_reset' => 'प्रति रीसेट अधिकतम उत्पाद',
    'max_products_per_reset_desc' => 'एक ऑपरेशन में रीसेट किए जा सकने वाले उत्पादों की अधिकतम संख्या',

    // Installation
    'module_installed_successfully' => 'इन्वेंटरी रीसेट मॉड्यूल सफलतापूर्वक इंस्टॉल!',
    'module_updated_successfully' => 'इन्वेंटरी रीसेट मॉड्यूल सफलतापूर्वक वर्जन में अपडेट ',
    'module_uninstalled_successfully' => 'इन्वेंटरी रीसेट मॉड्यूल सफलतापूर्वक अनइंस्टॉल!',

    // Status
    'completed' => 'पूर्ण',
    'processing' => 'प्रसंस्करण',
    'failed' => 'विफल',
    'pending' => 'लंबित',

    // Alert
    'info_alert' => 'आप :location पर :mode के साथ :type के लिए :operation करने वाले हैं।',
    'confirm_alert' => 'यह कार्य आपकी इन्वेंटरी में सभी मिलान करने वाले उत्पादों को प्रभावित करेगा और इसे पूर्ववत नहीं किया जा सकता!',
    'final_confirmation_alert' => 'अंतिम पुष्टि आवश्यक',
    'type_reset_hint' => 'इस विनाशकारी कार्य की पुष्टि के लिए RESET टाइप करें:',
    'reset_placeholder' => 'यहां RESET टाइप करें...',
    'confirm_reset' => 'रीसेट की पुष्टि करें',
    'must_type_reset_error' => 'पुष्टि के लिए आपको RESET टाइप करना होगा!',
    'processing_plz_wait' => 'कृपया प्रतीक्षा करें जब तक हम आपकी इन्वेंटरी रीसेट कर रहे हैं...',
    'reload_page' => 'पेज रीलोड करें',
    'select_atleast_product' => 'कृपया रीसेट करने के लिए कम से कम एक उत्पाद चुनें।',

    // Negative Inventory Display
    'products_with_negative_inventory' => 'नकारात्मक इन्वेंटरी वाले उत्पाद',
    'loading_negative_products' => 'नकारात्मक इन्वेंटरी उत्पाद लोड हो रहे हैं...',
    'no_negative_inventory_found' => 'कोई नकारात्मक इन्वेंटरी नहीं मिली! सभी उत्पादों में गैर-नकारात्मक मात्राएं हैं।',
    'error_loading_negative_products' => 'नकारात्मक इन्वेंटरी उत्पाद लोड करने में त्रुटि',
    'negative_quantity' => 'नकारात्मक मात्रा',
    'locations' => 'स्थान',
    'products_with_negative_inventory_count' => 'नकारात्मक इन्वेंटरी के साथ उत्पाद मिले',
    'select_specific_products_above' => 'कृपया ऊपर विशिष्ट उत्पाद चुनें, फिर नकारात्मक स्टॉक केवल चयनित उत्पादों के लिए दिखाया जाएगा।',
    'no_products_have_negative' => 'चयनित उत्पादों में से किसी में भी नकारात्मक इन्वेंटरी नहीं है।',

    // Reset Type Section
    'all_products' => 'सभी उत्पाद',
    'all_products_desc' => 'व्यापार/स्थान में सभी उत्पादों पर संचालन लागू करें',
    'selected_products_type' => 'चयनित उत्पाद',
    'selected_products_type_desc' => 'केवल विशेष रूप से चुने गए उत्पादों पर संचालन लागू करें',

    // Reset Mode Section
    'all_stock_levels' => 'सभी स्टॉक स्तर',
    'all_stock_levels_desc' => 'किसी भी स्टॉक स्तर को संशोधित करें (सकारात्मक, नकारात्मक, या शून्य)',
    'positive_stock_only' => 'केवल सकारात्मक स्टॉक',
    'positive_stock_only_desc' => 'केवल सकारात्मक स्टॉक मात्रा वाले उत्पादों को संशोधित करें',
    'negative_stock_only' => 'केवल नकारात्मक स्टॉक',
    'negative_stock_only_desc' => 'केवल नकारात्मक स्टॉक मात्रा वाले उत्पादों को संशोधित करें',
    'zero_stock_only' => 'केवल शून्य स्टॉक',
    'zero_stock_only_desc' => 'केवल बिल्कुल शून्य स्टॉक वाले उत्पादों को संशोधित करें',

    // Operation Type Section
    'operation_type' => 'संचालन प्रकार',
    'reset_to_zero' => 'शून्य पर रीसेट करें',
    'reset_to_zero_desc' => 'सभी मिलान करने वाली इन्वेंट्री मात्रा को शून्य पर सेट करें',
    'set_to_quantity' => 'कस्टम मात्रा पर सेट करें',
    'set_to_quantity_desc' => 'सभी मिलान करने वाली इन्वेंट्री को एक विशिष्ट मात्रा पर सेट करें',

    // Target Quantity Section
    'target_quantity' => 'लक्ष्य मात्रा:',
    'target_quantity_placeholder' => 'सेट करने के लिए मात्रा दर्ज करें',
    'target_quantity_desc' => 'सभी मिलान करने वाले उत्पाद इस मात्रा पर सेट होंगे',
    'error_fetching_negative_products' => 'नकारात्मक इन्वेंटरी उत्पाद प्राप्त करने में त्रुटि',

    // Display Labels
    'stock_adjustment' => 'Stock Adjustment',
    'inventory_reset_label' => 'Inventory Reset',
    'not_available' => 'N/A',
];