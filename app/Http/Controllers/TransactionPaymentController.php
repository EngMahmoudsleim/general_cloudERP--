<?php

namespace App\Http\Controllers;

use App\Contact;
use App\Events\TransactionPaymentAdded;
use App\Events\TransactionPaymentUpdated;
use App\Exceptions\AdvanceBalanceNotAvailable;
use App\Transaction;
use App\TransactionPayment;
use App\Utils\ModuleUtil;
use App\Utils\TransactionUtil;
use Datatables;
use DB;
use Illuminate\Http\Request;

class TransactionPaymentController extends Controller
{
    protected $transactionUtil;

    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param  TransactionUtil  $transactionUtil
     * @return void
     */
    public function __construct(TransactionUtil $transactionUtil, ModuleUtil $moduleUtil)
    {
        $this->transactionUtil = $transactionUtil;
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index_0()
    {
        if (! (auth()->user()->can('sell.payments') || auth()->user()->can('purchase.payments'))) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        if (request()->ajax()) {
            $contact_id = request()->get('contact_id');
            $contact_type = request()->get('contact_type');
            $voucher_type = request()->get('voucher_type');

            $query = TransactionPayment::leftJoin('contacts as c', 'transaction_payments.payment_for', '=', 'c.id')
                        ->leftJoin('transactions as t', 'transaction_payments.transaction_id', '=', 't.id')
                        ->where('transaction_payments.business_id', $business_id)
                        ->whereNull('transaction_payments.parent_id')
                        ->select(
                            'transaction_payments.id',
                            'transaction_payments.amount',
                            'transaction_payments.method',
                            'transaction_payments.paid_on',
                            'transaction_payments.payment_ref_no',
                            'transaction_payments.transaction_no',
                            'transaction_payments.is_return',
                            'transaction_payments.cheque_number',
                            'transaction_payments.card_transaction_number',
                            'transaction_payments.bank_account_number',
                            'c.name as contact_name',
                            'c.supplier_business_name',
                            'c.type as contact_type',
                            't.type as transaction_type',
                            't.ref_no',
                            't.invoice_no',
                            't.id as transaction_id'
                        )
                        ->groupBy('transaction_payments.id');

            if (! empty($contact_id)) {
                $query->where('transaction_payments.payment_for', $contact_id);
            }

            if (! empty($contact_type)) {
                if ($contact_type === 'customer') {
                    $query->whereIn('c.type', ['customer', 'both']);
                } elseif ($contact_type === 'supplier') {
                    $query->whereIn('c.type', ['supplier', 'both']);
                }
            }

            if (! empty($voucher_type)) {
                $query->where(function ($q) use ($voucher_type) {
                    if ($voucher_type === 'receipt') {
                        $q->whereIn('t.type', ['sell', 'sell_return']);
                    } elseif ($voucher_type === 'payment') {
                        $q->whereIn('t.type', ['purchase', 'purchase_return']);
                    }
                });
            }

            $payment_types = $this->transactionUtil->payment_types(null, true, $business_id);

            return Datatables::of($query)
                ->editColumn('paid_on', '{{@format_datetime($paid_on)}}')
                ->addColumn('contact', function ($row) {
                    $name = $row->contact_name;
                    if (! empty($row->supplier_business_name)) {
                        $name = $row->supplier_business_name.' ('.$row->contact_name.')';
                    }

                    return $name;
                })
                ->addColumn('voucher_type', function ($row) {
                    $receipt_types = ['sell', 'sell_return'];

                    if ($row->is_return) {
                        return __('lang_v1.refund');
                    }

                    return in_array($row->transaction_type, $receipt_types) ? __('lang_v1.receipt') : __('lang_v1.payment');
                })
                ->editColumn('amount', function ($row) {
                    return '<span class="display_currency paid-amount" data-orig-value="'.$row->amount.'" data-currency_symbol ="true">'.$row->amount.'</span>';
                })
                ->editColumn('method', function ($row) use ($payment_types) {
                    $method = ! empty($payment_types[$row->method]) ? $payment_types[$row->method] : '';

                    if ($row->method == 'cheque') {
                        $method .= '<br>('.__('lang_v1.cheque_no').': '.$row->cheque_number.')';
                    } elseif ($row->method == 'card') {
                        $method .= '<br>('.__('lang_v1.card_transaction_no').': '.$row->card_transaction_number.')';
                    } elseif ($row->method == 'bank_transfer') {
                        $method .= '<br>('.__('lang_v1.bank_account_no').': '.$row->bank_account_number.')';
                    } elseif ($row->method == 'custom_pay_1') {
                        $method .= '<br>('.__('lang_v1.transaction_no').': '.$row->transaction_no.')';
                    } elseif ($row->method == 'custom_pay_2') {
                        $method .= '<br>('.__('lang_v1.transaction_no').': '.$row->transaction_no.')';
                    } elseif ($row->method == 'custom_pay_3') {
                        $method .= '<br>('.__('lang_v1.transaction_no').': '.$row->transaction_no.')';
                    }

                    return $method;
                })
                ->addColumn('reference', function ($row) {
                    $ref = [];

                    if (! empty($row->invoice_no)) {
                        $ref[] = $row->invoice_no;
                    }

                    if (! empty($row->ref_no)) {
                        $ref[] = $row->ref_no;
                    }

                    return implode(' / ', $ref);
                })
                ->addColumn('action', '<button type="button" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline  tw-dw-btn-primary view_payment" data-href="{{ action([\App\Http\Controllers\TransactionPaymentController::class, \"viewPayment\"], [$id]) }}"><i class="fas fa-eye"></i> @lang("messages.view")</button>')
                ->removeColumn('supplier_business_name')
                ->removeColumn('contact_name')
                ->removeColumn('transaction_no')
                ->removeColumn('cheque_number')
                ->removeColumn('card_transaction_number')
                ->removeColumn('bank_account_number')
                ->removeColumn('transaction_type')
                ->rawColumns(['amount', 'method', 'action'])
                ->make(true);
        }

        $contact_types = [
            'customer' => __('report.customer'),
            'supplier' => __('report.supplier'),
        ];

        $customer_contacts = Contact::customersDropdown($business_id, true, false);
        $supplier_contacts = Contact::suppliersDropdown($business_id, true, false);

        return view('transaction_payment.index')
            ->with(compact('customer_contacts', 'supplier_contacts', 'contact_types'));
    }
 public function index01()
{
    if (! (auth()->user()->can('sell.payments') || auth()->user()->can('purchase.payments'))) {
        abort(403, 'Unauthorized action.');
    }

    $business_id = request()->session()->get('user.business_id');

    // إذا كان Ajax request
    if (request()->ajax()) {
        $contact_id = request()->get('contact_id');
        $contact_type = request()->get('contact_type');
        $voucher_type = request()->get('voucher_type');
        $date_from = request()->get('date_from');
        $date_to = request()->get('date_to');
        $method = request()->get('method');

        $query = TransactionPayment::leftJoin('contacts as c', 'transaction_payments.payment_for', '=', 'c.id')
                    ->leftJoin('transactions as t', 'transaction_payments.transaction_id', '=', 't.id')
                    ->where('transaction_payments.business_id', $business_id)
                    ->whereNull('transaction_payments.parent_id')
                    ->select(
                        'transaction_payments.id',
                        'transaction_payments.amount',
                        'transaction_payments.method',
                        'transaction_payments.paid_on',
                        'transaction_payments.payment_ref_no',
                        'transaction_payments.transaction_no',
                        'transaction_payments.is_return',
                        'transaction_payments.cheque_number',
                        'transaction_payments.card_transaction_number',
                        'transaction_payments.bank_account_number',
                        'c.name as contact_name',
                        'c.supplier_business_name',
                        'c.type as contact_type',
                        't.type as transaction_type',
                        't.ref_no',
                        't.invoice_no',
                        't.id as transaction_id'
                    )
                    ->groupBy('transaction_payments.id');

        // Filter by contact
        if (!empty($contact_id)) {
            $query->where('transaction_payments.payment_for', $contact_id);
        }

        // Filter by voucher type
        if (!empty($voucher_type)) {
            if ($voucher_type === 'receipt') {
                $query->whereIn('t.type', ['sell', 'sell_return']);
            } elseif ($voucher_type === 'payment') {
                $query->whereIn('t.type', ['purchase', 'purchase_return']);
            }
        }

        // Filter by date
        if (!empty($date_from)) {
            $query->where('transaction_payments.paid_on', '>=', $date_from);
        }
        if (!empty($date_to)) {
            $query->where('transaction_payments.paid_on', '<=', $date_to);
        }

        // Filter by payment method
        if (!empty($method)) {
            $query->where('transaction_payments.method', $method);
        }

        $payment_types = $this->transactionUtil->payment_types(null, true, $business_id);

        return Datatables::of($query)
            ->editColumn('paid_on', '{{@format_datetime($paid_on)}}')
            ->addColumn('contact', function ($row) {
                $name = $row->contact_name;
                if (!empty($row->supplier_business_name)) {
                    $name = $row->supplier_business_name . ' (' . $row->contact_name . ')';
                }
                return $name;
            })
            ->editColumn('amount', function ($row) {
                return '<span class="display_currency" data-orig-value="' . $row->amount . '" data-currency_symbol="true">' . $row->amount . '</span>';
            })
            ->editColumn('method', function ($row) use ($payment_types) {
                return $payment_types[$row->method] ?? '';
            })
            ->addColumn('action', function($row) {
                $html = '<button type="button" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline tw-dw-btn-primary view_payment" data-href="' . action([\App\Http\Controllers\TransactionPaymentController::class, 'viewPayment'], [$row->id]) . '"><i class="fas fa-eye"></i> ' . __("messages.view") . '</button>';
                
                if (auth()->user()->can('edit_sell_payment') || auth()->user()->can('edit_purchase_payment')) {
                    $html .= ' <button type="button" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline tw-dw-btn-info edit_payment" data-href="' . action([\App\Http\Controllers\TransactionPaymentController::class, 'edit'], [$row->id]) . '"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</button>';
                }
                
                if (auth()->user()->can('delete_sell_payment') || auth()->user()->can('delete_purchase_payment')) {
                    $html .= ' <button type="button" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline tw-dw-btn-error delete_payment" data-href="' . action([\App\Http\Controllers\TransactionPaymentController::class, 'destroy'], [$row->id]) . '"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</button>';
                }
                
                return $html;
            })
            ->rawColumns(['amount', 'action'])
            ->make(true);
    }

    // للصفحة العادية
    $customer_contacts = Contact::customersDropdown($business_id, true, false);
    $supplier_contacts = Contact::suppliersDropdown($business_id, true, false);
    $payment_types = $this->transactionUtil->payment_types(null, true, $business_id);

    return view('transaction_payment.index')
        ->with(compact('customer_contacts', 'supplier_contacts', 'payment_types'));
}


public function index000()
{
    if (! (auth()->user()->can('sell.payments') || auth()->user()->can('purchase.payments'))) {
        abort(403, 'Unauthorized action.');
    }

    $business_id = request()->session()->get('user.business_id');

    // إذا كان Ajax request
    if (request()->ajax()) {
        $contact_id = request()->get('contact_id');
        $contact_type = request()->get('contact_type');
        $voucher_type = request()->get('voucher_type');
        $date_from = request()->get('date_from');
        $date_to = request()->get('date_to');
        $method = request()->get('method');

        $query = TransactionPayment::leftJoin('contacts as c', 'transaction_payments.payment_for', '=', 'c.id')
                    ->leftJoin('transactions as t', 'transaction_payments.transaction_id', '=', 't.id')
                    ->where('transaction_payments.business_id', $business_id)
                    ->whereNull('transaction_payments.parent_id')
                    // أضف العلاقات هنا
                    ->with(['child_payments', 'child_payments.transaction', 'transaction.contact'])
                    ->select(
                        'transaction_payments.*', // اختيار جميع أعمدة transaction_payments
                        'c.name as contact_name',
                        'c.supplier_business_name',
                        'c.type as contact_type',
                        't.type as transaction_type',
                        't.ref_no',
                        't.invoice_no',
                        't.id as transaction_id'
                    )
                    ->groupBy('transaction_payments.id');

        // Filter by contact
        if (!empty($contact_id)) {
            $query->where('transaction_payments.payment_for', $contact_id);
        }

        // Filter by voucher type
        if (!empty($voucher_type)) {
            if ($voucher_type === 'receipt') {
                $query->whereIn('t.type', ['sell', 'sell_return']);
            } elseif ($voucher_type === 'payment') {
                $query->whereIn('t.type', ['purchase', 'purchase_return']);
            }
        }

        // Filter by date
        if (!empty($date_from)) {
            $query->where('transaction_payments.paid_on', '>=', $date_from);
        }
        if (!empty($date_to)) {
            $query->where('transaction_payments.paid_on', '<=', $date_to);
        }

        // Filter by payment method
        if (!empty($method)) {
            $query->where('transaction_payments.method', $method);
        }

        $payment_types = $this->transactionUtil->payment_types(null, true, $business_id);

        // استخدم view بدلاً من Datatables
        $payments = $query->get();
        
        $html = '';
        foreach ($payments as $payment) {
            $count_child_payments = count($payment->child_payments);
            
            $html .= view('transaction_payment.partials.payment_row', compact('payment', 'count_child_payments', 'payment_types'))->render();
            
            // عرض الدفعات الفرعية
            if ($count_child_payments > 0) {
                foreach ($payment->child_payments as $child_payment) {
                    $html .= view('transaction_payment.partials.payment_row', [
                        'payment' => $child_payment, 
                        'count_child_payments' => 0, 
                        'payment_types' => $payment_types,
                        'parent_payment_ref_no' => $payment->payment_ref_no
                    ])->render();
                }
            }
        }
        
        return response()->json([
            'html' => $html,
            'total' => count($payments)
        ]);
    }

    // للصفحة العادية
    $customer_contacts = Contact::customersDropdown($business_id, true, false);
    $supplier_contacts = Contact::suppliersDropdown($business_id, true, false);
    $payment_types = $this->transactionUtil->payment_types(null, true, $business_id);

    return view('transaction_payment.index')
        ->with(compact('customer_contacts', 'supplier_contacts', 'payment_types'));
}
/**
 * Get dashboard statistics for vouchers page
 */
public function getDashboardStats000()
{
    if (!auth()->user()->can('sell.payments') && !auth()->user()->can('purchase.payments')) {
        abort(403, 'Unauthorized action.');
    }

    $business_id = request()->session()->get('user.business_id');
    
    // Total receipts (sell payments)
    $total_receipts = TransactionPayment::leftJoin('transactions as t', 'transaction_payments.transaction_id', '=', 't.id')
        ->where('transaction_payments.business_id', $business_id)
        ->whereIn('t.type', ['sell', 'sell_return'])
        ->sum('transaction_payments.amount');
    
    // Total payments (purchase payments)
    $total_payments = TransactionPayment::leftJoin('transactions as t', 'transaction_payments.transaction_id', '=', 't.id')
        ->where('transaction_payments.business_id', $business_id)
        ->whereIn('t.type', ['purchase', 'purchase_return'])
        ->sum('transaction_payments.amount');
    
    // Today's receipts
    $today_receipts = TransactionPayment::leftJoin('transactions as t', 'transaction_payments.transaction_id', '=', 't.id')
        ->where('transaction_payments.business_id', $business_id)
        ->whereIn('t.type', ['sell', 'sell_return'])
        ->whereDate('transaction_payments.paid_on', \Carbon::today())
        ->sum('transaction_payments.amount');
    
    // Today's payments
    $today_payments = TransactionPayment::leftJoin('transactions as t', 'transaction_payments.transaction_id', '=', 't.id')
        ->where('transaction_payments.business_id', $business_id)
        ->whereIn('t.type', ['purchase', 'purchase_return'])
        ->whereDate('transaction_payments.paid_on', \Carbon::today())
        ->sum('transaction_payments.amount');
    
    // Receipt count
    $receipt_count = TransactionPayment::leftJoin('transactions as t', 'transaction_payments.transaction_id', '=', 't.id')
        ->where('transaction_payments.business_id', $business_id)
        ->whereIn('t.type', ['sell', 'sell_return'])
        ->count();
    
    // Payment count
    $payment_count = TransactionPayment::leftJoin('transactions as t', 'transaction_payments.transaction_id', '=', 't.id')
        ->where('transaction_payments.business_id', $business_id)
        ->whereIn('t.type', ['purchase', 'purchase_return'])
        ->count();
    
    return response()->json([
        'success' => true,
        'total_receipts' => $this->transactionUtil->num_f($total_receipts, true),
        'total_payments' => $this->transactionUtil->num_f($total_payments, true),
        'today_receipts' => $this->transactionUtil->num_f($today_receipts, true),
        'today_payments' => $this->transactionUtil->num_f($today_payments, true),
        'receipt_count' => $receipt_count,
        'payment_count' => $payment_count
    ]);
}
public function index()
{
    if (! (auth()->user()->can('sell.payments') || auth()->user()->can('purchase.payments'))) {
        abort(403, 'Unauthorized action.');
    }

    $business_id = request()->session()->get('user.business_id');

    // For normal page load
    $customer_contacts = Contact::customersDropdown($business_id, true, false);
    $supplier_contacts = Contact::suppliersDropdown($business_id, true, false);
    $payment_types = $this->transactionUtil->payment_types(null, true, $business_id);

    return view('transaction_payment.index')
        ->with(compact('customer_contacts', 'supplier_contacts', 'payment_types'));
}

/**
 * Get payments data for AJAX request
 */
public function getPayments(Request $request)
{
    if (! (auth()->user()->can('sell.payments') || auth()->user()->can('purchase.payments'))) {
        abort(403, 'Unauthorized action.');
    }

    $business_id = request()->session()->get('user.business_id');
    
    $contact_id = $request->get('contact_id');
    $voucher_type = $request->get('voucher_type'); // 'receipt' or 'payment'
    $date_from = $request->get('date_from');
    $date_to = $request->get('date_to');
    $method = $request->get('method');

    if (empty($contact_id)) {
        return response()->json([
            'success' => false,
            'msg' => 'من فضلك اختر عميل/مورد أولاً'
        ]);
    }

    $query = TransactionPayment::leftJoin('transactions as t', 'transaction_payments.transaction_id', '=', 't.id')
        ->leftJoin('contacts as c', 'transaction_payments.payment_for', '=', 'c.id')
        ->where('transaction_payments.business_id', $business_id)
        ->where('transaction_payments.payment_for', $contact_id)
        ->whereNull('transaction_payments.parent_id')
        ->with(['child_payments', 'child_payments.transaction']);

    // Filter by voucher type
    if ($voucher_type === 'receipt') {
        $query->whereIn('t.type', ['sell', 'sell_return']);
    } elseif ($voucher_type === 'payment') {
        $query->whereIn('t.type', ['purchase', 'purchase_return']);
    }

    // Filter by date
    if (!empty($date_from)) {
        $query->whereDate('transaction_payments.paid_on', '>=', $date_from);
    }
    if (!empty($date_to)) {
        $query->whereDate('transaction_payments.paid_on', '<=', $date_to);
    }

    // Filter by method
    if (!empty($method)) {
        $query->where('transaction_payments.method', $method);
    }

    $query->select(
        'transaction_payments.*',
        't.type as transaction_type',
        't.ref_no',
        't.invoice_no',
        'c.name as contact_name',
        'c.supplier_business_name'
    );

    $payments = $query->orderByDesc('transaction_payments.paid_on')->get();
    $payment_types = $this->transactionUtil->payment_types(null, true, $business_id);

    // Render HTML
    $html = view('transaction_payment.partials.payment_rows', compact('payments', 'payment_types'))->render();
    
    // Calculate total
    $total = $payments->sum('amount');

    return response()->json([
        'success' => true,
        'html' => $html,
        'total' => $this->transactionUtil->num_f($total, true),
        'count' => $payments->count()
    ]);
}

/**
 * Get dashboard stats
 */
public function getDashboardStats()
{
    if (!auth()->user()->can('sell.payments') && !auth()->user()->can('purchase.payments')) {
        abort(403, 'Unauthorized action.');
    }

    $business_id = request()->session()->get('user.business_id');
    
    $total_receipts = TransactionPayment::join('transactions as t', 'transaction_payments.transaction_id', '=', 't.id')
        ->where('transaction_payments.business_id', $business_id)
        ->whereIn('t.type', ['sell', 'sell_return'])
        ->sum('transaction_payments.amount');
    
    $total_payments = TransactionPayment::join('transactions as t', 'transaction_payments.transaction_id', '=', 't.id')
        ->where('transaction_payments.business_id', $business_id)
        ->whereIn('t.type', ['purchase', 'purchase_return'])
        ->sum('transaction_payments.amount');
    
    $today_receipts = TransactionPayment::join('transactions as t', 'transaction_payments.transaction_id', '=', 't.id')
        ->where('transaction_payments.business_id', $business_id)
        ->whereIn('t.type', ['sell', 'sell_return'])
        ->whereDate('transaction_payments.paid_on', \Carbon::today())
        ->sum('transaction_payments.amount');
    
    $today_payments = TransactionPayment::join('transactions as t', 'transaction_payments.transaction_id', '=', 't.id')
        ->where('transaction_payments.business_id', $business_id)
        ->whereIn('t.type', ['purchase', 'purchase_return'])
        ->whereDate('transaction_payments.paid_on', \Carbon::today())
        ->sum('transaction_payments.amount');
    
    return response()->json([
        'success' => true,
        'total_receipts' => $this->transactionUtil->num_f($total_receipts, true),
        'total_payments' => $this->transactionUtil->num_f($total_payments, true),
        'today_receipts' => $this->transactionUtil->num_f($today_receipts, true),
        'today_payments' => $this->transactionUtil->num_f($today_payments, true),
    ]);
}
















/**
 * 
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $business_id = $request->session()->get('user.business_id');
            $transaction_id = $request->input('transaction_id');
            $transaction = Transaction::where('business_id', $business_id)->with(['contact'])->findOrFail($transaction_id);

            $transaction_before = $transaction->replicate();

            if (! (auth()->user()->can('purchase.payments') || auth()->user()->can('hms.add_booking_payment') || auth()->user()->can('sell.payments') || auth()->user()->can('all_expense.access') || auth()->user()->can('view_own_expense'))) {
                abort(403, 'Unauthorized action.');
            }

            if ($transaction->payment_status != 'paid') {
                $inputs = $request->only(['amount', 'method', 'note', 'card_number', 'card_holder_name',
                    'card_transaction_number', 'card_type', 'card_month', 'card_year', 'card_security',
                    'cheque_number', 'bank_account_number', ]);
                $inputs['paid_on'] = $this->transactionUtil->uf_date($request->input('paid_on'), true);
                $inputs['transaction_id'] = $transaction->id;
                $inputs['amount'] = $this->transactionUtil->num_uf($inputs['amount']);
                $inputs['created_by'] = auth()->user()->id;
                $inputs['payment_for'] = $transaction->contact_id;

                if ($inputs['method'] == 'custom_pay_1') {
                    $inputs['transaction_no'] = $request->input('transaction_no_1');
                } elseif ($inputs['method'] == 'custom_pay_2') {
                    $inputs['transaction_no'] = $request->input('transaction_no_2');
                } elseif ($inputs['method'] == 'custom_pay_3') {
                    $inputs['transaction_no'] = $request->input('transaction_no_3');
                }

                if (! empty($request->input('account_id')) && $inputs['method'] != 'advance') {
                    $inputs['account_id'] = $request->input('account_id');
                }

                $prefix_type = 'purchase_payment';
                if (in_array($transaction->type, ['sell', 'sell_return'])) {
                    $prefix_type = 'sell_payment';
                } elseif (in_array($transaction->type, ['expense', 'expense_refund'])) {
                    $prefix_type = 'expense_payment';
                }

                DB::beginTransaction();

                $ref_count = $this->transactionUtil->setAndGetReferenceCount($prefix_type);
                //Generate reference number
                $inputs['payment_ref_no'] = $this->transactionUtil->generateReferenceNumber($prefix_type, $ref_count);

                $inputs['business_id'] = $request->session()->get('business.id');
                $inputs['document'] = $this->transactionUtil->uploadFile($request, 'document', 'documents');

                //Pay from advance balance
                $payment_amount = $inputs['amount'];
                $contact_balance = ! empty($transaction->contact) ? $transaction->contact->balance : 0;
                if ($inputs['method'] == 'advance' && $inputs['amount'] > $contact_balance) {
                    throw new AdvanceBalanceNotAvailable(__('lang_v1.required_advance_balance_not_available'));
                }

                if (! empty($inputs['amount'])) {
                    $tp = TransactionPayment::create($inputs);

                    if (! empty($request->input('denominations'))) {
                        $this->transactionUtil->addCashDenominations($tp, $request->input('denominations'));
                    }

                    $inputs['transaction_type'] = $transaction->type;
                    event(new TransactionPaymentAdded($tp, $inputs));
                }

                //update payment status
                $payment_status = $this->transactionUtil->updatePaymentStatus($transaction_id, $transaction->final_total);
                $transaction->payment_status = $payment_status;

                $this->transactionUtil->activityLog($transaction, 'payment_edited', $transaction_before);

                DB::commit();
            }

            $output = ['success' => true,
                'msg' => __('purchase.payment_added_success'),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            $msg = __('messages.something_went_wrong');

            if (get_class($e) == \App\Exceptions\AdvanceBalanceNotAvailable::class) {
                $msg = $e->getMessage();
            } else {
                \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
            }

            $output = ['success' => false,
                'msg' => $msg,
            ];
        }

        return redirect()->back()->with(['status' => $output]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! (auth()->user()->can('sell.payments') || auth()->user()->can('purchase.payments') || auth()->user()->can('hms.add_booking_payment'))) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $transaction = Transaction::where('id', $id)
                                        ->with(['contact', 'business', 'transaction_for'])
                                        ->first();
            $payments_query = TransactionPayment::where('transaction_id', $id);

            $accounts_enabled = false;
            if ($this->moduleUtil->isModuleEnabled('account')) {
                $accounts_enabled = true;
                $payments_query->with(['payment_account']);
            }

            $payments = $payments_query->get();
            $location_id = ! empty($transaction->location_id) ? $transaction->location_id : null;
            $payment_types = $this->transactionUtil->payment_types($location_id, true);

            return view('transaction_payment.show_payments')
                    ->with(compact('transaction', 'payments', 'payment_types', 'accounts_enabled'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! auth()->user()->can('edit_purchase_payment') && ! auth()->user()->can('edit_sell_payment') && !auth()->user()->can('hms.edit_booking_payment')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $payment_line = TransactionPayment::with(['denominations'])->where('method', '!=', 'advance')->findOrFail($id);

            $transaction = Transaction::where('id', $payment_line->transaction_id)
                                        ->where('business_id', $business_id)
                                        ->with(['contact', 'location'])
                                        ->first();

            $payment_types = $this->transactionUtil->payment_types($transaction->location);

            //Accounts
            $accounts = $this->moduleUtil->accountsDropdown($business_id, true, false, true);

            return view('transaction_payment.edit_payment_row')
                        ->with(compact('transaction', 'payment_types', 'payment_line', 'accounts'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (! auth()->user()->can('edit_purchase_payment') && ! auth()->user()->can('edit_sell_payment') && ! auth()->user()->can('all_expense.access') && ! auth()->user()->can('view_own_expense') && !auth()->user()->can('hms.edit_booking_payment')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = request()->session()->get('user.business_id');

            $inputs = $request->only(['amount', 'method', 'note', 'card_number', 'card_holder_name',
                'card_transaction_number', 'card_type', 'card_month', 'card_year', 'card_security',
                'cheque_number', 'bank_account_number', ]);
            $inputs['paid_on'] = $this->transactionUtil->uf_date($request->input('paid_on'), true);
            $inputs['amount'] = $this->transactionUtil->num_uf($inputs['amount']);

            if ($inputs['method'] == 'custom_pay_1') {
                $inputs['transaction_no'] = $request->input('transaction_no_1');
            } elseif ($inputs['method'] == 'custom_pay_2') {
                $inputs['transaction_no'] = $request->input('transaction_no_2');
            } elseif ($inputs['method'] == 'custom_pay_3') {
                $inputs['transaction_no'] = $request->input('transaction_no_3');
            }

            if (! empty($request->input('account_id'))) {
                $inputs['account_id'] = $request->input('account_id');
            }

            $payment = TransactionPayment::where('method', '!=', 'advance')->findOrFail($id);

            if (! empty($request->input('denominations'))) {
                $this->transactionUtil->updateCashDenominations($payment, $request->input('denominations'));
            }

            //Update parent payment if exists
            if (! empty($payment->parent_id)) {
                $parent_payment = TransactionPayment::find($payment->parent_id);
                $parent_payment->amount = $parent_payment->amount - ($payment->amount - $inputs['amount']);

                $parent_payment->save();
            }

            $business_id = $request->session()->get('user.business_id');

            $transaction = Transaction::where('business_id', $business_id)
                                ->find($payment->transaction_id);

            $transaction_before = $transaction->replicate();
            $document_name = $this->transactionUtil->uploadFile($request, 'document', 'documents');
            if (! empty($document_name)) {
                $inputs['document'] = $document_name;
            }

            DB::beginTransaction();

            $payment->update($inputs);

            //update payment status
            $payment_status = $this->transactionUtil->updatePaymentStatus($payment->transaction_id);
            $transaction->payment_status = $payment_status;

            $this->transactionUtil->activityLog($transaction, 'payment_edited', $transaction_before);

            DB::commit();

            //event
            event(new TransactionPaymentUpdated($payment, $transaction->type));

            $output = ['success' => true,
                'msg' => __('purchase.payment_updated_success'),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => false,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return redirect()->back()->with(['status' => $output]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! auth()->user()->can('delete_purchase_payment') && ! auth()->user()->can('delete_sell_payment') && ! auth()->user()->can('all_expense.access') && ! auth()->user()->can('view_own_expense') && !auth()->user()->can('hms.delete_booking_payment')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $payment = TransactionPayment::findOrFail($id);

                DB::beginTransaction();

                if (! empty($payment->transaction_id)) {
                    TransactionPayment::deletePayment($payment);
                } else { //advance payment
                    $adjusted_payments = TransactionPayment::where('parent_id',
                                                $payment->id)
                                                ->get();

                    $total_adjusted_amount = $adjusted_payments->sum('amount');

                    //Get customer advance share from payment and deduct from advance balance
                    $total_customer_advance = $payment->amount - $total_adjusted_amount;
                    if ($total_customer_advance > 0) {
                        $this->transactionUtil->updateContactBalance($payment->payment_for, $total_customer_advance, 'deduct');
                    }

                    //Delete all child payments
                    foreach ($adjusted_payments as $adjusted_payment) {
                        //Make parent payment null as it will get deleted
                        $adjusted_payment->parent_id = null;
                        TransactionPayment::deletePayment($adjusted_payment);
                    }

                    //Delete advance payment
                    TransactionPayment::deletePayment($payment);
                }

                DB::commit();

                $output = ['success' => true,
                    'msg' => __('purchase.payment_deleted_success'),
                ];
            } catch (\Exception $e) {
                DB::rollBack();

                \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

                $output = ['success' => false,
                    'msg' => __('messages.something_went_wrong'),
                ];
            }

            return $output;
        }
    }

    /**
     * Adds new payment to the given transaction.
     *
     * @param  int  $transaction_id
     * @return \Illuminate\Http\Response
     */
    public function addPayment($transaction_id)
    {
        if (! auth()->user()->can('purchase.payments') && ! auth()->user()->can('sell.payments') && ! auth()->user()->can('all_expense.access') && ! auth()->user()->can('view_own_expense') && !auth()->user()->can('hms.add_booking_payment')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $transaction = Transaction::where('business_id', $business_id)
                                        ->with(['contact', 'location'])
                                        ->findOrFail($transaction_id);
            if ($transaction->payment_status != 'paid') {
                $show_advance = in_array($transaction->type, ['sell', 'purchase']) ? true : false;
                $payment_types = $this->transactionUtil->payment_types($transaction->location, $show_advance);

                $paid_amount = $this->transactionUtil->getTotalPaid($transaction_id);
                $amount = $transaction->final_total - $paid_amount;
                if ($amount < 0) {
                    $amount = 0;
                }

                $amount_formated = $this->transactionUtil->num_f($amount);

                $payment_line = new TransactionPayment();
                $payment_line->amount = $amount;
                $payment_line->method = 'cash';
                $payment_line->paid_on = \Carbon::now()->toDateTimeString();

                //Accounts
                $accounts = $this->moduleUtil->accountsDropdown($business_id, true, false, true);

                $view = view('transaction_payment.payment_row')
                ->with(compact('transaction', 'payment_types', 'payment_line', 'amount_formated', 'accounts'))->render();

                $output = ['status' => 'due',
                    'view' => $view, ];
            } else {
                $output = ['status' => 'paid',
                    'view' => '',
                    'msg' => __('purchase.amount_already_paid'),  ];
            }

            return json_encode($output);
        }
    }

    /**
     * Shows contact's payment due modal
     *
     * @param  int  $contact_id
     * @return \Illuminate\Http\Response
     */
    public function getPayContactDue000($contact_id)
    {
        if (! (auth()->user()->can('sell.payments') || auth()->user()->can('purchase.payments'))) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $due_payment_type = request()->input('type');
            $query = Contact::where('contacts.id', $contact_id)
                            ->leftjoin('transactions AS t', 'contacts.id', '=', 't.contact_id');
            if ($due_payment_type == 'purchase') {
                $query->select(
                    DB::raw("SUM(IF(t.type = 'purchase', final_total, 0)) as total_purchase"),
                    DB::raw("SUM(IF(t.type = 'purchase', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as total_paid"),
                    'contacts.name',
                    'contacts.supplier_business_name',
                    'contacts.id as contact_id'
                    );
            } elseif ($due_payment_type == 'purchase_return') {
                $query->select(
                    DB::raw("SUM(IF(t.type = 'purchase_return', final_total, 0)) as total_purchase_return"),
                    DB::raw("SUM(IF(t.type = 'purchase_return', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as total_return_paid"),
                    'contacts.name',
                    'contacts.supplier_business_name',
                    'contacts.id as contact_id'
                    );
            } elseif ($due_payment_type == 'sell') {
                $query->select(
                    DB::raw("SUM(IF(t.type = 'sell' AND t.status = 'final', final_total, 0)) as total_invoice"),
                    DB::raw("SUM(IF(t.type = 'sell' AND t.status = 'final', (SELECT SUM(IF(is_return = 1,-1*amount,amount)) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as total_paid"),
                    'contacts.name',
                    'contacts.supplier_business_name',
                    'contacts.id as contact_id'
                );
            } elseif ($due_payment_type == 'sell_return') {
                $query->select(
                    DB::raw("SUM(IF(t.type = 'sell_return', final_total, 0)) as total_sell_return"),
                    DB::raw("SUM(IF(t.type = 'sell_return', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as total_return_paid"),
                    'contacts.name',
                    'contacts.supplier_business_name',
                    'contacts.id as contact_id'
                    );
            }

            //Query for opening balance details
            $query->addSelect(
                DB::raw("SUM(IF(t.type = 'opening_balance', final_total, 0)) as opening_balance"),
                DB::raw("SUM(IF(t.type = 'opening_balance', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as opening_balance_paid")
            );
            $contact_details = $query->first();

            $payment_line = new TransactionPayment();
            if ($due_payment_type == 'purchase') {
                $contact_details->total_purchase = empty($contact_details->total_purchase) ? 0 : $contact_details->total_purchase;
                $payment_line->amount = $contact_details->total_purchase -
                                    $contact_details->total_paid;
            } elseif ($due_payment_type == 'purchase_return') {
                $payment_line->amount = $contact_details->total_purchase_return -
                                    $contact_details->total_return_paid;
            } elseif ($due_payment_type == 'sell') {
                $contact_details->total_invoice = empty($contact_details->total_invoice) ? 0 : $contact_details->total_invoice;

                $payment_line->amount = $contact_details->total_invoice -
                                    $contact_details->total_paid;
            } elseif ($due_payment_type == 'sell_return') {
                $payment_line->amount = $contact_details->total_sell_return -
                                    $contact_details->total_return_paid;
            }

            //If opening balance due exists add to payment amount
            $contact_details->opening_balance = ! empty($contact_details->opening_balance) ? $contact_details->opening_balance : 0;
            $contact_details->opening_balance_paid = ! empty($contact_details->opening_balance_paid) ? $contact_details->opening_balance_paid : 0;
            $ob_due = $contact_details->opening_balance - $contact_details->opening_balance_paid;
            if ($ob_due > 0) {
                $payment_line->amount += $ob_due;
            }

            $amount_formated = $this->transactionUtil->num_f($payment_line->amount);

            $contact_details->total_paid = empty($contact_details->total_paid) ? 0 : $contact_details->total_paid;

            $payment_line->method = 'cash';
            $payment_line->paid_on = \Carbon::now()->toDateTimeString();

            $payment_types = $this->transactionUtil->payment_types(null, false, $business_id);

            //Accounts
            $accounts = $this->moduleUtil->accountsDropdown($business_id, true);

            return view('transaction_payment.pay_supplier_due_modal')
                        ->with(compact('contact_details', 'payment_types', 'payment_line', 'due_payment_type', 'ob_due', 'amount_formated', 'accounts'));
        }
    }
/**
 * Shows contact's payment due modal
 *
 * @param  int  $contact_id
 * @return \Illuminate\Http\Response
 */
public function getPayContactDue($contact_id)
{
    if (! (auth()->user()->can('sell.payments') || auth()->user()->can('purchase.payments'))) {
        abort(403, 'Unauthorized action.');
    }

    if (request()->ajax()) {
        $business_id = request()->session()->get('user.business_id');

        $due_payment_type = request()->input('type');
        
        // Validate payment type
        if (!in_array($due_payment_type, ['purchase', 'purchase_return', 'sell', 'sell_return'])) {
            return response()->json([
                'success' => false,
                'msg' => 'Invalid payment type'
            ], 400);
        }
        
        $query = Contact::where('contacts.id', $contact_id)
                        ->where('contacts.business_id', $business_id)
                        ->leftjoin('transactions AS t', 'contacts.id', '=', 't.contact_id');
        
        if ($due_payment_type == 'purchase') {
            $query->select(
                DB::raw("SUM(IF(t.type = 'purchase', final_total, 0)) as total_purchase"),
                DB::raw("SUM(IF(t.type = 'purchase', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as total_paid"),
                'contacts.name',
                'contacts.supplier_business_name',
                'contacts.id as contact_id'
            );
        } elseif ($due_payment_type == 'purchase_return') {
            $query->select(
                DB::raw("SUM(IF(t.type = 'purchase_return', final_total, 0)) as total_purchase_return"),
                DB::raw("SUM(IF(t.type = 'purchase_return', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as total_return_paid"),
                'contacts.name',
                'contacts.supplier_business_name',
                'contacts.id as contact_id'
            );
        } elseif ($due_payment_type == 'sell') {
            $query->select(
                DB::raw("SUM(IF(t.type = 'sell' AND t.status = 'final', final_total, 0)) as total_invoice"),
                DB::raw("SUM(IF(t.type = 'sell' AND t.status = 'final', (SELECT SUM(IF(is_return = 1,-1*amount,amount)) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as total_paid"),
                'contacts.name',
                'contacts.supplier_business_name',
                'contacts.id as contact_id'
            );
        } elseif ($due_payment_type == 'sell_return') {
            $query->select(
                DB::raw("SUM(IF(t.type = 'sell_return', final_total, 0)) as total_sell_return"),
                DB::raw("SUM(IF(t.type = 'sell_return', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as total_return_paid"),
                'contacts.name',
                'contacts.supplier_business_name',
                'contacts.id as contact_id'
            );
        }

        //Query for opening balance details
        $query->addSelect(
            DB::raw("SUM(IF(t.type = 'opening_balance', final_total, 0)) as opening_balance"),
            DB::raw("SUM(IF(t.type = 'opening_balance', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as opening_balance_paid")
        );
        
        $contact_details = $query->first();
        
        if (!$contact_details) {
            return response()->json([
                'success' => false,
                'msg' => 'Contact not found'
            ], 404);
        }

        $payment_line = new TransactionPayment();
        if ($due_payment_type == 'purchase') {
            $contact_details->total_purchase = empty($contact_details->total_purchase) ? 0 : $contact_details->total_purchase;
            $payment_line->amount = $contact_details->total_purchase - $contact_details->total_paid;
        } elseif ($due_payment_type == 'purchase_return') {
            $payment_line->amount = $contact_details->total_purchase_return - $contact_details->total_return_paid;
        } elseif ($due_payment_type == 'sell') {
            $contact_details->total_invoice = empty($contact_details->total_invoice) ? 0 : $contact_details->total_invoice;
            $payment_line->amount = $contact_details->total_invoice - $contact_details->total_paid;
        } elseif ($due_payment_type == 'sell_return') {
            $payment_line->amount = $contact_details->total_sell_return - $contact_details->total_return_paid;
        }

        //If opening balance due exists add to payment amount
        $contact_details->opening_balance = ! empty($contact_details->opening_balance) ? $contact_details->opening_balance : 0;
        $contact_details->opening_balance_paid = ! empty($contact_details->opening_balance_paid) ? $contact_details->opening_balance_paid : 0;
        $ob_due = $contact_details->opening_balance - $contact_details->opening_balance_paid;
        if ($ob_due > 0) {
            $payment_line->amount += $ob_due;
        }

        $amount_formated = $this->transactionUtil->num_f($payment_line->amount);
        $contact_details->total_paid = empty($contact_details->total_paid) ? 0 : $contact_details->total_paid;

        $payment_line->method = 'cash';
        $payment_line->paid_on = \Carbon::now()->toDateTimeString();

        $payment_types = $this->transactionUtil->payment_types(null, false, $business_id);

        //Accounts
        $accounts = $this->moduleUtil->accountsDropdown($business_id, true);

        return view('transaction_payment.pay_supplier_due_modal')
                    ->with(compact('contact_details', 'payment_types', 'payment_line', 'due_payment_type', 'ob_due', 'amount_formated', 'accounts'));
    }
    
    // If not ajax request, redirect to payments page
    return redirect()->action([TransactionPaymentController::class, 'index'], [
        'contact_id' => $contact_id,
        'type' => request()->input('type', 'sell')
    ]);
}
    public function getContactVoucherSummary(Request $request, $contact_id)
    {
        if (! (auth()->user()->can('sell.payments') || auth()->user()->can('purchase.payments'))) {
            abort(403, 'Unauthorized action.');
        }

        $due_payment_type = $request->input('type');
        $business_id = $request->session()->get('user.business_id');

        if (! in_array($due_payment_type, ['purchase', 'purchase_return', 'sell', 'sell_return'])) {
            abort(400, 'Invalid voucher type');
        }

        $query = Contact::where('contacts.id', $contact_id)
                        ->leftjoin('transactions AS t', 'contacts.id', '=', 't.contact_id');

        if ($due_payment_type == 'purchase') {
            $query->select(
                DB::raw("SUM(IF(t.type = 'purchase', final_total, 0)) as total_purchase"),
                DB::raw("SUM(IF(t.type = 'purchase', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as total_paid"),
                'contacts.name',
                'contacts.supplier_business_name',
                'contacts.id as contact_id'
            );
        } elseif ($due_payment_type == 'purchase_return') {
            $query->select(
                DB::raw("SUM(IF(t.type = 'purchase_return', final_total, 0)) as total_purchase_return"),
                DB::raw("SUM(IF(t.type = 'purchase_return', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as total_return_paid"),
                'contacts.name',
                'contacts.supplier_business_name',
                'contacts.id as contact_id'
            );
        } elseif ($due_payment_type == 'sell') {
            $query->select(
                DB::raw("SUM(IF(t.type = 'sell' AND t.status = 'final', final_total, 0)) as total_invoice"),
                DB::raw("SUM(IF(t.type = 'sell' AND t.status = 'final', (SELECT SUM(IF(is_return = 1,-1*amount,amount)) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as total_paid"),
                'contacts.name',
                'contacts.supplier_business_name',
                'contacts.id as contact_id'
            );
        } elseif ($due_payment_type == 'sell_return') {
            $query->select(
                DB::raw("SUM(IF(t.type = 'sell_return', final_total, 0)) as total_sell_return"),
                DB::raw("SUM(IF(t.type = 'sell_return', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as total_return_paid"),
                'contacts.name',
                'contacts.supplier_business_name',
                'contacts.id as contact_id'
            );
        }

        $query->addSelect(
            DB::raw("SUM(IF(t.type = 'opening_balance', final_total, 0)) as opening_balance"),
            DB::raw("SUM(IF(t.type = 'opening_balance', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as opening_balance_paid")
        )
        ->where('contacts.business_id', $business_id);

        $contact_details = $query->first();

        $due = 0;
        $paid = 0;
        $total = 0;

        if ($due_payment_type == 'purchase') {
            $total = empty($contact_details->total_purchase) ? 0 : $contact_details->total_purchase;
            $paid = empty($contact_details->total_paid) ? 0 : $contact_details->total_paid;
            $due = $total - $paid;
        } elseif ($due_payment_type == 'purchase_return') {
            $total = empty($contact_details->total_purchase_return) ? 0 : $contact_details->total_purchase_return;
            $paid = empty($contact_details->total_return_paid) ? 0 : $contact_details->total_return_paid;
            $due = $total - $paid;
        } elseif ($due_payment_type == 'sell') {
            $total = empty($contact_details->total_invoice) ? 0 : $contact_details->total_invoice;
            $paid = empty($contact_details->total_paid) ? 0 : $contact_details->total_paid;
            $due = $total - $paid;
        } elseif ($due_payment_type == 'sell_return') {
            $total = empty($contact_details->total_sell_return) ? 0 : $contact_details->total_sell_return;
            $paid = empty($contact_details->total_return_paid) ? 0 : $contact_details->total_return_paid;
            $due = $total - $paid;
        }

        $opening_balance = ! empty($contact_details->opening_balance) ? $contact_details->opening_balance : 0;
        $opening_balance_paid = ! empty($contact_details->opening_balance_paid) ? $contact_details->opening_balance_paid : 0;
        $ob_due = $opening_balance - $opening_balance_paid;

        $response = [
            'name' => $contact_details->name,
            'business_name' => $contact_details->supplier_business_name,
            'total' => $this->transactionUtil->num_f($total),
            'paid' => $this->transactionUtil->num_f($paid),
            'due' => $this->transactionUtil->num_f($due + ($ob_due > 0 ? $ob_due : 0)),
            'opening_balance' => $this->transactionUtil->num_f($opening_balance),
            'opening_balance_due' => $this->transactionUtil->num_f($ob_due > 0 ? $ob_due : 0),
        ];

        return $response;
    }

