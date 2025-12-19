<?php

return [
    'name' => 'InventoryReset',
    'module_version' => '2.0.9',
    'description' => 'Inventory Reset Management Module for UltimatePOS',

    // Module settings
    'settings' => [
        'max_items_per_reset' => 10000,
        'require_reason' => true,
        'log_retention_days' => 365,
        'auto_create_adjustments' => true,
        'require_confirmation' => true,
    ],

    // Security settings
    'permissions' => [
        'access' => 'inventory_reset.access',
        'view' => 'inventory_reset.view',
        'create' => 'inventory_reset.create',
        'delete' => 'inventory_reset.delete',
    ],

    // Reset configuration
    'reset_types' => [
        'all' => 'Reset All Products',
        'selected' => 'Reset Selected Products',
        'location' => 'Reset by Location'
    ],

    // Database settings
    'tables' => [
        'reset_logs' => 'inventory_reset_logs',
        'reset_items' => 'inventory_reset_items',
    ],
];