<?php

namespace Modules\ExchangeItem\Http\Controllers;

use App\Account;
use App\Product;
use App\TaxRate;
use App\Business;
use Carbon\Carbon;
use App\Transaction;
use App\BusinessLocation;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\TransactionPayment;
use App\Utils\BusinessUtil;
use App\TransactionSellLine;
use Illuminate\Http\Request;
use App\Utils\TransactionUtil;
use App\User;
use App\CashRegister;
use App\CashRegisterTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Modules\ExchangeItem\Entities\TransactionExchange;
use Modules\ExchangeItem\Entities\TransactionExchangeLine;
use Illuminate\Routing\Controller;
use App\Utils\CashRegisterUtil;

class ExchangeController extends Controller
{
    protected $businessUtil;
    protected $transactionUtil;
    protected $productUtil;
    protected $cashRegisterUtil;

    public function __construct(BusinessUtil $businessUtil, TransactionUtil $transactionUtil, ProductUtil $pUtil, CashRegisterUtil $cashRegisterUtil)
    {
        $this->businessUtil = $businessUtil;
        $this->transactionUtil = $transactionUtil;
        $this->productUtil = $pUtil;
        $this->cashRegisterUtil = $cashRegisterUtil;
    }

    /**
     * Display exchange index page
     */
    public function index()
    {
        if (!auth()->user()->can('exchange.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $business_locations = BusinessLocation::forDropdown($business_id, false, false);
        $users = TransactionExchange::getUsersForDropdown($business_id);

        return view('exchangeitem::index', compact('business_locations', 'users'));
    }

    /**
     * Show exchange creation form
     */
    public function create()
    {
        if (!auth()->user()->can('exchange.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $business_details = $this->businessUtil->getDetails($business_id);
        $business_locations = BusinessLocation::forDropdown($business_id, false, false);
        $default_location = null;

        if (count($business_locations) == 1) {
            foreach ($business_locations as $id => $name) {
                $default_location = BusinessLocation::findOrFail($id);
            }
        }

        $payment_types = $this->transactionUtil->payment_types(null, true, $business_id);
        $accounts = [];
        if ($this->businessUtil->isModuleEnabled('account')) {
            $accounts = Account::forDropdown($business_id, true, false, true);
        }

        return view('exchangeitem::create', compact(
            'business_details',
            'business_locations',
            'default_location',
            'payment_types',
            'accounts'
        ));
    }

    /**
     * Search for original transaction by invoice number
     */
    public function searchTransaction(Request $request)
    {
        if (!auth()->user()->can('exchange.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $invoice_no = $request->get('invoice_no');

        $transaction = Transaction::with([
            'sell_lines' => function ($query) {
                $query->where('quantity_returned', '<', DB::raw('quantity'))
                      ->select(['id', 'transaction_id', 'product_id', 'variation_id', 'quantity',
                               'unit_price', 'unit_price_inc_tax', 'unit_price_before_discount',
                               'line_discount_type', 'line_discount_amount', 'quantity_returned',
                               'sell_line_note', 'sub_unit_id', 'item_tax', 'tax_id']);
            },
            'sell_lines.product',
            'sell_lines.variations',
            'sell_lines.variations.product_variation',
            'sell_lines.product.unit',
            'contact'
        ])
            ->where('business_id', $business_id)
            ->where('type', 'sell')
            ->where('status', 'final')
            ->where('invoice_no', $invoice_no)
            ->first();

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => __('exchangeitem::lang.transaction_not_found')
            ]);
        }

        $exchangeable_lines = $transaction->sell_lines->filter(function ($line) {
            return ($line->quantity - $line->quantity_returned) > 0;
        });

        if ($exchangeable_lines->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => __('exchangeitem::lang.no_items_available_for_exchange')
            ]);
        }

        return response()->json([
            'success' => true,
            'transaction' => $transaction,
            'exchangeable_lines' => $exchangeable_lines->values()
        ]);
    }

    /**
     * Process the exchange - FIXED VERSION WITH PROPER STOCK AND CASH REGISTER HANDLING
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('exchange.create')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'original_transaction_id' => 'required|exists:transactions,id',
            'location_id' => 'required|exists:business_locations,id',
            'exchange_lines' => 'required|array|min:1',
            'exchange_lines.*.original_sell_line_id' => 'required|exists:transaction_sell_lines,id',
            'exchange_lines.*.original_quantity' => 'required|numeric|min:0.01',
            'payment_method' => 'sometimes|string',
        ]);

        try {
            DB::beginTransaction();

            $business_id = request()->session()->get('user.business_id');
            $user_id = request()->session()->get('user.id');
            $original_transaction = Transaction::findOrFail($request->original_transaction_id);
            $exchange_ref_no = TransactionExchange::generateExchangeRefNo($business_id);
            $now = new \DateTime();
            $timestamp_db = $now->format('Y-m-d H:i:s');

            \Log::info('Exchange process started', [
                'timestamp' => $timestamp_db,
                'exchange_ref' => $exchange_ref_no
            ]);

            // Calculate amounts and prepare data
            $original_amount = 0;
            $new_amount = 0;
            $exchange_lines_data = [];
            $new_sell_lines = [];
            $stock_adjustments = []; // Track new items sold (for reversal on cancel)
            $return_sell_lines = []; // Track returned items for sell_return transaction

            foreach ($request->exchange_lines as $line_data) {
                $original_sell_line = TransactionSellLine::findOrFail($line_data['original_sell_line_id']);

                // Validate quantity
                $available_qty = $original_sell_line->quantity - $original_sell_line->quantity_returned;
                if ($line_data['original_quantity'] > $available_qty) {
                    throw new \Exception("Insufficient quantity available for exchange");
                }

                $line_original_amount = round($line_data['original_quantity'] * $original_sell_line->unit_price_inc_tax, 4);
                $line_new_amount = 0;

                // If new item is selected for exchange
                if (!empty($line_data['new_product_id']) && !empty($line_data['new_variation_id'])) {
                    $new_quantity = $line_data['new_quantity'] ?? $line_data['original_quantity'];
                    $new_unit_price = $line_data['new_unit_price'];
                    $line_new_amount = round($new_quantity * $new_unit_price, 4);

                    // Track new items sold in exchange (needed for cancellation reversal)
                    $stock_adjustments[] = [
                        'type' => 'sell',
                        'location_id' => $request->location_id,
                        'product_id' => $line_data['new_product_id'],
                        'variation_id' => $line_data['new_variation_id'],
                        'quantity' => $new_quantity
                    ];

                    $product = Product::find($line_data['new_product_id']);
                    $unit_id = $product ? $product->unit_id : null;

                    $new_sell_lines[] = [
                        'product_id' => $line_data['new_product_id'],
                        'variation_id' => $line_data['new_variation_id'],
                        'quantity' => $new_quantity,
                        'unit_price' => $new_unit_price,
                        'unit_price_inc_tax' => $new_unit_price,
                        'unit_price_before_discount' => $new_unit_price,
                        'line_discount_type' => 'fixed',
                        'line_discount_amount' => 0.0000,
                        'sell_line_note' => 'Exchange item',
                        'sub_unit_id' => $unit_id,
                        'exchange_parent_line_id' => $original_sell_line->id,
                        'is_exchange_return' => 0,
                        'item_tax' => 0.0000,
                        'secondary_unit_quantity' => 0.0000,
                        'quantity_returned' => 0.0000,
                        'mfg_waste_percent' => 0.0000,
                        'so_quantity_invoiced' => 0.0000,
                    ];
                }

                $original_amount += $line_original_amount;
                $new_amount += $line_new_amount;
                $price_difference = round($line_new_amount - $line_original_amount, 4);

                $exchange_lines_data[] = [
                    'original_sell_line_id' => $original_sell_line->id,
                    'new_sell_line_id' => null,
                    'exchange_type' => !empty($line_data['new_product_id']) ? 'exchange_with_new' : 'return_only',
                    'original_quantity' => round($line_data['original_quantity'], 4),
                    'original_unit_price' => round($original_sell_line->unit_price_inc_tax, 4),
                    'new_quantity' => round($line_data['new_quantity'] ?? 0, 4),
                    'new_unit_price' => round($line_data['new_unit_price'] ?? 0, 4),
                    'price_difference' => $price_difference,
                ];

                // FIXED: Update original sell line quantity returned and update stock
                // Following the same approach as SellReturn (TransactionUtil::addSellReturn)
                $quantity_before = $original_sell_line->quantity_returned;
                $original_sell_line->quantity_returned += $line_data['original_quantity'];
                $original_sell_line->save();

                // Update stock for returned item - using the CORRECT approach
                // This matches how SellReturn handles stock in TransactionUtil.php:6224
                $this->productUtil->updateProductQuantity(
                    $request->location_id,
                    $original_sell_line->product_id,
                    $original_sell_line->variation_id,
                    $original_sell_line->quantity_returned,  // NEW total quantity_returned
                    $quantity_before,                         // OLD quantity_returned (before this exchange)
                    null,
                    false
                );

                // ISSUE 1 FIX: Update qty_returned in transaction_sell_lines_purchase_lines
                // This matches how SellReturn handles it in TransactionUtil.php:6221
                $this->transactionUtil->updateQuantitySoldFromSellLine(
                    $original_sell_line,
                    $original_sell_line->quantity_returned,  // NEW total quantity_returned
                    $quantity_before,                         // OLD quantity_returned
                    false
                );

                // Collect returned item data for sell_return transaction
                $return_sell_lines[] = [
                    'product_id' => $original_sell_line->product_id,
                    'variation_id' => $original_sell_line->variation_id,
                    'quantity' => $line_data['original_quantity'],
                    'unit_price_inc_tax' => $original_sell_line->unit_price_inc_tax,
                    'parent_sell_line_id' => $original_sell_line->id,
                ];
            }

            $exchange_difference = $new_amount - $original_amount;

            // FIXED: Apply stock adjustments for NEW items sold in exchange
            // Process only the 'sell' type adjustments (new items being exchanged)
            foreach ($stock_adjustments as $adjustment) {
                if ($adjustment['type'] == 'sell') {
                    // Reduce stock for new items sold in exchange
                    // Use decreaseProductQuantity approach for consistency with regular sales
                    $this->productUtil->decreaseProductQuantity(
                        $adjustment['product_id'],
                        $adjustment['variation_id'],
                        $adjustment['location_id'],
                        $adjustment['quantity'],  // New quantity being sold
                        0                         // Old quantity (always 0 for new exchange items)
                    );
                }
                // Note: We removed the 'return' type processing from here since we now
                // handle returned items directly in the loop above using updateProductQuantity
            }

            // CRITICAL FIX: Create sell_return transaction for returned items
            // This ensures proper financial recording and reporting
            $return_transaction = null;
            if (!empty($return_sell_lines)) {
                \Log::info('Creating sell_return transaction for exchanged items');

                $return_invoice_no = $this->transactionUtil->getInvoiceNumber($business_id, 'draft', $request->location_id);

                $return_transaction = Transaction::create([
                    'business_id' => $business_id,
                    'location_id' => $request->location_id,
                    'type' => 'sell_return',
                    'status' => 'final',
                    'contact_id' => $original_transaction->contact_id,
                    'transaction_date' => $timestamp_db,
                    'invoice_no' => $return_invoice_no,
                    'return_parent_id' => $original_transaction->id,
                    'created_by' => $user_id,
                    'total_before_tax' => $original_amount,
                    'tax_amount' => 0,
                    'final_total' => $original_amount,
                    'additional_notes' => "Exchange Return | Ref: {$exchange_ref_no} | Original Invoice: {$original_transaction->invoice_no}",
                ]);

                // Create return sell lines
                foreach ($return_sell_lines as $return_line) {
                    TransactionSellLine::create([
                        'transaction_id' => $return_transaction->id,
                        'product_id' => $return_line['product_id'],
                        'variation_id' => $return_line['variation_id'],
                        'quantity' => $return_line['quantity'],
                        'unit_price' => $return_line['unit_price_inc_tax'],
                        'unit_price_inc_tax' => $return_line['unit_price_inc_tax'],
                        'unit_price_before_discount' => $return_line['unit_price_inc_tax'],
                        'line_discount_type' => 'fixed',
                        'line_discount_amount' => 0,
                        'item_tax' => 0,
                        'parent_sell_line_id' => $return_line['parent_sell_line_id'],
                    ]);
                }

                // Create return payment transaction to balance the books
                $payment_method = $request->payment_method ?? 'cash';
                $payment_account_id = $request->payment_account ?? null;

                // Generate payment reference for return
                $ref_count = $this->transactionUtil->setAndGetReferenceCount('sell_payment', $business_id);
                $return_payment_ref = $this->transactionUtil->generateReferenceNumber('sell_payment', $ref_count, $business_id);

                $return_payment = \App\TransactionPayment::create([
                    'transaction_id' => $return_transaction->id,
                    'business_id' => $business_id,
                    'amount' => $original_amount,
                    'method' => 'advance',  // Changed to 'advance' so it appears in advance payment reports
                    'paid_on' => $timestamp_db,
                    'created_by' => $user_id,
                    'payment_ref_no' => $return_payment_ref,
                    'note' => "Return payment for exchange {$exchange_ref_no}",
                    'is_return' => 0,  // FIXED: Set to 0 so payment counts positively for sell_return payment status
                    'payment_for' => $original_transaction->contact_id,
                    'account_id' => $payment_account_id,
                ]);

                // ISSUE 5 FIX: Create account_transaction entry for return (debit - 120 in example)
                // This creates a debit entry representing money owed to customer for returned items
                if ($payment_account_id) {
                    $return_account_transaction = \App\AccountTransaction::create([
                        'account_id' => $payment_account_id,
                        'type' => 'debit',  // Debit = money going out (owed to customer)
                        'sub_type' => 'deposit',
                        'amount' => $original_amount,
                        'reff_no' => $return_payment_ref,
                        'operation_date' => $timestamp_db,
                        'created_by' => $user_id,
                        'transaction_id' => $return_transaction->id,
                        'transaction_payment_id' => $return_payment->id,
                        'note' => "Exchange return - returned items value | Ref: {$exchange_ref_no}",
                    ]);

                    \Log::info('Account transaction created for return (debit)', [
                        'account_transaction_id' => $return_account_transaction->id,
                        'amount' => $original_amount,
                        'type' => 'debit'
                    ]);
                }

                // ISSUE 2 FIX: Update payment status for sell_return transaction
                // This matches how SellReturn handles it in TransactionUtil.php:6198
                $this->transactionUtil->updatePaymentStatus($return_transaction->id, $return_transaction->final_total);

                \Log::info('Sell return transaction created', ['return_transaction_id' => $return_transaction->id]);
            }

            // Create exchange transaction if there are new items
            $exchange_transaction = null;
            if (!empty($new_sell_lines)) {
                \Log::info('Creating exchange transaction');

                // Generate exchange information for additional_notes
                $exchange_note = sprintf(
                    'Exchange Transaction | Ref: %s | Original Invoice: %s | Exchange Date: %s | Original Amount: %s | New Amount: %s | Difference: %s%s',
                    $exchange_ref_no,
                    $original_transaction->invoice_no,
                    date('Y-m-d H:i:s'),
                    number_format($original_amount, 2),
                    number_format($new_amount, 2),
                    $exchange_difference > 0 ? '+' : '',
                    number_format($exchange_difference, 2)
                );

                $exchange_transaction_data = [
                    'business_id' => $business_id,
                    'location_id' => $request->location_id,
                    'type' => 'sell',
                    'status' => 'final',
                    'contact_id' => $original_transaction->contact_id,
                    'transaction_date' => $timestamp_db,
                    'invoice_no' => $this->transactionUtil->getInvoiceNumber($business_id, 'final', $request->location_id),
                    'is_exchange' => 1,
                    'exchange_parent_id' => $original_transaction->id,
                    'created_by' => $user_id,
                    'total_before_tax' => $new_amount,
                    'tax_amount' => 0,
                    'final_total' => $new_amount,
                    'additional_notes' => $exchange_note,
                ];

                $exchange_transaction = Transaction::create($exchange_transaction_data);

                // Create new sell lines
                foreach ($new_sell_lines as $index => $new_sell_line_data) {
                    $new_sell_line_data['transaction_id'] = $exchange_transaction->id;
                    $new_sell_line = TransactionSellLine::create($new_sell_line_data);

                    if (isset($exchange_lines_data[$index])) {
                        $exchange_lines_data[$index]['new_sell_line_id'] = $new_sell_line->id;
                    }
                }
            }

            // Create exchange record
            $exchange_data = [
                'business_id' => $business_id,
                'location_id' => $request->location_id,
                'original_transaction_id' => $original_transaction->id,
                'exchange_transaction_id' => $exchange_transaction ? $exchange_transaction->id : null,
                'return_transaction_id' => $return_transaction ? $return_transaction->id : null,
                'exchange_ref_no' => $exchange_ref_no,
                'exchange_date' => $timestamp_db,
                'original_amount' => $original_amount,
                'new_amount' => $new_amount,
                'exchange_difference' => $exchange_difference,
                'payment_received' => $exchange_difference > 0 ? $exchange_difference : 0,
                'refund_given' => $exchange_difference < 0 ? abs($exchange_difference) : 0,
                'total_exchange_amount' => $exchange_difference,
                'status' => 'completed',
                'created_by' => $user_id,
                'notes' => $request->notes ?? '',
                'stock_adjustments' => json_encode($stock_adjustments), // Store for reversal
            ];

            $exchange = TransactionExchange::create($exchange_data);

            // Create exchange lines
            foreach ($exchange_lines_data as $line_data) {
                $line_data['exchange_id'] = $exchange->id;
                TransactionExchangeLine::create($line_data);
            }

            // FIXED: Handle payments with proper cash register integration
            if ($exchange_transaction) {
                $this->handleExchangePayments(
                    $exchange_transaction,
                    $exchange_difference,
                    $original_amount,
                    $new_amount,
                    $request,
                    $business_id,
                    $user_id,
                    $exchange_ref_no,
                    $original_transaction
                );
            }

            DB::commit();

            $invoice_url = null;
            if ($exchange_transaction) {
                $invoice_url = action([\App\Http\Controllers\SellPosController::class, 'printInvoice'], [$exchange_transaction->id]);
            }

            \Log::info('Exchange completed successfully', [
                'exchange_id' => $exchange->id,
                'exchange_ref' => $exchange_ref_no
            ]);

            return response()->json([
                'success' => true,
                'message' => __('exchangeitem::lang.exchange_completed_successfully'),
                'exchange_id' => $exchange->id,
                'invoice_url' => $invoice_url
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            \Log::emergency("Exchange Error - File:" . $e->getFile() . " Line:" . $e->getLine() . " Message:" . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * FIXED: Handle exchange payments with proper cash register and account integration
     * This method handles all payment scenarios for exchanges and ensures proper cash register and account entries
     *
     * ISSUE 5 FIX: Two-entry approach for proper accounting
     * - Entry 1: New sale payment (90) - creates credit in account_transactions
     * - Entry 2: Return payment (120) - already created above, creates debit in account_transactions
     * - Net effect: 120 debit - 90 credit = 30 debit (refund to customer)
     */
    private function handleExchangePayments($exchange_transaction, $exchange_difference, $original_amount, $new_amount, $request, $business_id, $user_id, $exchange_ref_no, $original_transaction)
    {
        try {
            $payment_method = $request->payment_method ?? 'cash';
            $current_date = date('Y-m-d H:i:s');

            \Log::info('Processing exchange payments', [
                'exchange_difference' => $exchange_difference,
                'original_amount' => $original_amount,
                'new_amount' => $new_amount,
                'payment_method' => $payment_method
            ]);

            // Prepare payment account details if provided
            $payment_account_id = $request->payment_account ?? null;

            // ISSUE 5 FIX: Create payment for the NEW SALE amount (credit entry - 90 in example)
            // This represents the value of new items being purchased
            if ($new_amount > 0) {
                $new_sale_payment = new \App\TransactionPayment();
                $new_sale_payment->transaction_id = $exchange_transaction->id;
                $new_sale_payment->business_id = $business_id;
                $new_sale_payment->amount = $new_amount;
                $new_sale_payment->method = 'advance';  // Changed to 'advance' so it appears in advance payment reports
                $new_sale_payment->paid_on = $current_date;
                $new_sale_payment->created_by = $user_id;
                $new_sale_payment->is_return = 0;
                $new_sale_payment->paid_through_link = 0;
                $new_sale_payment->is_advance = 0;
                $new_sale_payment->payment_for = $exchange_transaction->contact_id ?? 1;
                $new_sale_payment->note = "Exchange new item payment | Ref: {$exchange_ref_no}";
                $new_sale_payment->account_id = $payment_account_id;

                // Add optional payment details including account
                $this->addPaymentDetails($new_sale_payment, $request, $payment_method);

                // Generate payment reference
                $ref_count = $this->transactionUtil->setAndGetReferenceCount('sell_payment', $business_id);
                $new_sale_payment->payment_ref_no = $this->transactionUtil->generateReferenceNumber('sell_payment', $ref_count, $business_id);
                $new_sale_payment->save();

                // ISSUE 5 FIX: Create account_transaction entry for new sale (credit - 90 in example)
                // This creates a credit entry representing money received for new items
                if ($payment_account_id) {
                    $sale_account_transaction = \App\AccountTransaction::create([
                        'account_id' => $payment_account_id,
                        'type' => 'credit',  // Credit = money coming in (from new sale)
                        'sub_type' => 'deposit',
                        'amount' => $new_amount,
                        'reff_no' => $new_sale_payment->payment_ref_no,
                        'operation_date' => $current_date,
                        'created_by' => $user_id,
                        'transaction_id' => $exchange_transaction->id,
                        'transaction_payment_id' => $new_sale_payment->id,
                        'note' => "Exchange sale - new items value | Ref: {$exchange_ref_no}",
                    ]);

                    \Log::info('Account transaction created for new sale (credit)', [
                        'account_transaction_id' => $sale_account_transaction->id,
                        'amount' => $new_amount,
                        'type' => 'credit'
                    ]);
                }

                \Log::info('New sale payment created', [
                    'amount' => $new_amount,
                    'payment_id' => $new_sale_payment->id
                ]);
            }

            // Handle cash register entries based on actual cash movement
            if ($exchange_difference > 0) {
                // SCENARIO 1: Customer pays extra money (new items cost more than returned items)
                // Example: Return 100 item + Buy 150 item = Customer pays 50 extra
                \Log::info('Customer pays extra: ' . $exchange_difference);

                // Cash register entry - record net cash received
                if ($payment_method === 'cash') {
                    $this->addToCashRegister($exchange_transaction, $exchange_difference, 'credit', $payment_method, 'sell');
                }

                // ISSUE 5 FIX: Create payment record for additional payment from customer
                $extra_ref_count = $this->transactionUtil->setAndGetReferenceCount('sell_payment', $business_id);
                $extra_payment_ref = $this->transactionUtil->generateReferenceNumber('sell_payment', $extra_ref_count, $business_id);

                $extra_payment = \App\TransactionPayment::create([
                    'transaction_id' => $exchange_transaction->id,
                    'business_id' => $business_id,
                    'amount' => $exchange_difference,
                    'method' => $payment_method,  // Use actual payment method for the extra amount
                    'paid_on' => $current_date,
                    'created_by' => $user_id,
                    'payment_ref_no' => $extra_payment_ref,
                    'note' => "Exchange additional payment from customer | Ref: {$exchange_ref_no}",
                    'is_return' => 0,
                    'payment_for' => $exchange_transaction->contact_id ?? 1,
                    'account_id' => $payment_account_id,
                ]);

                // Create account_transaction entry for extra payment (credit)
                if ($payment_account_id) {
                    $extra_account_transaction = \App\AccountTransaction::create([
                        'account_id' => $payment_account_id,
                        'type' => 'credit',  // Credit = money coming in (from customer)
                        'sub_type' => 'deposit',
                        'amount' => $exchange_difference,
                        'reff_no' => $extra_payment_ref,
                        'operation_date' => $current_date,
                        'created_by' => $user_id,
                        'transaction_id' => $exchange_transaction->id,
                        'transaction_payment_id' => $extra_payment->id,
                        'note' => "Exchange extra payment - difference paid by customer | Ref: {$exchange_ref_no}",
                    ]);

                    \Log::info('Account transaction created for extra payment (credit)', [
                        'account_transaction_id' => $extra_account_transaction->id,
                        'amount' => $exchange_difference,
                        'type' => 'credit'
                    ]);
                }

            } elseif ($exchange_difference < 0) {
                // SCENARIO 2: Customer gets refund (returned items cost more than new items)
                // Example: Return 120 item + Buy 90 item = Customer gets 30 refund
                $refund_amount = abs($exchange_difference);
                \Log::info('Customer gets refund: ' . $refund_amount);

                // Cash register entry - record net cash paid out
                if ($payment_method === 'cash') {
                    $this->addToCashRegister($exchange_transaction, $refund_amount, 'debit', $payment_method, 'refund');
                }

                // FIXED: Only create account_transaction entry for refund (NOT transaction_payments)
                // The transaction_payments table should only have:
                // 1. Return payment (120) - on return_transaction
                // 2. New sale payment (90) - on exchange_transaction
                // The refund (30) should only appear in account_transactions
                if ($payment_account_id) {
                    $refund_ref_count = $this->transactionUtil->setAndGetReferenceCount('sell_payment', $business_id);
                    $refund_payment_ref = $this->transactionUtil->generateReferenceNumber('sell_payment', $refund_ref_count, $business_id);

                    $refund_account_transaction = \App\AccountTransaction::create([
                        'account_id' => $payment_account_id,
                        'type' => 'credit',  // Credit = money being refunded (credit to customer)
                        'sub_type' => 'deposit',
                        'amount' => $refund_amount,
                        'reff_no' => $refund_payment_ref,
                        'operation_date' => $current_date,
                        'created_by' => $user_id,
                        'transaction_id' => $exchange_transaction->id,
                        'transaction_payment_id' => null,  // No transaction_payment for refund
                        'note' => "Exchange refund - cash returned to customer | Ref: {$exchange_ref_no}",
                    ]);

                    \Log::info('Account transaction created for refund (credit)', [
                        'account_transaction_id' => $refund_account_transaction->id,
                        'amount' => $refund_amount,
                        'type' => 'credit'
                    ]);
                }

            } else {
                // SCENARIO 3: Even exchange (same value)
                // Example: Return 100 item + Buy 100 item = No money exchange
                \Log::info('Even exchange - no cash difference');
            }

            // Update payment status
            $exchange_transaction->payment_status = 'paid';
            $exchange_transaction->save();

            // Add exchange reference to original transaction's additional_notes
            $original_note = sprintf(
                'Items exchanged | Exchange Ref: %s | Date: %s | New Invoice: %s | Amount Difference: %s%s',
                $exchange_ref_no,
                date('Y-m-d H:i:s'),
                $exchange_transaction->invoice_no,
                $exchange_difference > 0 ? '+' : '',
                number_format($exchange_difference, 2)
            );

            // Append to existing additional_notes or create new
            $existing_notes = $original_transaction->additional_notes;
            $updated_notes = $existing_notes ? $existing_notes . ' | ' . $original_note : $original_note;

            $original_transaction->additional_notes = $updated_notes;
            $original_transaction->save();

            \Log::info('Exchange payments processed successfully', [
                'transaction_id' => $exchange_transaction->id,
                'payment_method' => $payment_method,
                'payment_account_id' => $payment_account_id,
                'original_amount' => $original_amount,
                'new_amount' => $new_amount,
                'difference' => $exchange_difference
            ]);
        } catch (\Exception $e) {
            \Log::error('Exchange payment error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            throw $e;
        }
    }

    /**
     * Add payment details to payment record
     */
    private function addPaymentDetails($payment, $request, $payment_method)
    {
        if ($request->payment_account) {
            $payment->account_id = $request->payment_account;
        }
        if ($request->card_number) {
            $payment->card_number = $request->card_number;
        }
        if ($request->card_holder_name) {
            $payment->card_holder_name = $request->card_holder_name;
        }
        if ($payment_method === 'card') {
            $payment->card_type = $request->card_type ?? 'credit';
        }
        if ($request->cheque_number) {
            $payment->cheque_number = $request->cheque_number;
        }
        if ($request->bank_name) {
            $payment->bank_account_number = $request->bank_name;
        }
        if ($request->transaction_no) {
            $payment->transaction_no = $request->transaction_no;
        }
    }

    /**
     * FIXED: Add proper cash register entries with correct operation types
     */
    private function addToCashRegister($transaction, $amount, $type, $payment_method, $operation_type = 'exchange')
    {
        try {
            // Get the current open cash register
            $cash_register = \App\CashRegister::where('user_id', auth()->user()->id)
                ->where('status', 'open')
                ->first();

            if ($cash_register) {
                $cash_register_transaction = new \App\CashRegisterTransaction();
                $cash_register_transaction->cash_register_id = $cash_register->id;
                $cash_register_transaction->transaction_id = $transaction->id;
                $cash_register_transaction->type = $type; // 'credit' or 'debit'
                $cash_register_transaction->amount = $amount;
                $cash_register_transaction->pay_method = $payment_method;

                // Set transaction_type based on operation for cash register reporting
                if ($operation_type == 'sale' || $operation_type == 'sell') {
                    $cash_register_transaction->transaction_type = 'sell'; // This will show in sales
                } elseif ($operation_type == 'refund') {
                    $cash_register_transaction->transaction_type = 'refund'; // This will show in refunds
                } else {
                    $cash_register_transaction->transaction_type = 'exchange';
                }

                $cash_register_transaction->created_at = now();
                $cash_register_transaction->updated_at = now();
                $cash_register_transaction->save();

                \Log::info('Added to cash register', [
                    'register_id' => $cash_register->id,
                    'transaction_id' => $transaction->id,
                    'amount' => $amount,
                    'type' => $type,
                    'method' => $payment_method,
                    'operation' => $operation_type,
                    'transaction_type' => $cash_register_transaction->transaction_type
                ]);
            } else {
                \Log::warning('No open cash register found for user: ' . auth()->user()->id);
            }
        } catch (\Exception $e) {
            \Log::error('Cash register error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
        }
    }

    /**
     * FIXED: Cancel exchange with proper stock reversal
     */
    public function cancel($id)
    {
        if (!auth()->user()->can('exchange.cancel')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::beginTransaction();

            $business_id = request()->session()->get('user.business_id');
            $exchange = TransactionExchange::with(['exchangeLines'])->where('business_id', $business_id)->findOrFail($id);

            if ($exchange->status == 'cancelled') {
                return response()->json([
                    'success' => false,
                    'message' => __('exchangeitem::lang.exchange_already_cancelled')
                ]);
            }

            // FIXED: Reverse stock adjustments using the CORRECT approach
            // This matches how SellReturn reversal works (SellReturnController::destroy line 530-543)

            // Step 1: Restore original sell line quantities and reverse returned item stock
            foreach ($exchange->exchangeLines as $exchange_line) {
                $original_sell_line = TransactionSellLine::find($exchange_line->original_sell_line_id);
                if ($original_sell_line) {
                    // Store the quantity before cancellation
                    $quantity_before = $original_sell_line->quantity_returned;

                    // Restore the quantity_returned to what it was before the exchange
                    $original_sell_line->quantity_returned -= $exchange_line->original_quantity;
                    $original_sell_line->save();

                    // Reverse the stock adjustment for returned items
                    // This removes the stock that was added back during the exchange
                    $this->productUtil->updateProductQuantity(
                        $exchange->location_id,
                        $original_sell_line->product_id,
                        $original_sell_line->variation_id,
                        $original_sell_line->quantity_returned,  // NEW quantity_returned (after reversal)
                        $quantity_before,                         // OLD quantity_returned (before reversal)
                        null,
                        false
                    );
                }
            }

            // Step 2: Reverse stock for new items that were sold in the exchange
            if ($exchange->stock_adjustments) {
                $stock_adjustments = json_decode($exchange->stock_adjustments, true);
                foreach ($stock_adjustments as $adjustment) {
                    if ($adjustment['type'] == 'sell') {
                        // Reverse the sale: Add stock back for items that were sold in exchange
                        // Using decreaseProductQuantity with negative difference restores stock
                        $this->productUtil->decreaseProductQuantity(
                            $adjustment['product_id'],
                            $adjustment['variation_id'],
                            $adjustment['location_id'],
                            0,                          // New quantity (0 - cancelling the sale)
                            $adjustment['quantity']     // Old quantity (what was sold in exchange)
                        );
                    }
                }
            }

            // Remove cash register entries
            if ($exchange->exchange_transaction_id) {
                \App\CashRegisterTransaction::where('transaction_id', $exchange->exchange_transaction_id)->delete();
            }

            // Update exchange status
            $exchange->status = 'cancelled';
            $exchange->cancelled_at = now();
            $exchange->cancelled_by = auth()->user()->id;
            $exchange->save();

            // Update original transaction's additional_notes to reflect cancellation
            $original_transaction = Transaction::find($exchange->original_transaction_id);
            if ($original_transaction) {
                $cancellation_note = sprintf(
                    'Exchange cancelled | Ref: %s | Cancelled on: %s',
                    $exchange->exchange_ref_no,
                    now()->format('Y-m-d H:i:s')
                );

                $existing_notes = $original_transaction->additional_notes;
                $updated_notes = $existing_notes ? $existing_notes . ' | ' . $cancellation_note : $cancellation_note;

                $original_transaction->additional_notes = $updated_notes;
                $original_transaction->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('exchangeitem::lang.exchange_cancelled_successfully')
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            \Log::emergency("Exchange cancel error: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * FIXED: Delete exchange with proper cleanup
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('exchange.delete')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::beginTransaction();

            $business_id = request()->session()->get('user.business_id');
            $exchange = TransactionExchange::where('business_id', $business_id)->findOrFail($id);

            // Only allow deletion of cancelled exchanges
            if ($exchange->status !== 'cancelled') {
                return response()->json([
                    'success' => false,
                    'message' => 'Can only delete cancelled exchanges. Please cancel the exchange first.'
                ]);
            }

            // Delete exchange lines
            TransactionExchangeLine::where('exchange_id', $exchange->id)->delete();

            // Clean up exchange transaction if exists
            if ($exchange->exchange_transaction_id) {
                $exchange_transaction = Transaction::find($exchange->exchange_transaction_id);
                if ($exchange_transaction) {
                    // Remove cash register entries
                    \App\CashRegisterTransaction::where('transaction_id', $exchange->exchange_transaction_id)->delete();

                    // Delete payments
                    \App\TransactionPayment::where('transaction_id', $exchange->exchange_transaction_id)->delete();

                    // Delete sell lines
                    TransactionSellLine::where('transaction_id', $exchange->exchange_transaction_id)->delete();

                    // Delete transaction
                    $exchange_transaction->delete();
                }
            }

            // Delete the exchange record
            $exchange->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Exchange deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            \Log::emergency("Exchange delete error: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get exchange list via DataTables
     */
    public function getExchanges()
    {
        if (!auth()->user()->can('exchange.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $exchanges = TransactionExchange::leftJoin('business_locations as bl', 'transaction_exchanges.location_id', '=', 'bl.id')
            ->leftJoin('transactions as ot', 'transaction_exchanges.original_transaction_id', '=', 'ot.id')
            ->leftJoin('transactions as et', 'transaction_exchanges.exchange_transaction_id', '=', 'et.id')
            ->leftJoin('contacts as c', 'ot.contact_id', '=', 'c.id')
            ->leftJoin('users as u', 'transaction_exchanges.created_by', '=', 'u.id')
            ->where('transaction_exchanges.business_id', $business_id);

        // Apply filters
        if (request()->has('location_id') && request()->location_id != '') {
            $exchanges->where('transaction_exchanges.location_id', request()->location_id);
        }

        if (request()->has('customer_id') && request()->customer_id != '') {
            $exchanges->where('ot.contact_id', request()->customer_id);
        }

        if (request()->has('status') && request()->status != '') {
            $exchanges->where('transaction_exchanges.status', request()->status);
        }

        if (request()->has('date_range') && request()->date_range != '') {
            $date_range = explode(' - ', request()->date_range);
            if (count($date_range) == 2) {
                $exchanges->whereBetween('transaction_exchanges.exchange_date', [
                    $this->transactionUtil->uf_date(trim($date_range[0])),
                    $this->transactionUtil->uf_date(trim($date_range[1]))
                ]);
            }
        }

        if (request()->has('created_by') && request()->created_by != '') {
            $exchanges->where('transaction_exchanges.created_by', request()->created_by);
        }

        $exchanges = $exchanges->select([
            'transaction_exchanges.id',
            'transaction_exchanges.exchange_ref_no',
            'transaction_exchanges.exchange_date',
            'transaction_exchanges.total_exchange_amount',
            'transaction_exchanges.status',
            'transaction_exchanges.exchange_transaction_id',
            'transaction_exchanges.original_transaction_id',
            'transaction_exchanges.cancelled_at',
            'bl.name as location_name',
            'ot.invoice_no as original_invoice',
            'et.invoice_no as exchange_invoice',
            'c.name as customer_name',
            DB::raw("CONCAT(COALESCE(u.surname, ''), ' ', COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')) as created_by_name"),
        ]);

        return DataTables::of($exchanges)
            ->addColumn('action', function ($row) {
                $is_cancelled = (!is_null($row->cancelled_at) || $row->status == 'cancelled');

                $html = '<div class="btn-group">';
                $html .= '<button type="button" class="btn btn-xs ' . ($is_cancelled ? 'btn-default' : 'btn-info') . ' dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
                $html .= __('messages.actions') . ' <span class="caret"></span>';
                $html .= '</button>';
                $html .= '<ul class="dropdown-menu dropdown-menu-right">';

                // View exchange
                $html .= '<li><a href="#" data-href="' . action([\Modules\ExchangeItem\Http\Controllers\ExchangeController::class, 'show'], [$row->id]) . '" class="btn-modal">';
                $html .= '<i class="fas fa-eye"></i> ' . __('exchangeitem::lang.view_exchange') . '</a></li>';

                // Print exchange receipt
                $html .= '<li><a href="' . route('exchangeitem.print-receipt', [$row->id]) . '" target="_blank">';
                $html .= '<i class="fas fa-receipt"></i> ' . __('exchangeitem::lang.print_exchange_receipt') . '</a></li>';

                if ($is_cancelled) {
                    // For cancelled exchanges - show delete option
                    if (auth()->user()->can('exchange.delete')) {
                        $html .= '<li><a href="#" class="delete-exchange" data-exchange-id="' . $row->id . '" data-href="' . route('exchangeitem.destroy', [$row->id]) . '">';
                        $html .= '<i class="fas fa-trash text-danger"></i> ' . __('exchangeitem::lang.delete_exchange') . '</a></li>';
                    }
                } else {
                    // For active exchanges - show cancel option
                    if (auth()->user()->can('exchange.delete')) {
                        $html .= '<li><a href="#" class="cancel-exchange" data-exchange-id="' . $row->id . '" data-href="' . action([\Modules\ExchangeItem\Http\Controllers\ExchangeController::class, 'cancel'], [$row->id]) . '">';
                        $html .= '<i class="fas fa-ban text-danger"></i> ' . __('exchangeitem::lang.cancel_exchange') . '</a></li>';
                    }
                }

                $html .= '</ul></div>';
                return $html;
            })
            ->editColumn('status', function ($row) {
                if (!is_null($row->cancelled_at) || $row->status == 'cancelled') {
                    return '<span class="label label-danger">' . __('exchangeitem::lang.cancelled') . '</span>';
                } else {
                    return '<span class="label label-success">' . ucfirst($row->status) . '</span>';
                }
            })
            ->editColumn('total_exchange_amount', function ($row) {
                return '<span class="display_currency" data-currency_symbol="true">' . $row->total_exchange_amount . '</span>';
            })
            ->editColumn('exchange_date', function ($row) {
                return $this->transactionUtil->format_date($row->exchange_date, true);
            })
            ->setRowClass(function ($row) {
                return (!is_null($row->cancelled_at) || $row->status == 'cancelled') ? 'cancelled-exchange' : '';
            })
            ->rawColumns(['action', 'total_exchange_amount', 'status'])
            ->make(true);
    }

    /**
     * Show exchange details
     */
    public function show($id)
    {
        if (!auth()->user()->can('exchange.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $exchange = TransactionExchange::with([
            'location',
            'originalTransaction',
            'originalTransaction.contact',
            'exchangeTransaction',
            'creator',
            'exchangeLines.originalSellLine.product',
            'exchangeLines.originalSellLine.variations',
            'exchangeLines.newSellLine.product',
            'exchangeLines.newSellLine.variations'
        ])->where('business_id', $business_id)->findOrFail($id);

        return view('exchangeitem::show', compact('exchange'));
    }

    /**
     * Print exchange receipt
     */
    public function printReceipt($id)
    {
        if (!auth()->user()->can('exchange.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $business_details = $this->businessUtil->getDetails($business_id);

        $exchange = TransactionExchange::with([
            'location',
            'originalTransaction',
            'originalTransaction.contact',
            'exchangeTransaction',
            'exchangeLines.originalSellLine.product',
            'exchangeLines.originalSellLine.variations',
            'exchangeLines.newSellLine.product',
            'exchangeLines.newSellLine.variations'
        ])->where('business_id', $business_id)->findOrFail($id);

        return view('exchangeitem::receipt', compact('exchange', 'business_details'));
    }

    /**
     * Print exchange receipt in print-only format (no headers/sidebars)
     */
    public function printReceiptOnly($id)
    {
        if (!auth()->user()->can('exchange.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $business_details = $this->businessUtil->getDetails($business_id);

        $exchange = TransactionExchange::with([
            'location',
            'originalTransaction',
            'originalTransaction.contact',
            'exchangeTransaction',
            'exchangeLines.originalSellLine.product',
            'exchangeLines.originalSellLine.variations',
            'exchangeLines.newSellLine.product',
            'exchangeLines.newSellLine.variations',
            'creator'
        ])->where('business_id', $business_id)->findOrFail($id);

        // Return print-only view (no app layout)
        return view('exchangeitem::receipt-print-only', compact('exchange', 'business_details'));
    }

    /**
     * INTEGRATED: Get register details for cash register reporting
     * This method is integrated into ExchangeController to handle exchange-specific cash register calculations
     */
    public function getExchangeRegisterDetails($register_id = null)
    {
        if (!auth()->user()->can('view_cash_register')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $query = CashRegister::leftjoin(
            'cash_register_transactions as ct',
            'ct.cash_register_id',
            '=',
            'cash_registers.id'
        )
            ->join(
                'users as u',
                'u.id',
                '=',
                'cash_registers.user_id'
            )
            ->leftJoin(
                'business_locations as bl',
                'bl.id',
                '=',
                'cash_registers.location_id'
            );

        if (empty($register_id)) {
            $user_id = auth()->user()->id;
            $query->where('user_id', $user_id)
                ->where('cash_registers.status', 'open');
        } else {
            $query->where('cash_registers.id', $register_id);
        }

        $register_details = $query->select(
            'cash_registers.created_at as open_time',
            'cash_registers.closed_at as closed_at',
            'cash_registers.user_id',
            'cash_registers.closing_note',
            'cash_registers.location_id',
            'cash_registers.denominations',

            // Initial cash
            DB::raw("SUM(IF(ct.transaction_type='initial', ct.amount, 0)) as cash_in_hand"),

            // FIXED: Total sales - includes regular sales and exchange sales
            DB::raw("SUM(IF(ct.type='credit' AND ct.transaction_type = 'sell', ct.amount, 0)) as total_sale"),

            // FIXED: Total refunds - includes regular refunds and exchange refunds  
            DB::raw("SUM(IF(ct.type='debit' AND ct.transaction_type = 'refund', ct.amount, 0)) as total_refund"),

            // Expenses
            DB::raw("SUM(IF(ct.transaction_type='expense', ct.amount, 0)) as total_expense"),

            // FIXED: Cash totals - net amount considering credits and debits
            DB::raw("SUM(IF(ct.pay_method='cash', IF(ct.type='credit', ct.amount, -ct.amount), 0)) as total_cash"),
            DB::raw("SUM(IF(ct.pay_method='cash' AND ct.transaction_type='expense', ct.amount, 0)) as total_cash_expense"),

            // FIXED: Cheque totals
            DB::raw("SUM(IF(ct.pay_method='cheque', IF(ct.type='credit', ct.amount, -ct.amount), 0)) as total_cheque"),
            DB::raw("SUM(IF(ct.pay_method='cheque' AND ct.transaction_type='expense', ct.amount, 0)) as total_cheque_expense"),

            // FIXED: Card totals
            DB::raw("SUM(IF(ct.pay_method='card', IF(ct.type='credit', ct.amount, -ct.amount), 0)) as total_card"),
            DB::raw("SUM(IF(ct.pay_method='card' AND ct.transaction_type='expense', ct.amount, 0)) as total_card_expense"),

            // FIXED: Bank transfer totals
            DB::raw("SUM(IF(ct.pay_method='bank_transfer', IF(ct.type='credit', ct.amount, -ct.amount), 0)) as total_bank_transfer"),
            DB::raw("SUM(IF(ct.pay_method='bank_transfer' AND ct.transaction_type='expense', ct.amount, 0)) as total_bank_transfer_expense"),

            // FIXED: Other payment method totals
            DB::raw("SUM(IF(ct.pay_method='other', IF(ct.type='credit', ct.amount, -ct.amount), 0)) as total_other"),
            DB::raw("SUM(IF(ct.pay_method='other' AND ct.transaction_type='expense', ct.amount, 0)) as total_other_expense"),

            // Advance payments
            DB::raw("SUM(IF(ct.pay_method='advance', IF(ct.type='credit', ct.amount, -ct.amount), 0)) as total_advance"),
            DB::raw("SUM(IF(ct.pay_method='advance' AND ct.transaction_type='expense', ct.amount, 0)) as total_advance_expense"),

            // FIXED: Specific refund totals by payment method
            DB::raw("SUM(IF(ct.type='debit' AND ct.pay_method='cash', ct.amount, 0)) as total_cash_refund"),
            DB::raw("SUM(IF(ct.type='debit' AND ct.pay_method='cheque', ct.amount, 0)) as total_cheque_refund"),
            DB::raw("SUM(IF(ct.type='debit' AND ct.pay_method='card', ct.amount, 0)) as total_card_refund"),
            DB::raw("SUM(IF(ct.type='debit' AND ct.pay_method='bank_transfer', ct.amount, 0)) as total_bank_transfer_refund"),
            DB::raw("SUM(IF(ct.type='debit' AND ct.pay_method='other', ct.amount, 0)) as total_other_refund"),
            DB::raw("SUM(IF(ct.type='debit' AND ct.pay_method='advance', ct.amount, 0)) as total_advance_refund"),

            // Count totals
            DB::raw("SUM(IF(ct.pay_method='cheque', 1, 0)) as total_cheques"),
            DB::raw("SUM(IF(ct.pay_method='card', 1, 0)) as total_card_slips"),

            // User and location info
            DB::raw("CONCAT(COALESCE(u.surname, ''), ' ', COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')) as user_name"),
            'u.email',
            'bl.name as location_name'
        )->first();

        return $register_details;
    }
}
