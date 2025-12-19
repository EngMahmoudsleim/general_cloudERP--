<?php

namespace Modules\Restaurant\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Menu;

class DataController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */



    public function superadmin_package()
    {
        return [
            [
                'name' => 'restaurant_module',
                'label' => __('Restaurant::lang.restaurant'),
                'default' => false
            ]
        ];
    }

    /* Module menu*/
    public function modifyAdminMenu0000()
    {

            $enabled_modules = !empty(session('business.enabled_modules')) ? session('business.enabled_modules') : [];

        $background_color = '#fff !important';
        $menu = Menu::instance('admin-sidebar-menu');
        if (auth()->user()->can('restaurant')) {
            $menu->dropdown(
                __('restaurant::lang.restaurant'),
                function ($sub) {
                    $sub->url(
                        action('\Modules\Restaurant\Http\Controllers\RestaurantController@index'),
                        __('restaurant::lang.submen1'),
                        ['icon' => 'fa fas fa-users-cog', 'active' => request()->segment(1) == 'restaurant' ]
                    );
                    $sub->url(
                        action('\Modules\Restaurant\Http\Controllers\RestaurantController@index'),
                        __('restaurant::lang.submen2'),
                        ['icon' => 'fa fas fa-users-cog', 'active' => request()->segment(1) == 'restaurant' ]
                    );

                    $sub->url(
                        action('\Modules\Restaurant\Http\Controllers\RestaurantController@index'),
                        __('restaurant::lang.submen3'),
                        ['icon' => 'fa fas fa-users-cog', 'active' => request()->segment(1) == 'restaurant' ]
                    );
                    /////
                    
            
                    
                    
                    

                },
                ['icon' => 'fa fas fa-users-cog', 'style' => 'background-color: #fdfdfd !important;']

            )->order(30);

        }
          /*Menu::modify('admin-sidebar-menu', function ($menu) use ($background_color) {
               $menu->url(
                action('\Modules\Restaurant\Http\Controllers\RestaurantController@index'),
                   __('restaurant::lang.restaurant'),
                   ['icon' => 'fa fas fa-user', 'active' => request()->segment(1) == 'restaurant', 'style' => 'background-color:' . $background_color]
            )
->order(24);
            });*/


    }
/* Module menu */
public function modifyAdminMenu()
{
    $background_color = '#fff !important';

    // نفس المتغير المستخدم في AdminSidebarMenu
    $enabled_modules = !empty(session('business.enabled_modules')) ? session('business.enabled_modules') : [];

    $menu = Menu::instance('admin-sidebar-menu');

    // صلاحية المطاعم
    if (auth()->user()->can('restaurant')) {

        $menu->dropdown(
            __('restaurant::lang.restaurant'),
            function ($sub) use ($enabled_modules) {

                // =========================
                // 1) قائمة الحجوزات (Booking)
                // =========================
                if (
                    in_array('booking', $enabled_modules)
                    && (auth()->user()->can('crud_all_bookings') || auth()->user()->can('crud_own_bookings'))
                ) {
                    $sub->url(
                        action([\App\Http\Controllers\Restaurant\BookingController::class, 'index']),
                        __('restaurant.bookings'),
                        [
                            'icon'   => 'fa fas fa-calendar-check',
                            'active' => request()->segment(1) == 'bookings',
                        ]
                    )->order(10);
                }

                // ===================
                // 2) قائمة المطبخ (Kitchen)
                // ===================
                if (in_array('kitchen', $enabled_modules)) {
                    $sub->url(
                        action([\App\Http\Controllers\Restaurant\KitchenController::class, 'index']),
                        __('restaurant.kitchen'),
                        [
                            'icon'   => 'fa fas fa-utensils',
                            'active' => request()->segment(1) == 'modules'
                                && request()->segment(2) == 'kitchen',
                        ]
                    )->order(20);
                }

                // ==========================
                // 3) طاقم الخدمة (Service Staff)
                // ==========================
                if (in_array('service_staff', $enabled_modules)) {
                    $sub->url(
                        action([\App\Http\Controllers\Restaurant\OrderController::class, 'index']),
                        __('restaurant.service_staff'),
                        [
                            'icon'   => 'fa fas fa-user-friends',
                            'active' => request()->segment(1) == 'modules'
                                && request()->segment(2) == 'orders',
                        ]
                    )->order(30);
                }

                // =======================
                // 4) الطاولات (Tables)
                // =======================
                if (
                    in_array('tables', $enabled_modules)
                    && auth()->user()->can('access_tables')
                ) {
                    $sub->url(
                        action([\App\Http\Controllers\Restaurant\TableController::class, 'index']),
                        __('restaurant.tables'),
                        [
                            'icon'   => '',
                            'active' => request()->segment(1) == 'modules'
                                && request()->segment(2) == 'tables',
                        ]
                    )->order(40);
                }

                // =========================
                // 5) الموديفاير (Modifier Sets)
                // =========================
                if (
                    in_array('modifiers', $enabled_modules)
                    && (auth()->user()->can('product.view') || auth()->user()->can('product.create'))
                ) {
                    $sub->url(
                        action([\App\Http\Controllers\Restaurant\ModifierSetsController::class, 'index']),
                        __('restaurant.modifiers'),
                        [
                            'icon'   => '',
                            'active' => request()->segment(1) == 'modules'
                                && request()->segment(2) == 'modifiers',
                        ]
                    )->order(50);
                }

            },
            [
                'icon'  => 'fa fas fa-users-cog',
                'style' => 'background-color: #fdfdfd !important;',
            ]
        )->order(30);
    }
}

    public function user_permissions()
    {
        return [
            [
                'value' => 'restaurant.create',
                'label' =>  __('restaurant::lang.creat'),
                'default' => false
            ],
            [
                'value' => 'restaurant.edit',
                'label' => __('restaurant::lang.edit'),
                'default' => false
            ],
            [
                'value' => 'restaurant.delete',
                'label' =>  __('restaurant::lang.delete'),
                'default' => false
            ],

            [
                'value' => 'restaurant.update',
                'label' => __('restaurant::lang.update'),
                'default' => false
            ],
        ];
    }
    public function index()
    {
        return view('Restaurant::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('restaurant::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        return view('restaurant::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        return view('restaurant::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
