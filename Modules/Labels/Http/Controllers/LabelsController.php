<?php

namespace Modules\Labels\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Utils\ProductUtil;
use Milon\Barcode\DNS1D;
use Milon\Barcode\DNS2D;
use App\Barcode;
use DB;

class LabelsController extends Controller
{

    protected $productUtil;

    /**
     * Constructor
     *
     * @param  ProductUtils  $productutil
     * @return void
     */
    public function __construct(ProductUtil $productUtil)
    {
        $this->productUtil = $productUtil;

    }


/**
 * Enhanced label printing interface
 */
public function enhancedShow(Request $request)
{
    $business_id = $request->session()->get('user.business_id');
    $purchase_id = $request->get('purchase_id', false);
    $product_id = $request->get('product_id', false);

    //Get products for the business
    $products = [];
    $price_groups = [];
    if ($purchase_id) {
        $products = $this->transactionUtil->getPurchaseProducts($business_id, $purchase_id);
    } elseif ($product_id) {
        $products = $this->productUtil->getDetailsFromProduct($business_id, $product_id);
    }

    //get price groups
    $price_groups = [];
    if (! empty($purchase_id) || ! empty($product_id)) {
        $price_groups = SellingPriceGroup::where('business_id', $business_id)
            ->active()
            ->pluck('name', 'id');
    }

    $barcode_settings = Barcode::where('business_id', $business_id)
        ->orWhereNull('business_id')
        ->select(DB::raw('CONCAT(name, ", ", COALESCE(description, "")) as name, id, is_default'))
        ->get();
    $default = $barcode_settings->where('is_default', 1)->first();
    $barcode_settings = $barcode_settings->pluck('name', 'id');

    // Fetch the business logo path
    $business_logo = request()->session()->get('business.logo');
    if (! empty($business_logo)) {
        $business_logo = asset('uploads/business_logos/' . $business_logo);
    }

    return view('labels::enhanced-show')
        ->with(compact('products', 'barcode_settings', 'default', 'price_groups', 'business_logo'));
}

/**
 * Enhanced preview method (reuses existing logic)
 */
public function enhancedPreview(Request $request)
{
    // Reuse the existing show_label_preview method
    return $this->show_label_preview($request);
}


/**
 * Generate optimized barcode with dynamic sizing
 */
private function generateOptimizedBarcode($data, $label_width_inches, $barcode_size_setting)
{
    $width_factor = $this->calculateOptimalBarcodeWidth($label_width_inches);
    $height_pixels = max(40, $barcode_size_setting * 40);

    $dns1d = new DNS1D();
    $barcode = $dns1d->getBarcodePNG(
        $data,
        'C128',
        $width_factor,
        $height_pixels,
        [0, 0, 0],
        true
    );

    return $barcode;
}

/**
 * Calculate optimal barcode width factor based on label dimensions
 */
private function calculateOptimalBarcodeWidth($label_width_inches)
{
    if ($label_width_inches <= 1.5) {
        return 4; // Narrow labels
    } elseif ($label_width_inches <= 2.5) {
        return 6; // Medium labels
    } else {
        return 8; // Wide labels
    }
}

/**
 * Generate high-resolution QR code with error correction
 */
private function generateOptimizedQRCode($data, $size_setting)
{
    $pixel_size = max(120, $size_setting * 80);

    $dns2d = new DNS2D();
    $qr_code = $dns2d->getBarcodePNG(
        $data,
        'QRCODE',
        $pixel_size,
        $pixel_size,
        [0, 0, 0],
        false,
        3
    );

    return $qr_code;
}

/**
 * Get label image URL from settings
 */
public function getLabelImageUrlFromSettings($business_id)
{
    $business_logo = request()->session()->get('business.logo');
    if (! empty($business_logo)) {
        $business_logo = asset('uploads/business_logos/' . $business_logo);
    }
    return $business_logo;
}


/**
 * Enhanced show_label_preview method with optimizations
 */
public function show_label_preview(Request $request)
{
    try {
        $products = $request->get('products');
        $print = $request->get('print');
        $barcode_setting = $request->get('barcode_setting');
        $business_id = $request->session()->get('user.business_id');

        $barcode_details = Barcode::find($barcode_setting);
        \Log::info('================================');
        \Log::info($request);
        \Log::info('================================');
        
        $barcode_details->stickers_in_one_sheet = $barcode_details->is_continuous ? $barcode_details->stickers_in_one_row : $barcode_details->stickers_in_one_sheet;
        $barcode_details->paper_height = $barcode_details->is_continuous ? $barcode_details->height : $barcode_details->paper_height;

        if ($barcode_details->stickers_in_one_row == 1) {
            $barcode_details->col_distance = 0;
            $barcode_details->row_distance = 0;
        }

        $business_name = $request->session()->get('business.name');

        // Handle image source selection
        $image_url = null;
        if (!empty($print['image'])) {
            if ($print['image_source'] === 'select_image' && !empty($print['select_image_url'])) {
                $image_url = $print['select_image_url'];
            } elseif ($print['image_source'] === 'label_image') {
                $image_url = $this->getLabelImageUrlFromSettings($business_id);
            }
        }

        $product_details_page_wise = [];
        $total_qty = 0;
        $location_id = 1;

        foreach ($products as $value) {

        $details = $this->productUtil->getDetailsFromVariation($value['variation_id'], $business_id, $location_id, false);
        
            // Format prices properly
            $details->sell_price_inc_tax = $this->productUtil->num_f($details->sell_price_inc_tax) ?: $details->sell_price_inc_tax;
            $details->default_sell_price = $this->productUtil->num_f($details->default_sell_price) ?: $details->default_sell_price;

            if (!empty($value['exp_date'])) {
                $details->exp_date = $value['exp_date'];
            }
            if (!empty($value['packing_date'])) {
                $details->packing_date = $value['packing_date'];
            }
            if (!empty($value['lot_number'])) {
                $details->lot_number = $value['lot_number'];
            }

            if (!empty($value['price_group_id'])) {
                $tax_id = $print['price_type'] == 'inclusive' ?: $details->tax_id;
                $group_prices = $this->productUtil->getVariationGroupPrice($value['variation_id'], $value['price_group_id'], $tax_id);
                $details->sell_price_inc_tax = $group_prices['price_inc_tax'];
                $details->default_sell_price = $group_prices['price_exc_tax'];
            }

            for ($i = 0; $i < $value['quantity']; $i++) {
                $page = intdiv($total_qty, $barcode_details->stickers_in_one_sheet);

                if ($total_qty % $barcode_details->stickers_in_one_sheet == 0) {
                    $product_details_page_wise[$page] = [];
                }

                // OPTIMIZATION: Pre-generate optimized barcodes and QR codes
                if (!empty($print['barcode'])) {
                    $details->optimized_barcode = $this->generateOptimizedBarcode(
                        $details->sub_sku,
                        $barcode_details->width,
                        $print['barcode_size'] ?? 0.8
                    );
                }

                if (!empty($print['qrcode'])) {
                    $details->optimized_qrcode = $this->generateOptimizedQRCode(
                        $details->sub_sku,
                        $print['qrcode_size'] ?? 1.4
                    );
                }

                $product_details_page_wise[$page][] = $details;
                $total_qty++;
            }
        }


        $margin_top = $barcode_details->is_continuous ? 0 : $barcode_details->top_margin * 1;
        $margin_left = $barcode_details->is_continuous ? 0 : $barcode_details->left_margin * 1;
        $paper_width = $barcode_details->paper_width * 1;
        $paper_height = $barcode_details->paper_height * 1;

        $i = 0;
        $len = count($product_details_page_wise);
        $is_first = false;
        $is_last = false;

        $factor = (($barcode_details->width / $barcode_details->height)) / ($barcode_details->is_continuous ? 2 : 4);
        $html = '';

        foreach ($product_details_page_wise as $page => $page_products) {
            $output = view('labels::layouts.partials.enhanced_preview')  // Use enhanced template
                ->with(compact('print', 'page_products', 'business_name', 'barcode_details', 'margin_top', 'margin_left', 'paper_width', 'paper_height', 'is_first', 'is_last', 'factor', 'image_url'))
                ->render();

            $html .= $output;
        }

        return response()->json(['success' => true, 'html' => $html]);
    } catch (\Exception $e) {
        \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

        return response()->json([
            'success' => false,
            'msg' => __('lang_v1.barcode_label_error')
        ]);
    }
}












    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        return view('labels::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('labels::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('labels::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('labels::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
}
