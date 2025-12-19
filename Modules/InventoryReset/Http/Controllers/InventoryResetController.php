<?php

namespace Modules\InventoryReset\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Product;
use App\VariationLocationDetails;
use App\Transaction;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\InventoryReset\Entities\InventoryResetLog;

class InventoryResetController extends Controller
{
    protected ModuleUtil $moduleUtil;
    protected ProductUtil $productUtil;
    protected TransactionUtil $transactionUtil;

    public function __construct(ModuleUtil $moduleUtil, ProductUtil $productUtil, TransactionUtil $transactionUtil)
    {
        $this->moduleUtil = $moduleUtil;
        $this->productUtil = $productUtil;
        $this->transactionUtil = $transactionUtil;
    }

    /**
     * Display the inventory reset dashboard
     */
    public function index(): View
    {
        if (!auth()->user()->can('inventory_reset.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = session()->get('user.business_id');

        // Get inventory statistics
        $stats = $this->getInventoryStats($business_id);

        // Get recent reset logs using business scoping pattern
        $recentResets = InventoryResetLog::business()
            ->with(['user', 'resetItems'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get business locations using InventoryManagement pattern
        $locations = \App\BusinessLocation::where('business_id', $business_id)->get();

        return view('inventoryreset::index', compact('stats', 'recentResets', 'locations'));
    }

    /**
     * Execute the inventory reset
     */
    public function executeReset(Request $request): JsonResponse
    {
        if (!auth()->user()->can('inventory_reset.create')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
        }

        $business_id = session()->get('user.business_id');

        // Validate location if provided
        if (!empty($request->location_id)) {
            $locationExists = \App\BusinessLocation::where('id', $request->location_id)
                ->where('business_id', $business_id)
                ->exists();

            if (!$locationExists) {
                return response()->json(['success' => false, 'message' => 'The selected location is invalid for your business.'], 422);
            }
        }

        // Support both new structure and old structure for backward compatibility
        $resetType = !empty($request->reset_type) ? $request->reset_type : 'all_products';
        $resetMode = !empty($request->reset_mode) ? $request->reset_mode : 'all_levels';
        $operationType = !empty($request->operation_type) ? $request->operation_type : 'reset_to_zero';
        $targetQuantity = $request->target_quantity ?? 0;

        $request->validate([
            // New structure (preferred)
            'reset_type' => 'nullable|in:all_products,selected_products',
            'reset_mode' => 'nullable|in:all_levels,positive_only,negative_only,zero_only',
            'operation_type' => 'nullable|in:reset_to_zero,set_to_quantity,add_quantity',
            'target_quantity' => 'nullable|numeric|min:1', // Changed: min:1 instead of min:0

            // Common fields
            'product_ids' => 'required_if:reset_type,selected_products|array',
            'product_ids.*' => 'exists:products,id,business_id,' . $business_id,
            'location_id' => 'nullable|exists:business_locations,id',
            'reason' => 'required|string|max:500',
            'confirm_reset' => 'required|accepted'
        ]);

        // Additional validation for set_to_quantity and add_quantity operations
        if (($operationType === 'set_to_quantity' || $operationType === 'add_quantity') && is_null($request->target_quantity)) {
            return response()->json(['success' => false, 'message' => 'Target quantity is required for set and add operations.'], 422);
        }

        // Additional validation: Prevent using set_to_quantity=0 (use reset_to_zero instead)
        if ($operationType === 'set_to_quantity' && $request->target_quantity == 0) {
            return response()->json([
                'success' => false,
                'message' => 'To set quantity to zero, please use "Reset to Zero" operation instead.'
            ], 422);
        }

        try {
            // Set resource limits for large operations
            set_time_limit(0);
            ini_set('max_execution_time', 0);
            ini_set('memory_limit', '512M');

            // Check server resources before starting
            $start_time = microtime(true);
            $current_memory = memory_get_usage(true);
            Log::info('Starting inventory reset operation', [
                'business_id' => $business_id,
                'current_memory_mb' => round($current_memory / 1024 / 1024, 2),
                'reset_type' => $resetType,
                'reset_mode' => $resetMode
            ]);

            DB::beginTransaction();

            // Fix stock mismatches before reset if requested
            // DISABLED: This was causing issues with using sold quantities instead of current stock
            /*
            if ($request->fix_stock_mismatches) {
                Log::info('Running stock mismatch fixes before inventory operation', [
                    'business_id' => $business_id,
                    'reset_type' => $resetType,
                    'location_id' => $request->location_id
                ]);

                $this->fixStockMismatchesBeforeReset($business_id, $request);
            }
            */

            // Create reset log entry (update database to store new structure)
            $resetLog = InventoryResetLog::create([
                'business_id' => $business_id,
                'user_id' => Auth::id(),
                'reset_type' => $resetType,
                'reset_mode' => $resetMode,
				'target_quantity' => $operationType === 'set_to_quantity' ? $targetQuantity : 0,
                'location_id' => $request->location_id,
                'reason' => $request->reason,
                'status' => 'processing'
            ]);

            $resetCount = 0;

            // Get total count for batch processing
            $total_items = $this->getVariationLocationDetailsCount($business_id, $request, $resetType, $resetMode);
            Log::info("Found {$total_items} items to process for inventory reset");

            // Process in batches to prevent memory issues
            $batch_size = 100;
            $processed = 0;
            $adjustmentData = [];
            $itemsToReset = [];

            while ($processed < $total_items) {
                Log::info("Processing batch " . ceil($processed / $batch_size + 1) . " ({$processed}/{$total_items})");

                // Get batch of variation location details
                $variationLocationDetails = $this->getVariationLocationDetailsBatch(
                    $business_id, $request, $resetType, $resetMode, $processed, $batch_size
                );

                if ($variationLocationDetails->isEmpty()) {
                    break;
                }

                foreach ($variationLocationDetails as $vld) {
                    $currentStock = $vld->qty_available;
                    $shouldProcess = true; // Items are pre-filtered

                    // Determine target quantity based on operation type
                    $targetStock = ($operationType === 'set_to_quantity') ? $targetQuantity : 0;

                    // Determine the quantity to add based on operation type
                    if ($operationType === 'add_quantity') {
                        $addQuantity = $targetQuantity; // Add the specified quantity
                    } elseif ($operationType === 'set_to_quantity') {
                        $addQuantity = $targetQuantity - $currentStock; // Set to specific quantity (difference)
                    } else {
                        $addQuantity = 0 - $currentStock; // Reset to zero (subtract current)
                    }

                    // Calculate adjustment based on operation type

                    // Only process items that actually need adjustment
                    if ($shouldProcess && $addQuantity != 0) {
                        $adjustmentData[] = [
                            'product_id' => $vld->product_id,
                            'variation_id' => $vld->variation_id,
                            'location_id' => $vld->location_id,
                            'current_qty' => $currentStock,
                            'target_qty' => $targetStock,
                            'adjustment_qty' => $addQuantity, // Use addQuantity instead of difference
                        ];

                        // Log items for audit trail
                        $finalQuantity = $currentStock + $addQuantity;
                        $itemsToReset[] = [
                            'product_id' => $vld->product_id,
                            'variation_id' => $vld->variation_id,
                            'location_id' => $vld->location_id,
                            'quantity_before' => $currentStock,
                            'quantity_after' => $finalQuantity
                        ];
                    }
                }

                $processed += count($variationLocationDetails);

                // Clear memory after each batch
                unset($variationLocationDetails);
                if (function_exists('gc_collect_cycles')) {
                    gc_collect_cycles();
                }

                // Small delay to prevent overwhelming the server
                usleep(50000); // 0.05 second
            }

            // Create a comprehensive inventory reset transaction for audit trail
            $stockAdjustmentTransaction = null;

            // Always create a stock adjustment transaction to record the reset operation
            if (!empty($itemsToReset)) {
                $stockAdjustmentTransaction = $this->createInventoryResetTransaction($business_id, $resetLog, $itemsToReset);
            }

            // CRITICAL FIX: Directly update variation_location_details with actual stock adjustments
            // Note: We cannot rely on UPOS automatic updates as they don't work in all cases
            if (!empty($adjustmentData)) {
                foreach ($adjustmentData as $adjustment) {
                    // Use DB::raw to handle both positive and negative adjustments correctly
                    VariationLocationDetails::where('variation_id', $adjustment['variation_id'])
                        ->where('product_id', $adjustment['product_id'])
                        ->where('location_id', $adjustment['location_id'])
                        ->update(['qty_available' => DB::raw('qty_available + (' . $adjustment['adjustment_qty'] . ')')]);
                }

                Log::info('InventoryReset: Updated variation_location_details', [
                    'items_updated' => count($adjustmentData)
                ]);
            }

            // Count reset items (both adjusted and already-zero items)
            $resetCount = count($itemsToReset);
            $adjustedCount = count($adjustmentData);

            // Create reset item logs
            foreach ($itemsToReset as $item) {
                $resetLog->resetItems()->create($item);
            }

            // Update reset log status
            $resetLog->update([
                'status' => 'completed',
                'items_reset' => $resetCount,
                'completed_at' => now()
            ]);

            DB::commit();

            $end_time = microtime(true);
            $execution_time = round($end_time - $start_time, 2);
            $peak_memory = round(memory_get_peak_usage(true) / 1024 / 1024, 2);

            Log::info('Inventory operation completed', [
                'business_id' => $business_id,
                'user_id' => Auth::id(),
                'reset_type' => $resetType,
                'reset_mode' => $resetMode,
                'operation_type' => $operationType,
                'target_quantity' => $targetQuantity,
                'items_processed' => $resetCount,
                'items_adjusted' => $adjustedCount,
                'execution_time_seconds' => $execution_time,
                'peak_memory_mb' => $peak_memory
            ]);

            // Create appropriate success message based on operation and what was processed
            $typeText = ($resetType === 'selected_products') ? 'selected products' : 'all products';
            $modeText = match ($resetMode) {
                'positive_only' => 'positive stock',
                'negative_only' => 'negative stock',
                'zero_only' => 'zero stock',
                default => 'any stock level'
            };
            $operationText = match ($operationType) {
                'set_to_quantity' => "set to {$targetQuantity} units",
                'add_quantity' => "increased by {$targetQuantity} units",
                default => 'reset to zero'
            };

            if ($adjustedCount > 0) {
                $message = __('inventoryreset::lang.reset_completed_successfully') . " ({$adjustedCount} {$typeText} with {$modeText} {$operationText})";
            } else {
                $message = __('inventoryreset::lang.reset_completed_successfully') . " (No {$typeText} with {$modeText} found to process)";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'items_reset' => $resetCount,
                'items_adjusted' => $adjustedCount,
                'reset_id' => $resetLog->id
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            if (isset($resetLog)) {
                $resetLog->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
            }

            Log::error('Inventory reset failed', [
                'business_id' => $business_id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => __('inventoryreset::lang.reset_failed') . ': ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reset Mapping - Simplified reset endpoint for resetting to 0 or target quantity
     */
    public function resetMapping(Request $request): JsonResponse
    {
        if (!auth()->user()->can('inventory_reset.create')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
        }

        $business_id = session()->get('user.business_id');

        // Simplified validation for reset-mapping
        $request->validate([
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'exists:products,id,business_id,' . $business_id,
            'location_id' => 'nullable|exists:business_locations,id',
            'operation_type' => 'required|in:reset_to_zero,set_to_quantity',
            'target_quantity' => 'required_if:operation_type,set_to_quantity|nullable|numeric|min:1', // Changed: min:1
            'reason' => 'required|string|max:500'
        ]);

        // Additional validation: Prevent using set_to_quantity=0 (use reset_to_zero instead)
        if ($request->operation_type === 'set_to_quantity' && $request->target_quantity == 0) {
            return response()->json([
                'success' => false,
                'message' => 'To set quantity to zero, please use "Reset to Zero" operation instead.'
            ], 422);
        }

        // Validate location if provided
        if (!empty($request->location_id)) {
            $locationExists = \App\BusinessLocation::where('id', $request->location_id)
                ->where('business_id', $business_id)
                ->exists();

            if (!$locationExists) {
                return response()->json(['success' => false, 'message' => 'The selected location is invalid for your business.'], 422);
            }
        }

        $operationType = $request->operation_type;
        $targetQuantity = $operationType === 'set_to_quantity' ? $request->target_quantity : 0;

        try {
            DB::beginTransaction();

            // Create reset log entry
            $resetLog = InventoryResetLog::create([
                'business_id' => $business_id,
                'user_id' => Auth::id(),
                'reset_type' => 'selected_products',
                'reset_mode' => 'all_levels',
                'target_quantity' => $targetQuantity,
                'location_id' => $request->location_id,
                'reason' => $request->reason,
                'status' => 'processing'
            ]);

            $adjustmentData = [];
            $itemsToReset = [];

            // Get variation location details for specified products
            $query = VariationLocationDetails::query()
                ->join('products', 'variation_location_details.product_id', '=', 'products.id')
                ->where('products.business_id', $business_id)
                ->where('products.type', '!=', 'modifier')
                ->whereIn('variation_location_details.product_id', $request->product_ids)
                ->select('variation_location_details.*');

            // Filter by location if specified
            if (!empty($request->location_id)) {
                $query->where('variation_location_details.location_id', $request->location_id);
            }

            $variationLocationDetails = $query->get();

            foreach ($variationLocationDetails as $vld) {
                $currentStock = $vld->qty_available;

                // Calculate adjustment based on operation type
                if ($operationType === 'set_to_quantity') {
                    $adjustmentQty = $targetQuantity - $currentStock;
                    $finalQuantity = $targetQuantity;
                } else { // reset_to_zero
                    $adjustmentQty = 0 - $currentStock;
                    $finalQuantity = 0;
                }

                // Only process if there's an actual change needed
                if ($adjustmentQty != 0) {
                    $adjustmentData[] = [
                        'product_id' => $vld->product_id,
                        'variation_id' => $vld->variation_id,
                        'location_id' => $vld->location_id,
                        'current_qty' => $currentStock,
                        'target_qty' => $finalQuantity,
                        'adjustment_qty' => $adjustmentQty,
                    ];
                }

                // Log all items for audit trail
                $itemsToReset[] = [
                    'product_id' => $vld->product_id,
                    'variation_id' => $vld->variation_id,
                    'location_id' => $vld->location_id,
                    'quantity_before' => $currentStock,
                    'quantity_after' => $finalQuantity
                ];
            }

            // Create stock adjustment transaction for audit trail
            if (!empty($itemsToReset)) {
                $this->createInventoryResetTransaction($business_id, $resetLog, $itemsToReset);
            }

            // Process actual stock adjustments
            if (!empty($adjustmentData)) {
                foreach ($adjustmentData as $adjustment) {
                    // Use DB::raw to handle both positive and negative adjustments correctly
                    VariationLocationDetails::where('variation_id', $adjustment['variation_id'])
                        ->where('product_id', $adjustment['product_id'])
                        ->where('location_id', $adjustment['location_id'])
                        ->update(['qty_available' => DB::raw('qty_available + (' . $adjustment['adjustment_qty'] . ')')]);
                }
            }

            // Create reset item logs
            foreach ($itemsToReset as $item) {
                $resetLog->resetItems()->create($item);
            }

            // Update reset log status
            $resetLog->update([
                'status' => 'completed',
                'items_reset' => count($itemsToReset),
                'completed_at' => now()
            ]);

            DB::commit();

            $adjustedCount = count($adjustmentData);
            $operationText = $operationType === 'set_to_quantity'
                ? "set to {$targetQuantity} units"
                : 'reset to zero';

            $message = "Reset mapping completed successfully! ({$adjustedCount} items {$operationText})";

            return response()->json([
                'success' => true,
                'message' => $message,
                'items_reset' => count($itemsToReset),
                'items_adjusted' => $adjustedCount,
                'reset_id' => $resetLog->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            if (isset($resetLog)) {
                $resetLog->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
            }

            Log::error('Reset mapping failed', [
                'business_id' => $business_id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Reset mapping failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show reset details
     */
    public function showReset($id): View
    {
        if (!auth()->user()->can('inventory_reset.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = session()->get('user.business_id');

        $resetLog = InventoryResetLog::business()
            ->where('id', $id)
            ->with(['user', 'resetItems.product.unit', 'resetItems.location'])
            ->firstOrFail();

        return view('inventoryreset::show', compact('resetLog'));
    }

    /**
     * Get inventory statistics
     */
    private function getInventoryStats($business_id): array
    {
        $totalProducts = Product::where('business_id', $business_id)
            ->where('type', '!=', 'modifier')
            ->count();

        $productsWithStock = DB::table('variation_location_details')
            ->join('products', 'variation_location_details.product_id', '=', 'products.id')
            ->where('products.business_id', $business_id)
            ->where('variation_location_details.qty_available', '!=', 0)
            ->distinct('variation_location_details.product_id')
            ->count('variation_location_details.product_id');

        $totalStockValue = DB::table('variation_location_details')
            ->join('products', 'variation_location_details.product_id', '=', 'products.id')
            ->join('variations', 'variation_location_details.variation_id', '=', 'variations.id')
            ->where('products.business_id', $business_id)
            ->sum(DB::raw('variation_location_details.qty_available * COALESCE(variations.default_purchase_price, 0)'));

        $lastReset = InventoryResetLog::business()
            ->where('status', 'completed')
            ->orderBy('completed_at', 'desc')
            ->first();

        $lastResetDate = $lastReset ? $lastReset->completed_at : null;

        return [
            'total_products' => $totalProducts,
            'products_with_stock' => $productsWithStock,
            'products_without_stock' => $totalProducts - $productsWithStock,
            'total_stock_value' => $totalStockValue,
            'last_reset_date' => $lastResetDate
        ];
    }

    /**
     * Get products to process based on request parameters
     */
    private function getProductsToReset($business_id, Request $request)
    {
        $query = Product::where('business_id', $business_id)->where('type', '!=', 'modifier');

        if ($request->reset_type === 'selected_products') {
            $query->whereIn('id', $request->product_ids);
        }

        return $query->get();
    }

    /**
     * Get variation location details to process based on new structure
     */
    private function getVariationLocationDetailsToProcess($business_id, Request $request, $resetType, $resetMode)
    {
        try {
            // Step 1: Basic query
            $query = VariationLocationDetails::query();

            // Log::info('InventoryOperation: Basic query created');

            // Step 2: Add business filtering through join
            $query->join('products', 'variation_location_details.product_id', '=', 'products.id')
                ->where('products.business_id', $business_id)
                ->where('products.type', '!=', 'modifier')
                ->select('variation_location_details.*');

            // Log::info('InventoryOperation: Business filtering added');

            // Step 3: Add location filtering if specified
            if (!empty($request->location_id)) {
                $query->where('variation_location_details.location_id', $request->location_id);
                // Log::info('InventoryOperation: Location filtering added for location_id: ' . $request->location_id);
            }

            // Step 4: Add reset type filtering
            if ($resetType === 'selected_products' || $request->reset_type === 'selected_products') {
                $query->whereIn('variation_location_details.product_id', $request->product_ids);
                // Log::info('InventoryOperation: Product filtering added for ' . count($request->product_ids) . ' products');
            }

            // Step 5: Add reset mode filtering based on new structure
            switch ($resetMode) {
                case 'positive_only':
                    $query->where('variation_location_details.qty_available', '>', 0);
                    // Log::info('InventoryOperation: Positive stock filtering - only items with qty > 0');
                    break;

                case 'negative_only':
                    $query->where('variation_location_details.qty_available', '<', 0);
                    // Log::info('InventoryOperation: Negative stock filtering - only items with qty < 0');
                    break;

                case 'zero_only':
                    $query->where('variation_location_details.qty_available', '=', 0);
                    // Log::info('InventoryOperation: Zero stock filtering - only items with qty = 0');
                    break;

                case 'all_levels':
                default:
                    // For operations, we typically want to exclude items that are already at target
                    // This will be refined in the main processing loop
                    // Log::info('InventoryOperation: All levels filtering - any stock level');
                    break;
            }

            // Log::info('InventoryOperation: Stock filtering applied');

            // Step 6: Get results
            $results = $query->get();

            // Log::info('InventoryOperation: Query executed, found ' . $results->count() . ' items');

            return $results;
        } catch (\Exception $e) {
            Log::error('InventoryOperation: Error in getVariationLocationDetailsToProcess: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get variation location details to reset based on request parameters (DEPRECATED - use getVariationLocationDetailsToProcess)
     */
    private function getVariationLocationDetailsToReset($business_id, Request $request)
    {
        try {
            // Step 1: Basic query without relationships
            $query = VariationLocationDetails::query();

            Log::info('InventoryReset: Basic query created');

            // Step 2: Add business filtering through join instead of whereHas
            $query->join('products', 'variation_location_details.product_id', '=', 'products.id')
                ->where('products.business_id', $business_id)
                ->where('products.type', '!=', 'modifier')
                ->select('variation_location_details.*');

            // Log::info('InventoryReset: Business filtering added');

            // Step 3: Add specific filters

            // Filter by location if specified (applies to all reset types)
            if (!empty($request->location_id)) {
                $query->where('variation_location_details.location_id', $request->location_id);
                // Log::info('InventoryReset: Location filtering added for location_id: ' . $request->location_id);
            }

            // Filter by specific products if reset type is 'selected_products'
            if ($request->reset_type === 'selected_products') {
                $query->whereIn('variation_location_details.product_id', $request->product_ids);
                // Log::info('InventoryReset: Product filtering added for ' . count($request->product_ids) . ' products');
            }

            // Log::info('InventoryReset: Type filtering added');

            // Filter by reset mode - if negative only, only get items with negative stock
            if ($request->reset_mode === 'negative_only') {
                $query->where('variation_location_details.qty_available', '<', 0);
                // Log::info('InventoryReset: Negative stock filtering added - only items with qty < 0');
            } elseif ($request->reset_mode === 'all_levels') {
                $query->where('variation_location_details.qty_available', '!=', 0);
                // Log::info('InventoryReset: Non-zero stock filtering added - only items with qty != 0');
            }

            // Log::info('InventoryReset: Type and mode filtering added');

            // Step 4: Get results without eager loading first
            $results = $query->get();

            // Log::info('InventoryReset: Query executed, found ' . $results->count() . ' items');

            return $results;
        } catch (\Exception $e) {
            Log::error('InventoryReset: Error in getVariationLocationDetailsToReset: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create comprehensive inventory reset transaction for complete audit trail
     * FIXED: Now creates separate transactions per location with correct final_total values
     * ENHANCED: Implements two-step process for Set to Custom Quantity when custom > current
     */
    private function createInventoryResetTransaction($business_id, $resetLog, $itemsToReset)
    {
        try {
            // Check if this is a "Set to Custom Quantity" operation
            $isSetToQuantity = isset($resetLog->target_quantity) && $resetLog->target_quantity > 0;

            // Group items by location to create separate transactions per location
            $itemsByLocation = [];
            foreach ($itemsToReset as $item) {
                $locationId = $item['location_id'];
                if (!isset($itemsByLocation[$locationId])) {
                    $itemsByLocation[$locationId] = [];
                }
                $itemsByLocation[$locationId][] = $item;
            }

            $transactions = [];
            $purchaseTransactions = [];

            // Create separate transaction for each location
            foreach ($itemsByLocation as $locationId => $locationItems) {
                // Separate items into two-step vs normal processing
                $itemsNeedingTwoStep = [];
                $itemsNormalAdjustment = [];

                foreach ($locationItems as $item) {
                    $isIncrease = $item['quantity_after'] > $item['quantity_before'];
                    $hasExistingStock = $item['quantity_before'] != 0;

                    // Two-step criteria: Set to Custom Qty + Increase + Has Existing Stock
                    if ($isSetToQuantity && $isIncrease && $hasExistingStock) {
                        $itemsNeedingTwoStep[] = $item;
                    } else {
                        $itemsNormalAdjustment[] = $item;
                    }
                }

                // Log processing strategy for audit
                if (count($itemsNeedingTwoStep) > 0) {
                    Log::info('InventoryReset: Using two-step process for location', [
                        'location_id' => $locationId,
                        'two_step_items' => count($itemsNeedingTwoStep)
                    ]);
                }

                // STEP 1: Process two-step items - Reset to 0 first
                if (!empty($itemsNeedingTwoStep)) {
                    // Create items that reset current stock to 0
                    $resetToZeroItems = [];
                    foreach ($itemsNeedingTwoStep as $item) {
                        $resetToZeroItems[] = [
                            'product_id' => $item['product_id'],
                            'variation_id' => $item['variation_id'],
                            'location_id' => $item['location_id'],
                            'quantity_before' => $item['quantity_before'],
                            'quantity_after' => 0  // Reset to zero
                        ];
                    }

                    // Process reset to zero
                    if (!empty($resetToZeroItems)) {
                        $this->createSingleLocationTransaction($business_id, $locationId, $resetToZeroItems, $resetLog);
                    }

                    // STEP 2: Create purchase transactions for custom quantities
                    foreach ($itemsNeedingTwoStep as $item) {
                        $customQty = $item['quantity_after']; // The target custom quantity
                        $purchaseTransaction = $this->createPurchaseTransactionForCustomQty(
                            $business_id,
                            $locationId,
                            $item,
                            $customQty,
                            $resetLog
                        );
                        $purchaseTransactions[] = $purchaseTransaction;
                    }
                }

                // Process normal adjustment items (including decreases and zero-start increases)
                if (!empty($itemsNormalAdjustment)) {
                    $transaction = $this->createSingleLocationTransaction($business_id, $locationId, $itemsNormalAdjustment, $resetLog);
                    $transactions[] = $transaction;
                }
            }

            // Log summary of transactions created
            if (!empty($purchaseTransactions)) {
                Log::info('InventoryReset: Two-step process completed', [
                    'adjustment_transactions' => count($transactions),
                    'purchase_transactions' => count($purchaseTransactions)
                ]);
            }

            // Return the first transaction for backward compatibility
            return $transactions[0] ?? $purchaseTransactions[0] ?? null;
        } catch (\Exception $e) {
            Log::error('InventoryReset: Error creating inventory reset transaction: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create a single location transaction (extracted for reuse)
     */
    private function createSingleLocationTransaction($business_id, $locationId, $locationItems, $resetLog)
    {
        // Generate reference number for this location's transaction
        $refCount = $this->productUtil->setAndGetReferenceCount('stock_adjustment');
        $refNo = 'IR' . date('Y') . '/' . str_pad($refCount, 4, '0', STR_PAD_LEFT);

                // Calculate total value for final_total using ADJUSTMENT quantities (not existing stock)
                $totalValue = 0;
                foreach ($locationItems as $item) {
                    // Use current purchase price for valuation
                    $variation = \App\Variation::find($item['variation_id']);
                    $currentPrice = 0;
                    if ($variation && $variation->default_purchase_price > 0) {
                        $currentPrice = $variation->default_purchase_price;
                    } else {
                        // Get last purchase price if available from this specific location
                        $lastPurchase = DB::table('purchase_lines')
                            ->join('transactions', 'purchase_lines.transaction_id', '=', 'transactions.id')
                            ->where('purchase_lines.variation_id', $item['variation_id'])
                            ->where('purchase_lines.product_id', $item['product_id'])
                            ->where('transactions.location_id', $locationId)
                            ->orderBy('purchase_lines.created_at', 'desc')
                            ->select('purchase_lines.purchase_price')
                            ->first();
                        if ($lastPurchase) {
                            $currentPrice = $lastPurchase->purchase_price ?? 0;
                        }
                    }
                    // Use adjustment quantity (quantity_after - quantity_before) from CURRENT STOCK
                    $actualAdjustment = $item['quantity_after'] - $item['quantity_before'];
                    $totalValue += abs($actualAdjustment) * $currentPrice;
                }

                // Create transaction for this location with proper final_total
                $transaction = Transaction::create([
                    'business_id' => $business_id,
                    'location_id' => $locationId,
                    'type' => 'stock_adjustment',
                    // 'status' => NULL (not set - standard UPOS leaves this blank)
                    'adjustment_type' => 'normal',
                    'transaction_date' => now(),
                    'ref_no' => $refNo,
                    // 'total_before_tax' => 0 (not set - defaults to 0)
                    'final_total' => $totalValue, // FIXED: Use calculated total value instead of 0
                    'total_amount_recovered' => 0, // Standard UPOS sets this to 0 or user-entered value
                    // 'payment_status' => NULL (not set - not applicable for stock adjustments)
                    'created_by' => Auth::id(),
                    'additional_notes' => $resetLog->reason, // Use user's reason directly
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                // Create stock adjustment records for this location's items
                $adjustmentLines = [];
                foreach ($locationItems as $item) {
                    // Get unit price for proper record keeping from this specific location
                    $variation = \App\Variation::find($item['variation_id']);
                    $unitPrice = 0;
                    if ($variation && $variation->default_purchase_price > 0) {
                        $unitPrice = $variation->default_purchase_price;
                    } else {
                        // Get last purchase price if available from this specific location
                        $lastPurchase = DB::table('purchase_lines')
                            ->join('transactions', 'purchase_lines.transaction_id', '=', 'transactions.id')
                            ->where('purchase_lines.variation_id', $item['variation_id'])
                            ->where('purchase_lines.product_id', $item['product_id'])
                            ->where('transactions.location_id', $locationId)
                            ->orderBy('purchase_lines.created_at', 'desc')
                            ->select('purchase_lines.purchase_price')
                            ->first();
                        if ($lastPurchase) {
                            $unitPrice = $lastPurchase->purchase_price ?? 0;
                        }
                    }

                    // Calculate the actual adjustment quantity from CURRENT STOCK (what was added/subtracted)
                    $actualAdjustment = $item['quantity_after'] - $item['quantity_before'];

                    // Match standard UPOS stock adjustment format
                    // CRITICAL FIX: UPOS treats positive quantity as DECREASE, negative as INCREASE
                    // So we need to INVERT the sign of our adjustment
                    $quantityForDB = -$actualAdjustment;

                    // Create adjustment line with inverted sign (UPOS convention)

                    $adjustmentLines[] = [
                        'product_id' => $item['product_id'],
                        'variation_id' => $item['variation_id'],
                        'quantity' => $quantityForDB, // INVERTED: negative for increase, positive for decrease
                        'unit_price' => $unitPrice,
                        // 'removed_purchase_line' => NULL (omitted - will be NULL by default)
                        // 'secondary_unit_quantity' => 0 (omitted - has default value in DB)
                        // 'lot_no_line_id' => NULL (omitted - will be NULL by default)
                        'transaction_id' => $transaction->id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }

                // Create all adjustment lines in batch using correct table name
                if (!empty($adjustmentLines)) {
                    DB::table('stock_adjustment_lines')->insert($adjustmentLines);
                }

                // FIX: Add mapPurchaseSell call to create transaction_sell_lines_purchase_lines mapping records
                // This is CRITICAL for proper inventory tracking and FIFO/LIFO calculations
                // IMPORTANT: Only map DECREASES (negative adjustments) - increases don't need mapping
                // FIXED: Now maps per location to get correct purchase_line_id for each location
                if (!empty($adjustmentLines)) {
                    // Reload the stock adjustment lines as model instances for mapPurchaseSell
                    $adjustmentLineModels = DB::table('stock_adjustment_lines')
                        ->where('transaction_id', $transaction->id)
                        ->get();

                    // Filter only DECREASES (where actual adjustment was negative)
                    $decreaseLines = [];

                    foreach ($adjustmentLineModels as $line) {
                        // Find corresponding item in locationItems to check if it was a decrease
                        foreach ($locationItems as $item) {
                            if ($item['product_id'] == $line->product_id &&
                                $item['variation_id'] == $line->variation_id &&
                                $item['location_id'] == $locationId) {
                                $actualAdjustment = $item['quantity_after'] - $item['quantity_before'];

                                // Only map if it was a DECREASE (negative adjustment)
                                if ($actualAdjustment < 0) {
                                    $decreaseLines[] = (object)[
                                        'id' => $line->id,
                                        'product_id' => $line->product_id,
                                        'variation_id' => $line->variation_id,
                                        'quantity' => $line->quantity,
                                        'lot_no_line_id' => $line->lot_no_line_id
                                    ];
                                }
                                break;
                            }
                        }
                    }

                    // Only call mapPurchaseSell if there are decrease lines to map
                    // FIXED: Call mapPurchaseSell with correct location_id to get proper purchase_line_id
                    if (!empty($decreaseLines)) {
                        // Enable overselling for inventory reset to handle cases where purchase history is insufficient
                        $pos_settings = session()->get('business.pos_settings', []);
                        if (is_string($pos_settings)) {
                            $pos_settings = json_decode($pos_settings, true) ?? [];
                        }
                        $pos_settings['allow_overselling'] = true; // Force allow overselling for inventory reset

                        $business = [
                            'id' => $business_id,
                            'accounting_method' => session()->get('business.accounting_method', 'fifo'),
                            'location_id' => $locationId, // FIXED: Use correct location_id for this transaction
                            'pos_settings' => $pos_settings
                        ];

                        $this->transactionUtil->mapPurchaseSell($business, $decreaseLines, 'stock_adjustment');
                    }
                }

        Log::info('InventoryReset: Created inventory reset transaction for location', [
            'transaction_id' => $transaction->id,
            'location_id' => $locationId,
            'ref_no' => $refNo,
            'items_logged' => count($adjustmentLines),
            'total_value' => $totalValue
        ]);

        return $transaction;
    }


    /**
     * Create purchase transaction for custom quantity (Step 2 of two-step process)
     * Used when Set to Custom Quantity where custom > current
     */
    private function createPurchaseTransactionForCustomQty($business_id, $locationId, $item, $customQty, $resetLog)
    {
        try {
            // Get product variation details for pricing
            $variation = \App\Variation::find($item['variation_id']);

            // Get purchase price - use default or last purchase price
            $purchasePrice = 0;
            if ($variation && $variation->default_purchase_price > 0) {
                $purchasePrice = $variation->default_purchase_price;
            } else {
                // Get last purchase price from this location
                $lastPurchase = DB::table('purchase_lines')
                    ->join('transactions', 'purchase_lines.transaction_id', '=', 'transactions.id')
                    ->where('purchase_lines.variation_id', $item['variation_id'])
                    ->where('purchase_lines.product_id', $item['product_id'])
                    ->where('transactions.location_id', $locationId)
                    ->orderBy('purchase_lines.created_at', 'desc')
                    ->select('purchase_lines.purchase_price')
                    ->first();

                if ($lastPurchase) {
                    $purchasePrice = $lastPurchase->purchase_price;
                } else {
                    // Fallback to variation selling price if no purchase history
                    $purchasePrice = $variation->default_sell_price ?? 0;
                }
            }

            // Calculate purchase price including tax if applicable
            $purchasePriceIncTax = $purchasePrice; // Simplified - can be enhanced with tax calculation

            // Calculate totals
            $totalBeforeTax = $customQty * $purchasePrice;
            $finalTotal = $customQty * $purchasePriceIncTax;

            // Generate reference number
            $refCount = $this->productUtil->setAndGetReferenceCount('purchase');
            $refNo = 'IRCQ' . date('Y') . '/' . str_pad($refCount, 4, '0', STR_PAD_LEFT);

            // Log purchase transaction creation
            Log::info('InventoryReset: Creating purchase for custom qty', [
                'product_id' => $item['product_id'],
                'custom_qty' => $customQty,
                'ref_no' => $refNo
            ]);

            // Create purchase transaction
            // NOTE: StockHistoryComposer will detect this as "Inventory Reset" via IRCQ ref_no pattern
            $purchaseTransaction = Transaction::create([
                'business_id' => $business_id,
                'location_id' => $locationId,
                'type' => 'purchase',
                'status' => 'received',
                'payment_status' => 'paid',
                'transaction_date' => now(),
                'ref_no' => $refNo,
                'total_before_tax' => $totalBeforeTax,
                'final_total' => $finalTotal,
                'opening_stock_product_id' => $item['product_id'],
                'created_by' => Auth::id(),
                'additional_notes' => 'Inventory Reset - Custom Quantity: ' . $resetLog->reason,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Create purchase line
            DB::table('purchase_lines')->insert([
                'transaction_id' => $purchaseTransaction->id,
                'product_id' => $item['product_id'],
                'variation_id' => $item['variation_id'],
                'quantity' => $customQty,
                'purchase_price' => $purchasePrice,
                'purchase_price_inc_tax' => $purchasePriceIncTax,
                'item_tax' => 0, // Simplified
                'tax_id' => null,
                'quantity_sold' => 0,
                'quantity_adjusted' => 0,
                'quantity_returned' => 0,
                'mfg_quantity_used' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Purchase created successfully

            return $purchaseTransaction;
        } catch (\Exception $e) {
            Log::error('InventoryReset: Error creating purchase transaction: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get summary data for AJAX requests
     */
    public function getSummary()
    {
        if (!auth()->user()->can('inventory_reset.view')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $business_id = session()->get('user.business_id');
        $stats = $this->getInventoryStats($business_id);

        return response()->json(['success' => true, 'data' => $stats]);
    }

    /**
     * Get locations for a business (AJAX endpoint)
     */
    public function getLocations($business_id)
    {
        if (!auth()->user()->can('inventory_reset.view')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $locations = \App\BusinessLocation::where('business_id', $business_id)->get();

        return response()->json(['success' => true, 'data' => $locations]);
    }

    /**
     * Get products with negative inventory (AJAX endpoint)
     */
    public function getNegativeInventoryProducts(Request $request)
    {
        if (!auth()->user()->can('inventory_reset.view')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $business_id = session()->get('user.business_id');
        $location_id = $request->get('location_id');

        try {
            // Query for products with negative inventory
            $query = VariationLocationDetails::query()
                ->join('products', 'variation_location_details.product_id', '=', 'products.id')
                ->join('variations', 'variation_location_details.variation_id', '=', 'variations.id')
                ->join('business_locations', 'variation_location_details.location_id', '=', 'business_locations.id')
                ->leftjoin('units', 'products.unit_id', '=', 'units.id')
                ->where('products.business_id', $business_id)
                ->where('products.type', '!=', 'modifier')
                ->where('variation_location_details.qty_available', '<', 0);

            // Filter by location if specified
            if (!empty($location_id)) {
                $query->where('variation_location_details.location_id', $location_id);
            }

            $negativeProducts = $query->select(
                'products.id as product_id',
                'products.name as product_name',
                'products.sku',
                'variations.sub_sku',
                'variation_location_details.qty_available',
                'business_locations.name as location_name',
                'variation_location_details.location_id',
                'units.short_name as unit'
            )
                ->orderBy('products.name')
                ->get();

            // Group by product and sum negative quantities
            $groupedProducts = [];
            foreach ($negativeProducts as $item) {
                $key = $item->product_id;

                if (!isset($groupedProducts[$key])) {
                    $groupedProducts[$key] = [
                        'id' => $item->product_id,
                        'name' => $item->product_name,
                        'sku' => $item->sku,
                        'total_negative_qty' => 0, // Initialize to 0, will sum below
                        'unit' => $item->unit,
                        'locations' => []
                    ];
                }

				// Sum all negative quantities for this product across locations
                $groupedProducts[$key]['total_negative_qty'] += floatval($item->qty_available);

                $groupedProducts[$key]['locations'][] = [
                    'location_name' => $item->location_name
                ];
            }

            // Format the total quantities after summing
            foreach ($groupedProducts as &$product) {
                $product['total_negative_qty'] = $this->productUtil->num_f($product['total_negative_qty'], false, null, true);
            }

            return response()->json([
                'success' => true,
                'products' => array_values($groupedProducts),
                'total_count' => count($groupedProducts)
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching negative inventory products: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => __('inventoryreset::lang.error_fetching_negative_products')
            ], 500);
        }
    }

    /**
     * Search products for inventory reset (AJAX endpoint for Select2)
     */
    public function searchProducts(Request $request)
    {
        if (!auth()->user()->can('inventory_reset.view')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $business_id = session()->get('user.business_id');
        $searchTerm = $request->get('term', '');
        $page = $request->get('page', 1);
        $perPage = 20;
		$location_id = $request->get('location_id'); // Get selected location

        $query = Product::where('business_id', $business_id)
            ->where('type', '!=', 'modifier')
            ->where(function ($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('sku', 'LIKE', "%{$searchTerm}%");
            });

        // If location is selected, only show products that have stock in that location
        if (!empty($location_id)) {
            $query->whereHas('variations.variation_location_details', function ($q) use ($location_id) {
                $q->where('location_id', $location_id);
            });
        }

        $total = $query->count();
        $products = $query->with('unit')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        // Get stock information for each product
        $productsWithStock = $products->map(function ($product) use ($business_id, $location_id) {
            if (!empty($location_id)) {
                // Get stock for specific location only
                $totalStock = VariationLocationDetails::join('product_variations', 'variation_location_details.variation_id', '=', 'product_variations.id')
                    ->where('product_variations.product_id', $product->id)
                    ->where('variation_location_details.location_id', $location_id)
                    ->sum('variation_location_details.qty_available');
            } else {
                // Get total stock across all locations for this product
                $totalStock = VariationLocationDetails::join('product_variations', 'variation_location_details.variation_id', '=', 'product_variations.id')
                    ->join('business_locations', 'variation_location_details.location_id', '=', 'business_locations.id')
                    ->where('product_variations.product_id', $product->id)
                    ->where('business_locations.business_id', $business_id)
                    ->sum('variation_location_details.qty_available');
            }

            return [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'current_stock' => $this->productUtil->num_f($totalStock, false, null, true),
                'unit' => $product->unit ? $product->unit->short_name : ''
            ];
        });

        $hasMore = ($page * $perPage) < $total;

        return response()->json([
            'success' => true,
            'products' => $productsWithStock,
            'pagination' => [
                'more' => $hasMore
            ]
        ]);
    }

    /**
     * Fix stock mismatches before performing inventory reset
     */
    private function fixStockMismatchesBeforeReset($business_id, Request $request)
    {
        // Log::info('Starting stock mismatch fixes', [
        //     'business_id' => $business_id,
        //     'reset_type' => $request->reset_type
        // ]);

        try {
            // Get variation location details that need fixing
            $variationLocationDetails = $this->getVariationLocationDetailsToReset($business_id, $request);

            $fixedCount = 0;
            $errorCount = 0;

            foreach ($variationLocationDetails as $vld) {
                try {
                    // Get the calculated stock using the stock details method
                    $calculatedStock = $this->calculateStockForVariation(
                        $business_id,
                        $vld->variation_id,
                        $vld->location_id
                    );

                    // Check if there's a mismatch between actual and calculated stock
                    if (round($vld->qty_available, 4) != round($calculatedStock, 4)) {
                        // Log::info('Stock mismatch found, fixing', [
                        //     'product_id' => $vld->product_id,
                        //     'variation_id' => $vld->variation_id,
                        //     'location_id' => $vld->location_id,
                        //     'current_qty' => $vld->qty_available,
                        //     'calculated_qty' => $calculatedStock
                        // ]);

                        // Fix the stock mismatch using ProductUtil method
                        $this->productUtil->fixVariationStockMisMatch(
                            $business_id,
                            $vld->variation_id,
                            $vld->location_id,
                            $calculatedStock
                        );

                        $fixedCount++;
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to fix stock mismatch for variation', [
                        'product_id' => $vld->product_id,
                        'variation_id' => $vld->variation_id,
                        'location_id' => $vld->location_id,
                        'error' => $e->getMessage()
                    ]);
                    $errorCount++;
                }
            }

            // Log::info('Stock mismatch fixes completed', [
            //     'business_id' => $business_id,
            //     'fixed_count' => $fixedCount,
            //     'error_count' => $errorCount
            // ]);

            return [
                'fixed_count' => $fixedCount,
                'error_count' => $errorCount
            ];
        } catch (\Exception $e) {
            Log::error('Failed to run stock mismatch fixes', [
                'business_id' => $business_id,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Calculate correct stock quantity for a specific variation at a specific location
     */
    private function calculateStockForVariation($business_id, $variation_id, $location_id)
    {
        // Use the same calculation logic as in ProductUtil->getStockDetails
        $stockData = DB::table('variation_location_details as vld')
            ->leftJoin('business_locations as bl', 'bl.id', '=', 'vld.location_id')
            ->leftJoin('products as p', 'p.id', '=', 'vld.product_id')
            ->leftJoin('variations as v', 'v.id', '=', 'vld.variation_id')
            ->leftJoin('product_variations as pv', 'pv.id', '=', 'v.product_variation_id')
            ->where('vld.variation_id', $variation_id)
            ->where('vld.location_id', $location_id)
            ->where('bl.business_id', $business_id)
            ->select([
                'vld.qty_available as stock',
                DB::raw('COALESCE((
                    SELECT SUM(quantity) FROM purchase_lines pl
                    JOIN transactions t ON t.id = pl.transaction_id
                    WHERE pl.variation_id = vld.variation_id
                    AND t.location_id = vld.location_id
                    AND t.business_id = ' . $business_id . '
                    AND t.type = "purchase"
                    AND t.status = "final"
                ), 0) as total_purchased'),

                DB::raw('COALESCE((
                    SELECT SUM(quantity) FROM transaction_sell_lines tsl
                    JOIN transactions t ON t.id = tsl.transaction_id
                    WHERE tsl.variation_id = vld.variation_id
                    AND t.location_id = vld.location_id
                    AND t.business_id = ' . $business_id . '
                    AND t.type = "sell"
                    AND t.status = "final"
                ), 0) as total_sold'),

                DB::raw('COALESCE((
                    SELECT SUM(quantity) FROM stock_adjustment_lines sal
                    JOIN transactions t ON t.id = sal.transaction_id
                    WHERE sal.variation_id = vld.variation_id
                    AND t.location_id = vld.location_id
                    AND t.business_id = ' . $business_id . '
                    AND t.type = "stock_adjustment"
                    AND t.status = "final"
                ), 0) as total_adjusted'),

                DB::raw('COALESCE((
                    SELECT SUM(quantity) FROM purchase_lines pl
                    JOIN transactions t ON t.id = pl.transaction_id
                    WHERE pl.variation_id = vld.variation_id
                    AND t.location_id = vld.location_id
                    AND t.business_id = ' . $business_id . '
                    AND t.type = "purchase_return"
                    AND t.status = "final"
                ), 0) as total_purchase_return'),

                DB::raw('COALESCE((
                    SELECT SUM(quantity) FROM transaction_sell_lines tsl
                    JOIN transactions t ON t.id = tsl.transaction_id
                    WHERE tsl.variation_id = vld.variation_id
                    AND t.location_id = vld.location_id
                    AND t.business_id = ' . $business_id . '
                    AND t.type = "sell_return"
                    AND t.status = "final"
                ), 0) as total_sell_return'),

                DB::raw('COALESCE((
                    SELECT SUM(quantity) FROM purchase_lines pl
                    JOIN transactions t ON t.id = pl.transaction_id
                    WHERE pl.variation_id = vld.variation_id
                    AND t.location_id = vld.location_id
                    AND t.business_id = ' . $business_id . '
                    AND t.type = "opening_stock"
                    AND t.status = "final"
                ), 0) as total_opening_stock')
            ])
            ->first();

        if (!$stockData) {
            return 0;
        }

        // Calculate stock using the same formula as ProductUtil
        $total_opening_stock = $stockData->total_opening_stock ?: 0;
        $total_purchased = $stockData->total_purchased ?: 0;
        $total_sell_return = $stockData->total_sell_return ?: 0;
        $total_sold = $stockData->total_sold ?: 0;
        $total_adjusted = $stockData->total_adjusted ?: 0;
        $total_purchase_return = $stockData->total_purchase_return ?: 0;

        $calculated_stock = $total_opening_stock + $total_purchased + $total_sell_return
            - ($total_sold + $total_adjusted + $total_purchase_return);

        return $calculated_stock;
    }

    /**
     * Get count of variation location details for batch processing
     */
    private function getVariationLocationDetailsCount($business_id, Request $request, $resetType, $resetMode)
    {
        $query = VariationLocationDetails::query();

        // Add business filtering through join
        $query->join('products', 'variation_location_details.product_id', '=', 'products.id')
            ->where('products.business_id', $business_id)
            ->where('products.type', '!=', 'modifier');

        // Add location filtering if specified
        if (!empty($request->location_id)) {
            $query->where('variation_location_details.location_id', $request->location_id);
        }

        // Add reset type filtering
        if ($resetType === 'selected_products') {
            $query->whereIn('variation_location_details.product_id', $request->product_ids);
        }

        // Add reset mode filtering
        switch ($resetMode) {
            case 'positive_only':
                $query->where('variation_location_details.qty_available', '>', 0);
                break;
            case 'negative_only':
                $query->where('variation_location_details.qty_available', '<', 0);
                break;
            case 'zero_only':
                $query->where('variation_location_details.qty_available', '=', 0);
                break;
        }

        return $query->count();
    }

    /**
     * Get batch of variation location details for processing
     */
    private function getVariationLocationDetailsBatch($business_id, Request $request, $resetType, $resetMode, $offset, $limit)
    {
        $query = VariationLocationDetails::query();

        // Add business filtering through join
        $query->join('products', 'variation_location_details.product_id', '=', 'products.id')
            ->where('products.business_id', $business_id)
            ->where('products.type', '!=', 'modifier')
            ->select('variation_location_details.*');

        // Add location filtering if specified
        if (!empty($request->location_id)) {
            $query->where('variation_location_details.location_id', $request->location_id);
        }

        // Add reset type filtering
        if ($resetType === 'selected_products') {
            $query->whereIn('variation_location_details.product_id', $request->product_ids);
        }

        // Add reset mode filtering
        switch ($resetMode) {
            case 'positive_only':
                $query->where('variation_location_details.qty_available', '>', 0);
                break;
            case 'negative_only':
                $query->where('variation_location_details.qty_available', '<', 0);
                break;
            case 'zero_only':
                $query->where('variation_location_details.qty_available', '=', 0);
                break;
        }

        return $query->orderBy('variation_location_details.id')
            ->skip($offset)
            ->take($limit)
            ->get();
    }
}