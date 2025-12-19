<?php

namespace App\Http\Controllers;

use App\Contact;
use App\Transaction;
use App\TransactionPayment;
use App\Utils\TransactionUtil;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class VoucherController extends Controller
{
    protected $transactionUtil;

    public function __construct(TransactionUtil $transactionUtil)
    {
        $this->transactionUtil = $transactionUtil;
    }

    /**
     * Display voucher management page
     */
    public function index(Request $request)
    {
        if (!(auth()->user()->can('sell.payments') || auth()->user()->can('purchase.payments'))) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        
        $customer_contacts = Contact::customersDropdown($business_id, true, false);
        $supplier_contacts = Contact::suppliersDropdown($business_id, true, false);
        $payment_types = $this->transactionUtil->payment_types(null, true, $business_id);

        return view('voucher.index')
            ->with(compact('customer_contacts', 'supplier_contacts', 'payment_types'));
    }

    /**
     * Get vouchers for DataTable
     */
    public function getVouchers(Request $request)
    {
        if (!(auth()->user()->can('sell.payments') || auth()->user()->can('purchase.payments'))) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        
        $voucher_type = $request->get('voucher_type'); // 'receipt' or 'payment'
        $contact_id = $request->get('contact_id');
        $date_from = $request->get('date_from');
        $date_to = $request->get('date_to');
        $method = $request->get('method');

        // تحديد نوع المعاملة بناءً على نوع السند
        $transaction_types = [];
        if ($voucher_type == 'receipt') {
            $transaction_types = ['sell', 'sell_return'];
        } elseif ($voucher_type == 'payment') {
            $transaction_types = ['purchase', 'purchase_return'];
        }

        $query = TransactionPayment::leftJoin('transactions as t', 'transaction_payments.transaction_id', '=', 't.id')
            ->leftJoin('contacts as c', 'transaction_payments.payment_for', '=', 'c.id')
            ->where('transaction_payments.business_id', $business_id)
            ->whereIn('t.type', $transaction_types)
            ->select(
                'transaction_payments.id',
                'transaction_payments.amount',
                'transaction_payments.method',
                'transaction_payments.paid_on',
                'transaction_payments.payment_ref_no',
                'transaction_payments.transaction_no',
                'transaction_payments.is_return',
                'c.name as contact_name',
                'c.supplier_business_name',
                'c.type as contact_type',
                't.type as transaction_type',
                't.ref_no',
                't.invoice_no',
                't.id as transaction_id'
            );

        // تصفية حسب جهة الاتصال
        if (!empty($contact_id)) {
            $query->where('transaction_payments.payment_for', $contact_id);
        }

        // تصفية حسب التاريخ
        if (!empty($date_from)) {
            $query->whereDate('transaction_payments.paid_on', '>=', $date_from);
        }
        if (!empty($date_to)) {
            $query->whereDate('transaction_payments.paid_on', '<=', $date_to);
        }

        // تصفية حسب طريقة الدفع
        if (!empty($method)) {
            $query->where('transaction_payments.method', $method);
        }

        return DataTables::of($query)
            ->editColumn('paid_on', '{{@format_datetime($paid_on)}}')
            ->addColumn('contact', function ($row) {
                $name = $row->contact_name;
                if (!empty($row->supplier_business_name)) {
                    $name = $row->supplier_business_name . ' (' . $row->contact_name . ')';
                }
                return $name;
            })
            ->editColumn('amount', function ($row) {
                $amount = $row->amount;
                if ($row->is_return) {
                    $amount = -$amount;
                }
                return '<span class="display_currency" data-orig-value="' . $amount . '" data-currency_symbol="true">' . $amount . '</span>';
            })
            ->editColumn('method', function ($row) {
                $payment_types = $this->transactionUtil->payment_types(null, true);
                return $payment_types[$row->method] ?? $row->method;
            })
            ->addColumn('reference', function ($row) {
                if (!empty($row->invoice_no)) {
                    return $row->invoice_no;
                }
                return $row->ref_no ?? $row->payment_ref_no;
            })
            ->addColumn('action', function($row) {
                $html = '<button type="button" class="btn btn-xs btn-primary view_payment" data-href="' . 
                        action([TransactionPaymentController::class, 'viewPayment'], [$row->id]) . '">
                        <i class="fas fa-eye"></i></button>';
                
                // تحقق من الصلاحيات للإجراءات الإضافية
                if (auth()->user()->can('edit_sell_payment') || auth()->user()->can('edit_purchase_payment')) {
                    $html .= ' <button type="button" class="btn btn-xs btn-info edit_payment" data-href="' . 
                            action([TransactionPaymentController::class, 'edit'], [$row->id]) . '">
                            <i class="glyphicon glyphicon-edit"></i></button>';
                }
                
                if (auth()->user()->can('delete_sell_payment') || auth()->user()->can('delete_purchase_payment')) {
                    $html .= ' <button type="button" class="btn btn-xs btn-danger delete_payment" data-href="' . 
                            action([TransactionPaymentController::class, 'destroy'], [$row->id]) . '">
                            <i class="fa fa-trash"></i></button>';
                }
                
                return $html;
            })
            ->rawColumns(['amount', 'action'])
            ->make(true);
    }
}