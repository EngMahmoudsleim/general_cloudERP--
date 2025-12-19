<?php

namespace Modules\Labels\Http\Controllers;

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
                'name' => 'Labels',
                'label' => __('Labels::Labels.Labels_module'),
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
              [
                  'value' => 'Labels.view',
                  'label' => __('Labels::app.view'),
                  'default' => false
              ],
          ];
      }

    /**
     * Adds Catalogue QR menus
     * @return null
     */
    //  public function modifyAdminMenu()
    // {
    //     $business_id = session()->get('user.business_id');
    //     $module_util = new ModuleUtil();
    //     $is_Labels_enabled = (boolean)$module_util->hasThePermissionInSubscription($business_id, 'Labels_module', 'superadmin_package');

    //     if ($is_Labels_enabled && auth()->user()->can('superadmin') || auth()->user()->can('Labels.view')) {

    //         Menu::modify('admin-sidebar-menu', function ($menu) {
    //             $menu->url(
    //                     action([\Modules\Labels\Http\Controllers\LabelsController::class, 'index']),
    //                     __('labels::lang.title_Labels'),
    //                     //['icon' => 'fa fas fa-podcast', 'active' => request()->segment(1) == 'Labels', 'style' => config('app.env') == 'demo' ? 'background-color: #ff851b;' : '']
    //                     ['icon' => 'fa fas fa-podcast','active' => request()->segment(1) == 'Labels', 'style' => 'background-color: #D6DBDF!important;']
    //                 )
    //             ->order(98);
    //         });
    //     }
    // }

    public function modifyAdminMenu()
    {
        $business_id = session()->get('user.business_id');
        $module_util = new ModuleUtil();
        $background_color = '#E74C3C';
        $is_Clients_enabled = (boolean)$module_util->hasThePermissionInSubscription($business_id, 'Clients_module', 'superadmin_package');

        if ($is_Clients_enabled && (auth()->user()->can('superadmin') || auth()->user()->can('product.view')) ) {

            $menuparent = Menu::instance('admin-sidebar-menu');
            $menuparent->dropdown (__('labels::lang.title_Labels'), 

                function ($sub) use ($background_color){    
                    if (auth()->user()->can('product.view')) {
                        // Old version
                        // $sub->url(
                        //     action([\App\Http\Controllers\LabelsController::class, 'show']),
                        //     __('barcode.print_labels') . ' (Old)',
                        //     ['icon' => '', 'active' => request()->segment(1) == 'labels' && request()->segment(2) == 'show']
                        // );

                        // Add enhanced version
                        $sub->url(
                            action([\Modules\Labels\Http\Controllers\LabelsController::class, 'enhancedShow']),
                            __('labels::lang.print_labels') . ' (Enhanced)',
                            ['icon' => '', 'active' => request()->segment(1) == 'labels' && request()->segment(2) == 'enhanced-show']
                        );
                    } 
        
                    if (auth()->user()->can('barcode_settings.access')) {
                        // BarCode Settings
                        $sub->url(
                            action([\App\Http\Controllers\BarcodeController::class, 'index']),
                            __('barcode.barcode_settings'),
                            ['icon' => '', 'active' => request()->segment(1) == 'barcodes']
                        );
                    }
                },
                ['icon' => 'fa fas fa-laptop','active' => request()->segment(1) == 'Labels ']
            )->order(98);
  
        }
    }    

}
