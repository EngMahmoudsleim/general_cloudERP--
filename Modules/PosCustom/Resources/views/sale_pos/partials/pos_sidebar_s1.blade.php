<style>

.title_muted_cate{
   white-space: nowrap; 
   overflow: hidden; 
   text-overflow: ellipsis; 
   max-width: 120px;
   text-align: left;
}

/* Drawer Toggle Functionality */
    .drawer-toggle {
        display: none;
    }
    
    .drawer-content label {
        cursor: pointer;
    }
    
    /* Drawer Side */
    .drawer-side {
        position: fixed;
        top: 0;
        bottom: 0;
        right: -100%;
        width: 20%;
        max-width: 400px;
        background-color: #fff;
        box-shadow: -2px 0 5px rgba(0, 0, 0, 0.5);
        overflow-y: auto;
        transition: right 0.3s ease;
        border-radius: 10px;
        z-index: 1000;
    }
    
    .drawer-toggle:checked + .drawer-content + .drawer-side .drawer-overlay {
        display: block;
    }

    
    .drawer-toggle:checked + .drawer-content + .drawer-side {
        right: 0;
    }
    
    /* Drawer Menu */
    .drawer-menu {
        padding: 1rem;
        position: relative;
    }
    
    /* Category in the middle*/
    .cardpanel {
/*         border-radius: 15px !important;
 */        border: 3px solid #ffffff !important;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1) !important;
        transition: box-shadow 0.3s ease !important;
        /* font-size: 10px !important;
    	font-family: 'Times New Roman' !important; */
        /* background-color: #f0f0f0; */
    }

    .cardpanel:hover {
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        background-color: rgb(235, 234, 234);
    }
    
    .card:hover {
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        background-color: rgb(222, 220, 220);
    }
    
    .card-body {
        padding: 0.75rem; 

    }
    
    .card-actions {
        text-align: center;
        margin-top: 0.5rem;
    }
    
    .main-category-div,
    .product_subcategory,
    .product_brand_div,
    .main-category
     {
        cursor: pointer;
    }
    
    /* Keyframes for slide-in-from-right animation */
    @keyframes slideInFromRight {
        from {
            opacity: 0;
            transform: translateX(10px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    /* Subcategory Dropdown */
    .sub-category-dropdown {
        display: none;
        position: fixed;
        /*left: calc(61.5% - 5px);  Adjust based on drawer width */
        float: left;
        top: auto;
        left: 63%;
        right: auto;
        /* margin-top: -180px; */
        width: 50%;
        max-width: 300px;
        /* background: white; */
        border: 2px solid #ddd;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        overflow-y: auto;
        z-index: 1001; /* Higher than the main drawer */
        opacity: 0;
        transform: translateX(10px); /* Start off to the right */
    }
    
    /* Apply animation on hover */
    .drawer-menu .main-category-div:hover .sub-category-dropdown,
    .drawer-menu .main-category-div:focus-within .sub-category-dropdown {
        display: block;
        animation: slideInFromRight 0.6s ease forwards;
    }
    

    /* Responsive Layout */
    @media (min-width: 576px) {
        .drawer-menu .row > .col-md-3,
        .drawer-menu .row > .col-md-4 {
            display: inline-block;
            width: 100%;
        }
    }
    
    @media (max-width: 575px) {
        .drawer-menu .row > .col-md-3,
        .drawer-menu .row > .col-md-4 {
            display: inline-block;
            width: 100%;
        }
    }
    .close-side-bar {
        position: absolute;
        top: 10px; /* Adjust the top position as needed */
        right: 10px; /* Adjust the right position as needed */
        background: none; /* Remove background if needed */
        border: none; /* Remove border if needed */
        font-size: 24px; /* Adjust the size as needed */
        cursor: pointer;
        z-index: 1001; /* Ensure it is above other elements */
    }
    
    .close-side-bar i {
        color: #000; /* Set icon color */
    }
    
</style>
@php
    $is_mobile = isMobile(); 
    $custom_labels = json_decode($business_details['custom_labels']);
    $theme_divider_class = 'theme_' . $business_details->theme_color . '_divider';
    /* Old style 5.40 using css/app.css */
    $theme_pos_class1 = 'theme_' . $business_details->theme_color . '_pos';
    /* New style usin css/tailwind/app.css */
    $theme_color = $business_details->theme_color;
    if ($theme_color == 'primary') /* primary doesnt exist */
        $theme_color = 'blue';

    $theme_pos_class = 'tw-bg-gradient-to-r tw-from-' . $business_details->theme_color . '-800'
    .' tw-to-'.$theme_color.'-500';
    //var_dump($business_details->theme_color);
    if ($business_details->theme_color == 'sky')
        $border_theme_color = '#357ca5'; //skyblue
    elseif ($business_details->theme_color == 'primary') 
        $border_theme_color = 'blue'; //blue
    else 
        $border_theme_color = $business_details->theme_color;

    //#JCN Default to avoid erros
    //$ps_default_category = 1; by default if poscustom_default_category is null = all categories
    //115 is a calculate value for PosCustom 
    $ps_width_product = 115;
        $ps_default_category = null; 

    
    if (!empty($pos_settings['poscustom_default_category'])) 
        $ps_default_category = $pos_settings['poscustom_default_category'];

    if (!empty($pos_settings['poscustom_width_product']))                                                                                                   
        $ps_width_product = $pos_settings['poscustom_width_product'] ;

    //Testing the best way to draw the products    

    //Case quick access category on middle
    $winproduct_size = 'tw-h-dvh-cate-off' ; // Default 1 Show Category BAR In the CSS  

    //#JCN Beta still testing the best way right now it works with 105px and 112 for pagination
    if( $poscustom_style_cate == 's1' && $poscustom_style_totals == 's1') {
            $winproduct_size = 'tw-h-dvh-bt_left_right_cate_off' ;
            switch ($ps_width_product) {
                case '100':
                    $ps_width_product = 112;
                    break;                  
                case '120':
                    $ps_width_product = 125;
                    break;            
                case '140':
                    $ps_width_product = 142; 
                    break;                                
                default:
                    $ps_width_product = 112; //Default with 105px still testing the bet way
                    break;
            }
        }
    else {
            $winproduct_size = 'tw-h-dvh-cate-off' ; // 1 Show Quick Category  
            switch ($ps_width_product) {
                case '100':
                    $ps_width_product = 112;
                    break;                   
                case '120':
                    $ps_width_product = 125;
                    break;            
                case '140':
                    $ps_width_product = 135;
                    break;                                
                default:
                    $ps_width_product = 112; //Default with 105px still testing the bet way
                    break;
            }
        }  
/*Border Subcategory dropup*/
$style = "
    <style>
        .dropdown-class {
            /* width: 100%; */
            background-color: #fdfefe;
            border-radius: 20px; 
            border: 2px solid $border_theme_color !important;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1) !important;
        }
        /* Category & Brand Cards */
        .card {
            border-radius: 20px;
            border: 1px dashed $border_theme_color;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.3s ease;
            /* background-color: #f0f0f0; */    
        }                    
    </style>
";
echo $style;

@endphp

{{-- Size pos_sidebar_s1
{{ $ps_width_product }}
 --}}    
    <div class="row tw-mb-1  ">
        <!-- used in repair : filter for service/product -->
        <div class="col-md-6 hide" id="product_service_div">
            {!! Form::select(
                'is_enabled_stock',
                ['' => __('messages.all'), 'product' => __('sale.product'), 'service' => __('lang_v1.service')],
                null,
                ['id' => 'is_enabled_stock', 'class' => 'select2', 'name' => null, 'style' => 'width:100% !important'],
            ) !!}
        </div>
    
        {{-- #JCN remove this button to show features products
        <div class="col-sm-4 @if (empty($featured_products)) hide @endif" id="feature_product_div">
            <button type="button" class="btn btn-primary btn-flat"
                id="show_featured_products">@lang('lang_v1.featured_products')</button>
        </div>
        --}}
    </div>
    
    <div class="row" id="featured_products_box" style="display: none;">
        @if (!empty($featured_products))
            @include('PosCustom::sale_pos.partials.featured_products')
        @endif
    </div>
    
    {{--#JCN Ini--}}
    {{-- 202 If no allowed dont show the bar--}}
    <!-- Divider and categories -->

    {{--#JCN 2025 taking from parameters settings/POS needs settings_pos.blade --}}
    <div class = "{{-- min-h-screen --}}  grid-cols-1 grid-rows-[auto,1fr] grid md:grid-cols-[100px,1fr] lg:grid-cols-[100px,1fr] {{-- tw-gap-1 --}}  ">
        <!-- Divider next previous -->    
        <div class="col-span-full {{$theme_pos_class}} " style="border-radius: 8px; border: 1px solid #FFF;" >
            {{-- #JCN using pagination no scroll background-color:#D0D5DD; --}}
            <div class="btn-group-justified{{-- pos-grid-nav --}}">
                <div class="btn-group tw-rounded-xl"  >
                    <button style="color: #FFF" class="btn {{$theme_pos_class}} " title="Previous" type="button" id="previous">
                        {{-- <i class="fa fa-chevron-circle-left fa-lg" aria-hidden="true" > </i>  --}}
                        <i class="fa fa-backward fa-lg" aria-hidden="true" > </i> 
                        @if (!isMobile()) @lang('poscustom::lang.previous')  @endif
                    </button>
                </div>
                {{-- Pagination: To show the current and last page --}}
                {{-- 
                    <div class="btn-group primary" >
                        <input value="@lang('lang_v1.all_category')"  disabled class="btn"  id="name_category" style="background-color:#D0D5DD; font-weight: bold; text-align: center;color: red; "> 
                    </div> 
                --}}
                <div class="btn-group primary" >
                    <input  disabled class="btn"  id="pagePN" style="background-color:#D0D5DD; font-weight: bold; text-align: center;color: red; "> 
                </div>
                    
                <div class="btn-group tw-rounded-xl" >
                    <button style="color: #FFF" class="btn {{$theme_pos_class}}" title="Next" type="button" id="next">
                        @if (!isMobile()) @lang('poscustom::lang.next') @endif
                        {{-- <i class="fa fa-chevron-circle-right fa-lg" aria-hidden="true"></i> --}}
                        <i class="fa fa-forward fa-lg" aria-hidden="true" > </i> 
                    </button>
                </div>

                <!--Spinner loading-->
                <div class="btn-group text-center" id="suggestion_page_loader" style="display: none;" >
                    <i class="fa fa-spinner fa-spin fa-2x" style="color:white"></i>   
                </div>

                {{--#JCN Show features products--}}
                <div class="btn-group @if (empty($featured_products)) hidden @endif " >
                    {{--#JCN Add button style like category to $featured_products --}}
                    <div class="tw-pt-1" id="feature_product_div">
                        <div class="dw-drawer dw-drawer-end" >
                            <input id="my-drawer-features" type="checkbox" class="drawer-toggle">
                            <div class="drawer-features" id="show_featured_products">
                                <!-- Page content here -->
                                <label for="my-drawer-features" 
                                    class="{{$theme_pos_class}} hover:tw-animate-pulse focus:tw-outline-none focus:tw-ring-2 focus:tw-ring-blue-500 focus:tw-ring-offset-2 active:tw-from-indigo-700 active:tw-to-blue-700 lg:tw-w-[98%] tw-w-full tw-flex tw-items-center tw-justify-center tw-gap-1 tw-text-base md:tw-text-lg tw-text-white tw-font-semibold tw-rounded-xl {{-- tw-h-12 --}} tw-cursor-pointer">
                                        <svg xmlns="http://www.w3.org/2000/svg" 
                                            class="tw-w-5 icon icon-tabler icon-tabler-brand-beats" width="44" height="30" 
                                            viewBox="0 0 24 24" stroke-width="1.5" stroke="#ffffff" fill="none" 
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m17 21-5-4-5 4V3.889a.92.92 0 0 1 .244-.629.808.808 0 0 1 .59-.26h8.333a.81.81 0 0 1 .589.26.92.92 0 0 1 .244.63V21Z"/>
                                        </svg>
                                        <small class="title_muted"  > @lang('lang_v1.featured_products') </small>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                {{--#JCN Show categories $poscustom_style_cate == 's1' category in the middle hiiden the button category--}}
                <div class="btn-group @if ( $poscustom_style_cate == 's1')  hidden @endif " >
                        @if (!empty($categories))
                            <div class="{{-- col-md-6 --}} tw-pt-1" id="product_subcategory_div">
                                <div class="tw-dw-drawer tw-dw-drawer-end">
                                    <input id="category-drawer-toggle" type="checkbox" class="tw-dw-drawer-toggle"> 
                                    <!--Only show the category if the poscustom_bar_show its false--> 
                                        <div class="tw-dw-drawer-content">
                                            <!-- Page content here -->
                                            <label for="category-drawer-toggle"
                                                class="{{$theme_pos_class}} hover:tw-animate-pulse tw-w-full tw-flex tw-items-center tw-justify-center tw-gap-1 tw-text-base md:tw-text-lg tw-text-white tw-font-semibold tw-rounded-xl {{-- tw-h-12 --}} tw-cursor-pointer">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    class="tw-w-5 icon icon-tabler icon-tabler-category-plus" width="44" height="30"
                                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="#ffffff" fill="none"
                                                    stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M4 4h6v6h-6zm10 0h6v6h-6zm-10 10h6v6h-6zm10 3h6m-3 -3v6" />
                                                </svg> 
                                                {{-- 🗂️ --}}
                                                <small class="title_muted" > @lang('category.category') </small>
                                            </label>
                                        </div> 

                                    <div class="tw-dw-drawer-side" id="category-drawer" style="z-index: 4000;@if (isMobile()) width: 50% @endif" >
                                        <label for="category-drawer-toggle" aria-label="close sidebar" class="tw-dw-drawer-overlay overlay-category"></label>
                                        <div class="tw-dw-menu tw-w-72 tw-h-screen tw-bg-white tw-p-6 ">
                                            <div class="align-items-center mb-4 " style="margin-bottom: 20px;">
                                                <button type="button"
                                                    class="tw-dw-btn tw-dw-btn-accent category-back tw-bg-transparent tw-border-2"
                                                    style="display: none">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        class="tw-w-5 icon icon-tabler icon-tabler-chevron-left" width="44"
                                                        height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50"
                                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path d="M15 6l-6 6l6 6" />
                                                    </svg>
                                                </button>
                                                <button type="button" class="tw-dw-btn {{-- tw-dw-btn-error --}} close-side-bar-category close-side-bar">
                                                    <i class="fa fa-times-circle" aria-hidden="true"></i>
                                                </button>
                                                <h3 class="text-center text-primary mb-4" style="margin-bottom: 0px; margin-top:5px; font-size: 18px; " >🗃️ @lang('poscustom::lang.categories')</h3>
                                            </div>
                                            <div class="row" >
                                                <div class="panel-group col-xs-12 mb-3" id="accordion">
                                                    <div class="panel panel-default cardpanel main-category no-print" data-value="all" data-parent="0">
                                                            <div class="card  active-all"  >
                                                                <div class="panel-title card-body">
                                                                    <button type="button" >📱@lang('lang_v1.all_category')</button> {{--Must be replace by local lang PosCustom--}}
                                                                </div>
                                                            </div>                                                
                                                    </div>    
                                                    {{--Ini categories dropup--}}
                                                    {{--Style accordion--}}                                            
                                                    {{--Ini categories dropup--}}
                                                    @foreach ($categories as $category)
                                                    <div class=" panel dropdown dropleft  {{--float-right  panel-default --}} @if (empty($category['sub_categories'])) main-category cardpanel  @else main-category-div cardpanel   @endif    no-print" data-value="{{ $category['id'] }}" data-name="{{ $category['name'] }}" data-parent="0">
                                                        <div data-toggle="dropdown" id="{{$category['id']}}" class="card dropdown-toggle active-{{$category['id']}} "  >
                                                            <div  class="tw-p-3 tw-inline-flex tw-items-center">
                                                                @if (!empty($category['sub_categories'])) {{--If there isnt empty has categories--}}
                                                                    {{-- <i style="font-size:24px " id="icon_subcate" class="fa tw-mr-5">&#xf139;&#xf13a; down arrow</i>                     --}}
                                                                    <i class="fa fa-chevron-circle-down" id="icon_subcate" style="font-size:24px;">&nbsp;</i>
                                                                @endif
                                                                <img style="width: 40px;height: 30px" class="img-tumbnail tw-rounded-md" src="{{asset('/uploads/category_images/' . $category['image'])}}" >
                                                                {{-- <img style="width: 40px" class="img-tumbnail " src="{{asset('/img/' . $category['id'] .'.jpg')}}" > --}}
                                                                {{-- <a   class=" title_muted " title="{{ $category['name'] }}" >{{ $category['name'] }}</a> --}}
                                                                <p class="title_muted_cate">&nbsp; {{ $category['name'] }} </p>
                                                            </div>
                                                        </div>
                                                        {{--SubCategories--}}
                                                        @if (!empty($category['sub_categories'])) {{--Isnt a parent category--}}
                                                            <div class="dropdown-menu dropdown-class {{--tw-ml-4 in --}}" > 
                                                                @foreach ($category['sub_categories'] as $sc)
                                                                    <div  class=" card cardpanel tw-m-2  {{-- panel-body --}} active-{{$sc['id']}}" value="{{$sc['id']}}" style="color: black !important;">
                                                                        <div class="tw-p-2 product_subcategory no-print" data-maincategory="{{$category['id']}}" data-value="{{ $sc['id'] }}" data-name="{{$sc['name']}}" data-parent="1">
                                                                            {{--#JCN The id=category_id is to detect the category parent to OFF/ON--}}
                                                                            <div class=" {{-- panel-footer --}} tw-inline-flex tw-items-center">
                                                                                <img style="width: 40px;height: 30px" class="img-tumbnail tw-rounded-md" src="{{asset('/uploads/category_images/' . $sc['image'])}}" >
                                                                                {{-- <img style="width: 40px" src="{{asset('/img/' .$sc['id'] .'.jpg')}}" class="img-tumbnail"> --}}
                                                                                {{-- <button type="button" class="tw-ml-2 title_muted">{{ $sc['name'] }}</button> --}}
                                                                                <p class="title_muted_cate">&nbsp; {{ $sc['name'] }} </p>
                                                                            </div>
                                                                        </div>
                                                                    </div>    
                                                                @endforeach    
                                                            </div>                                                
                                                        @endif
                                                    </div>
                                                    @endforeach
                                                </div>
                                            </div>  {{--/row--}}                                
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif 
                </div>
                {{--#JCN Show brands--}}
                <div class="btn-group" >
                    @if (!empty($brands))
                        <div class="{{-- col-md-6 --}} tw-pt-1" id="product_brand_div">
                            <div class="tw-dw-drawer tw-dw-drawer-end">
                                {{--#JCN Tail repsonsive move to the bar previous/next--}}
                                <input id="brand-drawer-toggle" type="checkbox" class="tw-dw-drawer-toggle">
                                <div class="tw-dw-drawer-content">
                                    <!-- Page content here -->
                                    <label for="brand-drawer-toggle"
                                        class="{{$theme_pos_class}} hover:tw-animate-pulse focus:tw-outline-none{{--  focus:tw-ring-2 focus:tw-ring-blue-500 focus:tw-ring-offset-2 active:tw-from-indigo-700 active:tw-to-blue-700  lg:tw-w-[98%] --}} tw-w-full tw-flex tw-items-center tw-justify-center tw-gap-1 tw-text-base md:tw-text-lg tw-text-white tw-font-semibold tw-rounded-xl  tw-cursor-pointer">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="tw-w-5 icon icon-tabler icon-tabler-brand-beats"
                                            width="44" height="30" viewBox="0 0 24 24" stroke-width="1.5" stroke="#ffffff"
                                            fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                            <path d="M12.5 12.5m-3.5 0a3.5 3.5 0 1 0 7 0a3.5 3.5 0 1 0 -7 0" />
                                            <path d="M9 12v-8" />
                                        </svg>
                                        {{-- 🏬 --}} 
                                        <small class="title_muted" >@lang('brand.brands') </small>
                                    </label>
                                </div>
                                <div class="tw-dw-drawer-side" id="brand-drawer" style="z-index: 4000;@if (isMobile()) width: 50% @endif">
                                    <label for="brand-drawer-toggle" aria-label="close sidebar" class="tw-dw-drawer-overlay overlay-brand"></label>
                                    <div class="tw-dw-menu tw-w-72 tw-min-h-full tw-bg-white tw-p-6">
                                        <div class="mb-3" style="margin-bottom: 20px;">
                                            <button type="button" class="tw-dw-btn {{-- tw-dw-btn-error --}} close-side-bar-brand close-side-bar">
                                                <i class="fa fa-times-circle" aria-hidden="true"></i>
                                            </button>
                                            <h3 class="text-center text-primary mb-4" style="font-size: 18px;" >📇 @lang('brand.brands')</h3>
                                        </div>
                                        <div class="row product_brand_div">
                                            @foreach ($brands as $key => $brand)
                                                <div class="tw-w-full product_brand no-print" data-value="{{ $key }}">
                                                    <div class="card cardpanel ">
                                                        <div class="card-body tw-p-2">
                                                            <button type="button" >🏬 {{ $brand }}</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
        
            </div>	
        </div>
        <!-- Divider left cate (in the middle) -->
        @if (!empty($pos_settings['poscustom_bar_show']) && !isMobile() ) {{--Show Custom Bar categories --}}
            <div class = "{{ $winproduct_size }}  border md:row-start-2   md:block tw-overflow-y-auto tw-overflow-hidden tw-rounded-lg " >
                {{-- <div style="clear:both; padding: 0px 2px 2px 0px;"></div> --}}
                {{-- To center items in the DIV and make responsive--}}
                {{--<div class="tw-text-center"> #JCN Without background color--}}
                    {{--#JCN 2025 max-height for mobile --}}
                <div class="tw-flex tw-flex-col tw-text-center  {{-- {{$theme_pos_class}} --}}" 
                        {{-- style="border-radius: 8px; border: 1px solid #FFF;" --}} > {{-- To center items in the DIV + responsive--}}
                    @if(!empty($categories))            
                        @include('PosCustom::sale_pos.partials.pos_category_product_left')
                    @endif 

                </div>
            </div>
        @endif
{{--             $winproduct_size: 
    {{ $winproduct_size }} --}}
        <div class=" tw-px-1 md:row-start-2 @if (empty($pos_settings['poscustom_bar_show'])) tw-col-span-2 @endif  md:block {{-- tw-overflow-y-auto tw-overflow-hidden --}} tw-rounded-lg ">
            <div class="row">
                <input type="hidden" id="suggestion_page" value="1">
                {{--#JCN To save default category & Size-Width of the product box--}}
                
                <input type="hidden" name="ps_default_category" id="ps_default_category" value="{{$ps_default_category}}">
                <input type="hidden" name="ps_width_product" id="ps_width_product" value="{{$ps_width_product}}">
                <input type="hidden" name="color_category_tab" id="color_category_tab" value="{{$business_details->theme_color}}">

                <div class="col-md-12">
                    {{--#JCN Testing responsive <div class="tw-text-center md:max-xl:flex" id="product_list_body"></div> --}}
                    <div class="{{ $winproduct_size }} border tw-justify-items-stretch {{--tw-justify-betwwen tw-justify-center  tw-gap-0 tw-p-2 tw-h-screen --}}   tw-text-center tw-overflow-y-auto tw-overflow-hidden"  id="product_list_body">
                    </div>
                </div>
                <div class="col-md-12 text-center" id="suggestion_page_loader" style="display: none;">
                    {{-- <i class="fa fa-spinner fa-spin fa-2x" style="color:red"></i> --}}
                </div>
            </div>
        </div>
    <div> 