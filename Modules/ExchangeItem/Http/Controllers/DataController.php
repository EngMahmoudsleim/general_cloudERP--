<?php

namespace Modules\ExchangeItem\Http\Controllers;

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
                'name' => 'ExchangeItem',
                'label' => __('exchangeitem::lang.exchange'),
                'default' => false
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
                'value' => 'exchange.access',
                'label' => __('exchangeitem::lang.access_exchange_module'),
                'default' => false
            ],
            [
                'value' => 'exchange.view',
                'label' => __('exchangeitem::lang.view_exchanges'),
                'default' => false
            ],
            
            // Exchange Operations
            [
                'value' => 'exchange.create',
                'label' => __('exchangeitem::lang.create_exchange'),
                'default' => false
            ],
            [
                'value' => 'exchange.cancel',
                'label' => __('exchangeitem::lang.cancel_exchange'),
                'default' => false
            ],
            [
                'value' => 'exchange.delete',
                'label' => __('exchangeitem::lang.delete_exchange'),
                'default' => false
            ],
            
            // Reporting & Export
            [
                'value' => 'exchange.export',
                'label' => __('exchangeitem::lang.export_exchange_data'),
                'default' => false
            ],
            [
                'value' => 'exchange.print',
                'label' => __('exchangeitem::lang.print_exchange_receipt'),
                'default' => false
            ],
        ];
    }



    public function modifyAdminMenu()
    {
        $business_id = session()->get('user.business_id');
        $module_util = new ModuleUtil();
        
        // Fixed: Changed module name to match superadmin_package definition
        $is_exchange_enabled = (bool)$module_util->hasThePermissionInSubscription($business_id, 'ExchangeItem', 'superadmin_package');

        // Fixed: Simplified permission check - removed 'superadmin' check that was blocking regular users
        if ($is_exchange_enabled && auth()->user()->can('exchange.access')) 
        {
            // Define background color for the Exchange menu item
           // $background_color = 'rgb(104, 211, 145)'; // Green color for Exchange
            
            Menu::modify(
                'admin-sidebar-menu',
                function ($menu) {
                    $menu->url(
                        action([\Modules\ExchangeItem\Http\Controllers\ExchangeController::class, 'index']),
                        __('exchangeitem::lang.exchange'),
                        [
                            'icon' => 'fa fas fa-exchange-alt',
                            
                            'active' => request()->segment(1) == 'exchangeitem'
                        ]
                    )->order(31);
                }
            );
        }
    }
}
