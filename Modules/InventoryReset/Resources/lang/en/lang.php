<?php

return [
    // Module Information
    'module_name' => 'Inventory Reset',
    'module_description' => 'Reset all inventory quantities to zero with just a few clicks',

    // Dashboard
    'dashboard_subtitle' => 'Manage and track your inventory reset operations',
    'inventory_reset' => 'Inventory Reset',

    // Statistics
    'total_products' => 'Total Products',
    'products_with_stock' => 'Products with Non-Zero Stock',
    'products_without_stock' => 'Products with Zero Stock',
    'total_stock_value' => 'Total Stock Value',
    'last_reset_date' => 'Last Reset Date',
    'never' => 'Never',

    // Quick Actions
    'products_with_stock_warning' => 'You have :count products with non-zero stock (positive or negative) that can be reset.',
    'all_stock_zero_info' => 'All products currently have zero stock. You can still perform a reset to create an audit trail record.',

    // Reset History
    'recent_reset_history' => 'Recent Reset History',
    'no_reset_history' => 'No Reset History',
    'no_reset_history_desc' => 'You haven\'t performed any inventory resets yet.',
    'reason' => 'Reason',
    'items_reset' => 'Items Reset',
    'filter_by_user' => 'Filter by User',
    'filter_by_status' => 'Filter by Status',
    'filter_by_type' => 'Filter by Type',
    'filter_by_reset_mode' => 'Filter by Reset Mode',
    'filter_by_operation_type' => 'Filter by Operation Type',
    'clear_filters' => 'Clear Filters',

    // Reset Form
    'inventory_reset_form' => 'Inventory Reset Form',
    'reset_type' => 'Reset Type',
    'reset_mode' => 'Reset Mode',

    // Form Fields
    'select_location' => 'Select Location',
    'select_products' => 'Select Products',
    'selected_products' => 'Selected Products',
    'sku' => 'SKU',
    'reason_placeholder' => 'Provide a reason for this inventory reset (e.g., Annual inventory, Stock audit, System migration)...',
    'reason_help' => 'This reason will be logged and visible in reports for audit purposes.',
    'confirm_reset_checkbox' => 'I understand that this action will permanently reset all selected inventory to zero and cannot be undone.',

    // Stock Fix Options
    'fix_stock_mismatches' => 'Fix Stock Mismatches Before Reset',
    'fix_stock_mismatches_desc' => 'Automatically correct stock discrepancies before performing the reset',
    'fix_stock_mismatches_help' => 'This will ensure all calculated stock matches actual stock before resetting',

    // Summary Panel
    'execute_reset' => 'Execute Reset',

    // Help
    'help' => 'Help & Tips',
    'help_tip_1' => 'Make sure to backup your data before performing a reset.',
    'help_tip_2' => 'Stock adjustments will be created to track the reset.',
    'help_tip_3' => 'You can view detailed reports of all reset operations.',

    // Danger Zone
    'danger_zone_description' => 'The inventory reset operation will permanently set all selected product quantities to zero across the specified locations.',
    'warning_irreversible' => 'This action is irreversible!',

    // Processing
    'processing_status' => 'Processing Reset...',
    'final_confirmation' => 'Are you absolutely sure you want to proceed with this inventory reset? This action cannot be undone.',

    // Messages
    'reset_completed_successfully' => 'Inventory reset completed successfully!',
    'reset_failed' => 'Inventory reset failed',
    'success' => 'Success',
    'error' => 'Error',

    // Reset Details
    'reset_details' => 'Reset Details',
    'reset_information' => 'Reset Information',
    'reset_statistics' => 'Reset Statistics',
    'reset_id' => 'Reset ID',
    'performed_by' => 'Performed By',
    'started_at' => 'Started At',
    'completed_at' => 'Completed At',
    'location' => 'Location',
    'status' => 'Status',
    'error_message' => 'Error Message',
    'items_processed' => 'Items Processed',
    'total_quantity_reset' => 'Total Quantity',
    'unique_products' => 'Unique Products',
    'duration' => 'Duration',
    'back_to_dashboard' => 'Back to Dashboard',

    // Reset Items Table
    'reset_items_details' => 'Reset Items Details',
    'product' => 'Product',
    'quantity_before' => 'Quantity Before',
    'quantity_after' => 'Quantity After',
    'quantity_reset' => 'Quantity Reset',
    'variable_product' => 'Variable Product',

    // Permissions
    'inventory_reset_module' => 'Inventory Reset Module',
    'access_inventory_reset' => 'Access Inventory Reset Module',
    'view_inventory_reset_history' => 'View Inventory Reset History',
    'perform_inventory_reset' => 'Perform Inventory Reset',
    'delete_inventory_reset_records' => 'Delete Inventory Reset Records',
    'inventory_management' => 'Inventory Management',
    'perform_reset' => 'Perform Reset',

    // Configuration
    'default_reset_reason' => 'Default Reset Reason',
    'default_reset_reason_desc' => 'Default reason text that will be prefilled in reset forms',
    'require_confirmation' => 'Require Confirmation',
    'require_confirmation_desc' => 'Require users to check a confirmation box before executing resets',
    'auto_create_adjustments' => 'Auto Create Stock Adjustments',
    'auto_create_adjustments_desc' => 'Automatically create stock adjustment transactions for resets',
    'max_products_per_reset' => 'Maximum Products per Reset',
    'max_products_per_reset_desc' => 'Maximum number of products that can be reset in a single operation',

    // Installation
    'module_installed_successfully' => 'Inventory Reset module installed successfully!',
    'module_updated_successfully' => 'Inventory Reset module updated successfully to version ',
    'module_uninstalled_successfully' => 'Inventory Reset module uninstalled successfully!',

    // Status
    'completed' => 'Completed',
    'processing' => 'Processing',
    'failed' => 'Failed',
    'pending' => 'Pending',

    // Alert
    'info_alert' => 'You are about to :operation for :type with :mode at :location.',
    'confirm_alert' => 'This action will affect ALL matching products in your inventory and cannot be undone!',
    'final_confirmation_alert' => 'Final Confirmation Required',
    'type_reset_hint' => 'Type RESET to confirm this destructive action:',
    'reset_placeholder' => 'Type RESET here...',
    'confirm_reset' => 'Confirm Reset',
    'must_type_reset_error' => 'You must type RESET to confirm!',
    'processing_plz_wait' => 'Please wait while we reset your inventory...',
    'reload_page' => 'Reload Page',
    'select_atleast_product' => 'Please select at least one product to reset.',

    // Negative Inventory Display
    'products_with_negative_inventory' => 'Products with Negative Inventory',
    'loading_negative_products' => 'Loading negative inventory products...',
    'no_negative_inventory_found' => 'No negative inventory found! All products have non-negative quantities.',
    'error_loading_negative_products' => 'Error loading negative inventory products',
    'negative_quantity' => 'Negative Quantity',
    'locations' => 'Locations',
    'products_with_negative_inventory_count' => 'products found with negative inventory',
    'select_specific_products_above' => 'Please select specific products above, then negative stock will be shown for selected products only.',
    'no_products_have_negative' => 'None of the selected products have negative inventory.',

    // Reset Type Section
    'all_products' => 'All Products',
    'all_products_desc' => 'Apply operation to all products in the business/location',
    'selected_products_type' => 'Selected Products',
    'selected_products_type_desc' => 'Apply operation only to specifically chosen products',

    // Reset Mode Section
    'all_stock_levels' => 'All Stock Levels',
    'all_stock_levels_desc' => 'Modify any stock level (positive, negative, or zero)',
    'positive_stock_only' => 'Positive Stock Only',
    'positive_stock_only_desc' => 'Only modify products with positive stock quantities',
    'negative_stock_only' => 'Negative Stock Only',
    'negative_stock_only_desc' => 'Only modify products with negative stock quantities',
    'zero_stock_only' => 'Zero Stock Only',
    'zero_stock_only_desc' => 'Only modify products with exactly zero stock',

    // Operation Type Section
    'operation_type' => 'Operation Type',
    'reset_to_zero' => 'Reset to Zero',
    'reset_to_zero_desc' => 'Set all matching inventory quantities to zero',
    'set_to_quantity' => 'Set to Custom Quantity',
    'set_to_quantity_desc' => 'Set all matching inventory to a specific quantity',
    'add_quantity' => 'Add Quantity',
    'add_quantity_desc' => 'Add a specific quantity to current inventory levels',

    // Target Quantity Section
    'target_quantity' => 'Target Quantity:',
    'target_quantity_placeholder' => 'Enter quantity to set',
    'target_quantity_desc' => 'All matching products will be set to this quantity',
    'add_quantity_placeholder' => 'Enter quantity to add',
    'add_quantity_desc' => 'This quantity will be added to each matching product\'s current stock',
    'error_fetching_negative_products' => 'Error fetching negative inventory products',

    // Display Labels
    'stock_adjustment' => 'Stock Adjustment',
    'inventory_reset_label' => 'Inventory Reset',
    'not_available' => 'N/A',
];