    /**
     * Adds Payments for Contact due
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postPayContactDue(Request $request)
    {
        if (! (auth()->user()->can('sell.payments') || auth()->user()->can('purchase.payments'))) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::beginTransaction();

            $business_id = request()->session()->get('business.id');
            $tp = $this->transactionUtil->payContact($request);

            $pos_settings = ! empty(session()->get('business.pos_settings')) ? json_decode(session()->get('business.pos_settings'), true) : [];
            $enable_cash_denomination_for_payment_methods = ! empty($pos_settings['enable_cash_denomination_for_payment_methods']) ? $pos_settings['enable_cash_denomination_for_payment_methods'] : [];
            //add cash denomination
            if (in_array($tp->method, $enable_cash_denomination_for_payment_methods) && ! empty($request->input('denominations')) && ! empty($pos_settings['enable_cash_denomination_on']) && $pos_settings['enable_cash_denomination_on'] == 'all_screens') {
                $denominations = [];

                foreach ($request->input('denominations') as $key => $value) {
                    if (! empty($value)) {
                        $denominations[] = [
                            'business_id' => $business_id,
                            'amount' => $key,
                            'total_count' => $value,
                        ];
                    }
                }

                if (! empty($denominations)) {
                    $tp->denominations()->createMany($denominations);
                }
            }

            DB::commit();
            $output = ['success' => true,
                'msg' => __('purchase.payment_added_success'),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => false,
                'msg' => 'File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage(),
            ];
        }

        return redirect()->back()->with(['status' => $output]);
    }

    /**
     * view details of single..,
     * payment.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function viewPayment($payment_id)
    {
        if (! (auth()->user()->can('sell.payments') ||
                auth()->user()->can('purchase.payments') ||
                auth()->user()->can('edit_sell_payment') ||
                auth()->user()->can('delete_sell_payment') ||
                auth()->user()->can('edit_purchase_payment') ||
                auth()->user()->can('delete_purchase_payment') ||
                auth()->user()->can('hms.add_booking_payment')
            )) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('business.id');
            $single_payment_line = TransactionPayment::findOrFail($payment_id);

            $transaction = null;
            if (! empty($single_payment_line->transaction_id)) {
                $transaction = Transaction::where('id', $single_payment_line->transaction_id)
                                ->with(['contact', 'location', 'transaction_for'])
                                ->first();
            } else {
                $child_payment = TransactionPayment::where('business_id', $business_id)
                        ->where('parent_id', $payment_id)
                        ->with(['transaction', 'transaction.contact', 'transaction.location', 'transaction.transaction_for'])
                        ->first();
                $transaction = ! empty($child_payment) ? $child_payment->transaction : null;
            }

            $payment_types = $this->transactionUtil->payment_types(null, false, $business_id);

            return view('transaction_payment.single_payment_view')
                    ->with(compact('single_payment_line', 'transaction', 'payment_types'));
        }
    }

    /**
     * Retrieves all the child payments of a parent payments
     * payment.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function showChildPayments($payment_id)
    {
        if (! (auth()->user()->can('sell.payments') ||
                auth()->user()->can('purchase.payments') ||
                auth()->user()->can('edit_sell_payment') ||
                auth()->user()->can('delete_sell_payment') ||
                auth()->user()->can('edit_purchase_payment') ||
                auth()->user()->can('delete_purchase_payment')
            )) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('business.id');

            $child_payments = TransactionPayment::where('business_id', $business_id)
                                                    ->where('parent_id', $payment_id)
                                                    ->with(['transaction', 'transaction.contact'])
                                                    ->get();

            $payment_types = $this->transactionUtil->payment_types(null, false, $business_id);

            return view('transaction_payment.show_child_payments')
                    ->with(compact('child_payments', 'payment_types'));
        }
    }

    /**
     * Retrieves list of all opening balance payments.
     *
     * @param  int  $contact_id
     * @return \Illuminate\Http\Response
     */
    public function getOpeningBalancePayments($contact_id)
    {
        if (! (auth()->user()->can('sell.payments') ||
                auth()->user()->can('purchase.payments') ||
                auth()->user()->can('edit_sell_payment') ||
                auth()->user()->can('delete_sell_payment') ||
                auth()->user()->can('edit_purchase_payment') ||
                auth()->user()->can('delete_purchase_payment')
            )) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('business.id');
        if (request()->ajax()) {
            $query = TransactionPayment::leftjoin('transactions as t', 'transaction_payments.transaction_id', '=', 't.id')
                ->where('t.business_id', $business_id)
                ->where('t.type', 'opening_balance')
                ->where('t.contact_id', $contact_id)
                ->where('transaction_payments.business_id', $business_id)
                ->select(
                    'transaction_payments.amount',
                    'method',
                    'paid_on',
                    'transaction_payments.payment_ref_no',
                    'transaction_payments.document',
                    'transaction_payments.id',
                    'cheque_number',
                    'card_transaction_number',
                    'bank_account_number'
                )
                ->groupBy('transaction_payments.id');

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('t.location_id', $permitted_locations);
            }

            return Datatables::of($query)
                ->editColumn('paid_on', '{{@format_datetime($paid_on)}}')
                ->editColumn('method', function ($row) {
                    $method = __('lang_v1.'.$row->method);
                    if ($row->method == 'cheque') {
                        $method .= '<br>('.__('lang_v1.cheque_no').': '.$row->cheque_number.')';
                    } elseif ($row->method == 'card') {
                        $method .= '<br>('.__('lang_v1.card_transaction_no').': '.$row->card_transaction_number.')';
                    } elseif ($row->method == 'bank_transfer') {
                        $method .= '<br>('.__('lang_v1.bank_account_no').': '.$row->bank_account_number.')';
                    } elseif ($row->method == 'custom_pay_1') {
                        $method = __('lang_v1.custom_payment_1').'<br>('.__('lang_v1.transaction_no').': '.$row->transaction_no.')';
                    } elseif ($row->method == 'custom_pay_2') {
                        $method = __('lang_v1.custom_payment_2').'<br>('.__('lang_v1.transaction_no').': '.$row->transaction_no.')';
                    } elseif ($row->method == 'custom_pay_3') {
                        $method = __('lang_v1.custom_payment_3').'<br>('.__('lang_v1.transaction_no').': '.$row->transaction_no.')';
                    }

                    return $method;
                })
                ->editColumn('amount', function ($row) {
                    return '<span class="display_currency paid-amount" data-orig-value="'.$row->amount.'" data-currency_symbol = true>'.$row->amount.'</span>';
                })
                ->addColumn('action', '<button type="button" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline  tw-dw-btn-primary view_payment" data-href="{{ action([\App\Http\Controllers\TransactionPaymentController::class, \'viewPayment\'], [$id]) }}"><i class="fas fa-eye"></i> @lang("messages.view")
                    </button> <button type="button" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline  tw-dw-btn-info edit_payment" 
                    data-href="{{action([\App\Http\Controllers\TransactionPaymentController::class, \'edit\'], [$id]) }}"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                    &nbsp; <button type="button" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline  tw-dw-btn-error delete_payment" 
                    data-href="{{ action([\App\Http\Controllers\TransactionPaymentController::class, \'destroy\'], [$id]) }}"
                    ><i class="fa fa-trash" aria-hidden="true"></i> @lang("messages.delete")</button> @if(!empty($document))<a href="{{asset("/uploads/documents/" . $document)}}" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline  tw-dw-btn-accent" download=""><i class="fa fa-download"></i> @lang("purchase.download_document")</a>@endif')
                ->rawColumns(['amount', 'method', 'action'])
                ->make(true);
        }
    }
}