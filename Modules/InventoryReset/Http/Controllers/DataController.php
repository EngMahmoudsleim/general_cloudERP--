<?php

namespace Modules\InventoryReset\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Utils\ModuleUtil;
use Menu;

class DataController extends Controller
{
    /**
     * Defines module as a superadmin package.
     * @return Array
     */
    public function superadmin_package()
    {
        return [
            [
                'name' => 'inventory_reset_module',
                'label' => __('inventoryreset::lang.inventory_reset_module'),
                'default' => false,
            ]
        ];
    }

    /**
     * Defines user permissions for the module.
     * @return array
     */
    public function user_permissions()
    {
        return [
            // General Access
            [
                'value' => 'inventory_reset.access',
                'label' => __('inventoryreset::lang.access_inventory_reset'),
                'default' => false
            ],
            [
                'value' => 'inventory_reset.view',
                'label' => __('inventoryreset::lang.view_inventory_reset_history'),
                'default' => false
            ],

            // Reset Operations
            [
                'value' => 'inventory_reset.create',
                'label' => __('inventoryreset::lang.perform_inventory_reset'),
                'default' => false
            ],

            // Management
            [
                'value' => 'inventory_reset.delete',
                'label' => __('inventoryreset::lang.delete_inventory_reset_records'),
                'default' => false
            ],
        ];
    }

    /**
     * Modify admin menu to add InventoryReset menu items
     */
    public function modifyAdminMenu()
    {
        $business_id = session()->get('user.business_id');
        $module_util = new ModuleUtil();

        $is_inventory_reset_enabled = (bool)$module_util->hasThePermissionInSubscription($business_id, 'inventory_reset_module', 'superadmin_package');

        if ($is_inventory_reset_enabled && auth()->user()->can('inventory_reset.access')) {
            $menuparent = Menu::instance('admin-sidebar-menu');

            // Add as single menu item under Inventory/Stock management
            $menuparent->url(
                action([\Modules\InventoryReset\Http\Controllers\InventoryResetController::class, 'index']),
                __('inventoryreset::lang.inventory_reset'),
                [
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="tw-size-5 tw-shrink-0" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4"/>
                        <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4"/>
                        <path d="M9 12l2 2l4 -4"/>
                    </svg>',
                    'active' => request()->segment(1) == 'inventory-reset'
                ]
            )->order(86); // Position after stock management items

            // Alternative dropdown approach if you want sub-items
            /*
            $menuparent->dropdown(
                __('inventoryreset::lang.inventory_management'),
                function ($sub) {
                    $sub->url(
                        action([\Modules\InventoryReset\Http\Controllers\InventoryResetController::class, 'index']),
                        '🔄 ' . __('inventoryreset::lang.inventory_reset'),
                        ['active' => request()->segment(1) == 'inventory-reset']
                    );

                    if (auth()->user()->can('inventory_reset.create')) {
                        $sub->url(
                            action([\Modules\InventoryReset\Http\Controllers\InventoryResetController::class, 'resetForm']),
                            '⚡ ' . __('inventoryreset::lang.perform_reset'),
                            ['active' => request()->segment(2) == 'reset-form']
                        );
                    }
                },
                [
                    'icon' => 'fa fa-refresh',
                    'style' => 'color: #dd4b39;'
                ]
            )->order(86);
            */
        }
    }

    /**
     * Returns details about the module
     */
    public function module_details()
    {
        return [
            'name' => __('inventoryreset::lang.module_name'),
            'version' => config('inventoryreset.module_version'),
            'description' => __('inventoryreset::lang.module_description'),
            'author' => 'UPOS Development Team',
            'website' => '',
            'category' => 'Inventory Management',
            'dependencies' => [],
            'features' => [
                'Complete inventory quantity reset to zero',
                'Selective product reset options',
                'Location-specific reset capabilities',
                'Comprehensive reset history tracking',
                'Detailed audit trail for all reset operations',
                'Business-specific data isolation',
                'Stock adjustment transaction creation',
                'Permission-based access control'
            ]
        ];
    }

    /**
     * Module installation requirements check
     */
    public function installation_requirements()
    {
        $requirements = [
            'php_version' => '8.0',
            'laravel_version' => '10.0',
            'mysql_version' => '5.7',
            'required_permissions' => [
                'user.create', // Admin permissions needed
            ],
            'required_tables' => [
                'products',
                'product_locations',
                'business_locations',
                'transactions',
                'transaction_sell_lines',
                'users'
            ]
        ];

        // Check if all requirements are met
        $status = [
            'php_version_ok' => version_compare(PHP_VERSION, $requirements['php_version'], '>='),
            'tables_exist' => $this->checkRequiredTables($requirements['required_tables']),
            'permissions_ok' => auth()->user() && auth()->user()->can('user.create'),
        ];

        return [
            'requirements' => $requirements,
            'status' => $status,
            'ready_to_install' => !in_array(false, $status)
        ];
    }

    /**
     * Check if required database tables exist
     */
    private function checkRequiredTables($tables)
    {
        try {
            foreach ($tables as $table) {
                if (!\Schema::hasTable($table)) {
                    return false;
                }
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get module configuration options
     */
    public function configuration_options()
    {
        return [
            'default_reset_reason' => [
                'type' => 'text',
                'label' => __('inventoryreset::lang.default_reset_reason'),
                'default' => 'Inventory reset operation',
                'description' => __('inventoryreset::lang.default_reset_reason_desc')
            ],
            'require_confirmation' => [
                'type' => 'boolean',
                'label' => __('inventoryreset::lang.require_confirmation'),
                'default' => true,
                'description' => __('inventoryreset::lang.require_confirmation_desc')
            ],
            'auto_create_adjustments' => [
                'type' => 'boolean',
                'label' => __('inventoryreset::lang.auto_create_adjustments'),
                'default' => true,
                'description' => __('inventoryreset::lang.auto_create_adjustments_desc')
            ],
            'max_products_per_reset' => [
                'type' => 'number',
                'label' => __('inventoryreset::lang.max_products_per_reset'),
                'default' => 1000,
                'description' => __('inventoryreset::lang.max_products_per_reset_desc')
            ]
        ];
    }

    /**
     * Get statistics for dashboard display
     */
    public function dashboard_stats($business_id)
    {
        try {
            $totalResets = \Modules\InventoryReset\Entities\InventoryResetLog::where('business_id', $business_id)
                ->where('status', 'completed')
                ->count();

            $lastResetDate = \Modules\InventoryReset\Entities\InventoryResetLog::where('business_id', $business_id)
                ->where('status', 'completed')
                ->max('completed_at');

            $totalItemsReset = \Modules\InventoryReset\Entities\InventoryResetLog::where('business_id', $business_id)
                ->where('status', 'completed')
                ->sum('items_reset');

            $recentResets = \Modules\InventoryReset\Entities\InventoryResetLog::where('business_id', $business_id)
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            return [
                'total_resets' => $totalResets,
                'last_reset_date' => $lastResetDate,
                'total_items_reset' => $totalItemsReset,
                'recent_resets' => $recentResets
            ];
        } catch (\Exception $e) {
            \Log::error('InventoryReset: Error fetching dashboard stats: ' . $e->getMessage());
            return [
                'total_resets' => 0,
                'last_reset_date' => null,
                'total_items_reset' => 0,
                'recent_resets' => collect()
            ];
        }
    }
}