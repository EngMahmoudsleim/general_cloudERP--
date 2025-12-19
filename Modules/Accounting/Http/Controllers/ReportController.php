<?php

namespace Modules\Accounting\Http\Controllers;

use App\BusinessLocation;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use DB;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Accounting\Entities\AccountingAccount;
use Modules\Accounting\Utils\AccountingUtil;

class ReportController extends Controller
{
    protected $accountingUtil;

    protected $businessUtil;

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct(AccountingUtil $accountingUtil, BusinessUtil $businessUtil,
    ModuleUtil $moduleUtil)
    {
        $this->accountingUtil = $accountingUtil;
        $this->businessUtil = $businessUtil;
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $business_id = request()->session()->get('user.business_id');

        if (! (auth()->user()->can('superadmin') ||
            $this->moduleUtil->hasThePermissionInSubscription($business_id, 'accounting_module')) ||
            ! (auth()->user()->can('accounting.view_reports'))) {
            abort(403, 'Unauthorized action.');
        }

        $first_account = AccountingAccount::where('business_id', $business_id)
                            ->where('status', 'active')
                            ->first();
        $ledger_url = null;
        if (! empty($first_account)) {
            $ledger_url = route('accounting.ledger', $first_account);
        }

        return view('accounting::report.index')
            ->with(compact('ledger_url'));
    }

    /**
     * Trial Balance
     *
     * @return Response
     */
    public function trialBalance()
    {
        $business_id = request()->session()->get('user.business_id');

        if (! (auth()->user()->can('superadmin') ||
            $this->moduleUtil->hasThePermissionInSubscription($business_id, 'accounting_module')) ||
            ! (auth()->user()->can('accounting.view_reports'))) {
            abort(403, 'Unauthorized action.');
        }

        if (! empty(request()->start_date) && ! empty(request()->end_date)) {
            $start_date = request()->start_date;
            $end_date = request()->end_date;
        } else {
            $fy = $this->businessUtil->getCurrentFinancialYear($business_id);
            $start_date = $fy['start'];
            $end_date = $fy['end'];
        }

        $accounts = AccountingAccount::join('accounting_accounts_transactions as AAT',
                                'AAT.accounting_account_id', '=', 'accounting_accounts.id')
                            ->where('business_id', $business_id)
                            ->whereDate('AAT.operation_date', '>=', $start_date)
                            ->whereDate('AAT.operation_date', '<=', $end_date)
                            ->select(
                                DB::raw("SUM(IF(AAT.type = 'credit', AAT.amount, 0)) as credit_balance"),
                                DB::raw("SUM(IF(AAT.type = 'debit', AAT.amount, 0)) as debit_balance"),
                                'accounting_accounts.name'
                            )
                            ->groupBy('accounting_accounts.name')
                            ->get();

        return view('accounting::report.trial_balance')
            ->with(compact('accounts', 'start_date', 'end_date'));
    }

    /**
     * Trial Balance
     *
     * @return Response
     */
    public function balanceSheet()
    {
        $business_id = request()->session()->get('user.business_id');

        if (! (auth()->user()->can('superadmin') ||
            $this->moduleUtil->hasThePermissionInSubscription($business_id, 'accounting_module')) ||
            ! (auth()->user()->can('accounting.view_reports'))) {
            abort(403, 'Unauthorized action.');
        }

        if (! empty(request()->start_date) && ! empty(request()->end_date)) {
            $start_date = request()->start_date;
            $end_date = request()->end_date;
        } else {
            $fy = $this->businessUtil->getCurrentFinancialYear($business_id);
            $start_date = $fy['start'];
            $end_date = $fy['end'];
        }

        $balance_formula = $this->accountingUtil->balanceFormula();

        $assets = AccountingAccount::join('accounting_accounts_transactions as AAT',
                                'AAT.accounting_account_id', '=', 'accounting_accounts.id')
                    ->join('accounting_account_types as AATP',
                                'AATP.id', '=', 'accounting_accounts.account_sub_type_id')
                    ->whereDate('AAT.operation_date', '>=', $start_date)
                    ->whereDate('AAT.operation_date', '<=', $end_date)
                    ->select(DB::raw($balance_formula), 'accounting_accounts.name', 'AATP.name as sub_type')
                    ->where('accounting_accounts.business_id', $business_id)
                    ->whereIn('accounting_accounts.account_primary_type', ['asset'])
                    ->groupBy('accounting_accounts.name')
                    ->get();

        $liabilities = AccountingAccount::join('accounting_accounts_transactions as AAT',
                                'AAT.accounting_account_id', '=', 'accounting_accounts.id')
                    ->join('accounting_account_types as AATP',
                                'AATP.id', '=', 'accounting_accounts.account_sub_type_id')
                    ->whereDate('AAT.operation_date', '>=', $start_date)
                    ->whereDate('AAT.operation_date', '<=', $end_date)
                    ->select(DB::raw($balance_formula), 'accounting_accounts.name', 'AATP.name as sub_type')
                    ->where('accounting_accounts.business_id', $business_id)
                    ->whereIn('accounting_accounts.account_primary_type', ['liability'])
                    ->groupBy('accounting_accounts.name')
                    ->get();

        $equities = AccountingAccount::join('accounting_accounts_transactions as AAT',
                                'AAT.accounting_account_id', '=', 'accounting_accounts.id')
                    ->join('accounting_account_types as AATP',
                                'AATP.id', '=', 'accounting_accounts.account_sub_type_id')
                    ->whereDate('AAT.operation_date', '>=', $start_date)
                    ->whereDate('AAT.operation_date', '<=', $end_date)
                    ->select(DB::raw($balance_formula), 'accounting_accounts.name', 'AATP.name as sub_type')
                    ->where('accounting_accounts.business_id', $business_id)
                    ->whereIn('accounting_accounts.account_primary_type', ['equity'])
                    ->groupBy('accounting_accounts.name')
                    ->get();

        return view('accounting::report.balance_sheet')
            ->with(compact('assets', 'liabilities', 'equities', 'start_date', 'end_date'));
    }


// في ملف: ReportController.php

public function incomeStatement()
{
    $business_id = request()->session()->get('user.business_id');

    if (! (auth()->user()->can('superadmin') ||
        $this->moduleUtil->hasThePermissionInSubscription($business_id, 'accounting_module')) ||
        ! (auth()->user()->can('accounting.view_reports'))) {
        abort(403, 'Unauthorized action.');
    }

    if (! empty(request()->start_date) && ! empty(request()->end_date)) {
        $start_date = request()->start_date;
        $end_date = request()->end_date;
    } else {
        $fy = $this->businessUtil->getCurrentFinancialYear($business_id);
        $start_date = $fy['start'];
        $end_date = $fy['end'];
    }

    $balance_formula = $this->accountingUtil->balanceFormula();

    // ==========================================
    // 📊 الإيرادات (Revenues)
    // ==========================================
    
    // 1. المبيعات الإجمالية (Gross Sales)
    $sales_accounts = $this->getAccountsForIncomeStatement(
        $business_id, 
        ['income'], 
        $balance_formula, 
        $start_date, 
        $end_date,
        ['Sales', 'Sales - retail', 'Sales - wholesale', 'Sales of Product Income', 'Revenue - General']
    );
    
    // 2. مرتجعات وخصومات المبيعات (Sales Returns & Allowances)
    $sales_returns_accounts = $this->getAccountsForIncomeStatement(
        $business_id, 
        ['income'], 
        $balance_formula, 
        $start_date, 
        $end_date,
        ['Sales Returns', 'Sales Returns and Allowances', 'Sales Allowances', 'Discounts given']
    );

    // 3. الإيرادات الأخرى (Other Income)
    $other_income_accounts = $this->getAccountsForIncomeStatement(
        $business_id, 
        ['income'], 
        $balance_formula, 
        $start_date, 
        $end_date,
        ['Interest income', 'Dividend income', 'Other operating income']
    );

    // ==========================================
    // 💰 التكاليف والمصروفات (Costs & Expenses)
    // ==========================================
    
    // 4. تكلفة البضاعة المباعة (Cost of Goods Sold)
    $cogs_accounts = $this->getAccountsForIncomeStatement(
        $business_id, 
        ['expense', 'expenses'], 
        $balance_formula, 
        $start_date, 
        $end_date,
        [
            'Cost of sales', 
            'Materials - COS', 
            'Direct labour - COS', 
            'Overhead - COS',
            'Freight and delivery - COS',
            'Change in inventory - COS',
            'Subcontractors - COS',
            'Other - COS'
        ]
    );

    // 5. مرتجعات المشتريات (Purchase Returns)
    $purchase_returns_accounts = $this->getAccountsForIncomeStatement(
        $business_id, 
        ['expense', 'expenses'], 
        $balance_formula, 
        $start_date, 
        $end_date,
        ['Purchase Returns', 'Purchase Returns and Allowances', 'Purchase Allowances', 'Purchase Discounts']
    );

    // 6. المصروفات التشغيلية (Operating Expenses)
    $operating_expense_accounts = $this->getAccountsForIncomeStatement(
        $business_id, 
        ['expense', 'expenses'], 
        $balance_formula, 
        $start_date, 
        $end_date,
        null, // نريد كل المصروفات ماعدا COGS
        array_merge(
            ['Cost of sales', 'Materials - COS', 'Direct labour - COS', 'Overhead - COS', 'Freight and delivery - COS', 'Change in inventory - COS', 'Subcontractors - COS', 'Other - COS'],
            ['Interest expense', 'Income tax expense', 'Loss on disposal of assets', 'Loss on discontinued operations, net of tax']
        )
    );

    // 7. المصروفات الأخرى (Other Expenses)
    $other_expense_accounts = $this->getAccountsForIncomeStatement(
        $business_id, 
        ['expense', 'expenses'], 
        $balance_formula, 
        $start_date, 
        $end_date,
        ['Interest expense', 'Loss on disposal of assets', 'Loss on discontinued operations, net of tax']
    );

    // ==========================================
    // 🧮 الحسابات (Calculations)
    // ==========================================
    
    // إجمالي المبيعات
    $gross_sales = array_sum(array_column($this->formatAccountsForStatement($sales_accounts), 'total'));
    
    // مرتجعات المبيعات
    $sales_returns = array_sum(array_column($this->formatAccountsForStatement($sales_returns_accounts), 'total'));
    
    // صافي المبيعات = إجمالي المبيعات - المرتجعات
    $net_sales = $gross_sales - abs($sales_returns);
    
    // تكلفة البضاعة المباعة
    $cogs = array_sum(array_column($this->formatAccountsForStatement($cogs_accounts), 'total'));
    
    // مرتجعات المشتريات (تخفض من التكلفة)
    $purchase_returns = array_sum(array_column($this->formatAccountsForStatement($purchase_returns_accounts), 'total'));
    
    // صافي تكلفة البضاعة = التكلفة - مرتجعات المشتريات
    $net_cogs = $cogs - abs($purchase_returns);
    
    // إجمالي الربح = صافي المبيعات - صافي التكلفة
    $gross_profit = $net_sales - $net_cogs;
    
    // المصروفات التشغيلية
    $operating_expenses = array_sum(array_column($this->formatAccountsForStatement($operating_expense_accounts), 'total'));
    
    // الربح التشغيلي = إجمالي الربح - المصروفات التشغيلية
    $operating_profit = $gross_profit - $operating_expenses;
    
    // الإيرادات الأخرى
    $other_income = array_sum(array_column($this->formatAccountsForStatement($other_income_accounts), 'total'));
    
    // المصروفات الأخرى
    $other_expenses = array_sum(array_column($this->formatAccountsForStatement($other_expense_accounts), 'total'));
    
    // صافي الربح قبل الضريبة
    $profit_before_tax = $operating_profit + $other_income - $other_expenses;
    
    // ضريبة الدخل
    $income_tax_accounts = $this->getAccountsForIncomeStatement(
        $business_id, 
        ['expense', 'expenses'], 
        $balance_formula, 
        $start_date, 
        $end_date,
        ['Income tax expense']
    );
    $income_tax = array_sum(array_column($this->formatAccountsForStatement($income_tax_accounts), 'total'));
    
    // صافي الربح بعد الضريبة
    $net_profit = $profit_before_tax - $income_tax;

    // تنسيق البيانات للعرض
    $sales_details = $this->formatAccountsForStatement($sales_accounts);
    $sales_returns_details = $this->formatAccountsForStatement($sales_returns_accounts);
    $other_income_details = $this->formatAccountsForStatement($other_income_accounts);
    $cogs_details = $this->formatAccountsForStatement($cogs_accounts);
    $purchase_returns_details = $this->formatAccountsForStatement($purchase_returns_accounts);
    $operating_expense_details = $this->formatAccountsForStatement($operating_expense_accounts);
    $other_expense_details = $this->formatAccountsForStatement($other_expense_accounts);
    $income_tax_details = $this->formatAccountsForStatement($income_tax_accounts);

    return view('accounting::report.income_statement')
        ->with(compact(
            'sales_details',
            'sales_returns_details',
            'other_income_details',
            'cogs_details',
            'purchase_returns_details',
            'operating_expense_details',
            'other_expense_details',
            'income_tax_details',
            'gross_sales',
            'sales_returns',
            'net_sales',
            'cogs',
            'purchase_returns',
            'net_cogs',
            'gross_profit',
            'operating_expenses',
            'operating_profit',
            'other_income',
            'other_expenses',
            'profit_before_tax',
            'income_tax',
            'net_profit',
            'start_date',
            'end_date'
        ));
}

// دالة مساعدة محسّنة
private function getAccountsForIncomeStatement(
    $business_id, 
    $primary_types, 
    $balance_formula, 
    $start_date, 
    $end_date,
    $include_names = null,  // أسماء الحسابات المطلوبة
    $exclude_names = null   // أسماء الحسابات المستبعدة
) {
    $query = AccountingAccount::join('accounting_accounts_transactions as AAT',
                            'AAT.accounting_account_id', '=', 'accounting_accounts.id')
            ->leftJoin('accounting_account_types as AATP',
                            'AATP.id', '=', 'accounting_accounts.account_sub_type_id')
            ->where('accounting_accounts.business_id', $business_id)
            ->whereDate('AAT.operation_date', '>=', $start_date)
            ->whereDate('AAT.operation_date', '<=', $end_date)
            ->whereIn('accounting_accounts.account_primary_type', $primary_types)
            ->select(
                DB::raw($balance_formula),
                'accounting_accounts.id',
                'accounting_accounts.name',
                'AATP.name as sub_type',
                'AATP.business_id as sub_type_business_id'
            );

    // تصفية الحسابات المطلوبة
    if ($include_names !== null) {
        $query->where(function($q) use ($include_names) {
            foreach ($include_names as $name) {
                $q->orWhere('accounting_accounts.name', 'LIKE', '%' . $name . '%');
            }
        });
    }

    // استبعاد حسابات معينة
    if ($exclude_names !== null) {
        foreach ($exclude_names as $name) {
            $query->where('accounting_accounts.name', 'NOT LIKE', '%' . $name . '%');
        }
    }

    return $query->groupBy('accounting_accounts.id', 'accounting_accounts.name', 'AATP.name', 'AATP.business_id')
                 ->get();
}
    public function accountReceivableAgeingReport()
    {
        $business_id = request()->session()->get('user.business_id');

        if (! (auth()->user()->can('superadmin') ||
            $this->moduleUtil->hasThePermissionInSubscription($business_id, 'accounting_module')) ||
            ! (auth()->user()->can('accounting.view_reports'))) {
            abort(403, 'Unauthorized action.');
        }

        $location_id = request()->input('location_id', null);

        $report_details = $this->accountingUtil->getAgeingReport($business_id, 'sell', 'contact', $location_id);

        $business_locations = BusinessLocation::forDropdown($business_id, true);

        return view('accounting::report.account_receivable_ageing_report')
        ->with(compact('report_details', 'business_locations'));
    }

    public function accountPayableAgeingReport()
    {
        $business_id = request()->session()->get('user.business_id');

        if (! (auth()->user()->can('superadmin') ||
            $this->moduleUtil->hasThePermissionInSubscription($business_id, 'accounting_module')) ||
            ! (auth()->user()->can('accounting.view_reports'))) {
            abort(403, 'Unauthorized action.');
        }

        $location_id = request()->input('location_id', null);
        $report_details = $this->accountingUtil->getAgeingReport($business_id, 'purchase', 'contact',
        $location_id);
        $business_locations = BusinessLocation::forDropdown($business_id, true);

        return view('accounting::report.account_payable_ageing_report')
        ->with(compact('report_details', 'business_locations'));
    }

    public function accountReceivableAgeingDetails()
    {
        $business_id = request()->session()->get('user.business_id');

        if (! (auth()->user()->can('superadmin') ||
            $this->moduleUtil->hasThePermissionInSubscription($business_id, 'accounting_module')) ||
            ! (auth()->user()->can('accounting.view_reports'))) {
            abort(403, 'Unauthorized action.');
        }

        $location_id = request()->input('location_id', null);

        $report_details = $this->accountingUtil->getAgeingReport($business_id, 'sell', 'due_date',
        $location_id);

        $business_locations = BusinessLocation::forDropdown($business_id, true);

        return view('accounting::report.account_receivable_ageing_details')
        ->with(compact('business_locations', 'report_details'));
    }

    public function accountPayableAgeingDetails()
    {
        $business_id = request()->session()->get('user.business_id');

        if (! (auth()->user()->can('superadmin') ||
            $this->moduleUtil->hasThePermissionInSubscription($business_id, 'accounting_module')) ||
            ! (auth()->user()->can('accounting.view_reports'))) {
            abort(403, 'Unauthorized action.');
        }

        $location_id = request()->input('location_id', null);

        $report_details = $this->accountingUtil->getAgeingReport($business_id, 'purchase', 'due_date',
        $location_id);

        $business_locations = BusinessLocation::forDropdown($business_id, true);

        return view('accounting::report.account_payable_ageing_details')
        ->with(compact('business_locations', 'report_details'));
    }

    private function getAccountsForIncomeStatement0000($business_id, $primary_types, $balance_formula, $start_date, $end_date)
    {
        return AccountingAccount::join('accounting_accounts_transactions as AAT',
                                'AAT.accounting_account_id', '=', 'accounting_accounts.id')
                ->leftJoin('accounting_account_types as AATP',
                                'AATP.id', '=', 'accounting_accounts.account_sub_type_id')
                ->where('accounting_accounts.business_id', $business_id)
                ->whereDate('AAT.operation_date', '>=', $start_date)
                ->whereDate('AAT.operation_date', '<=', $end_date)
                ->whereIn('accounting_accounts.account_primary_type', $primary_types)
                ->select(
                    DB::raw($balance_formula),
                    'accounting_accounts.id',
                    'accounting_accounts.name',
                    'AATP.name as sub_type',
                    'AATP.business_id as sub_type_business_id'
                )
                ->groupBy('accounting_accounts.id', 'accounting_accounts.name', 'AATP.name', 'AATP.business_id')
                ->get();
    }

    private function formatAccountsForStatement($accounts)
    {
        $grouped_accounts = [];

        foreach ($accounts as $account) {
            if (! empty($account->sub_type)) {
                $sub_type = ! empty($account->sub_type_business_id) ? $account->sub_type : __('accounting::lang.'.$account->sub_type);
            } else {
                $sub_type = __('lang_v1.others');
            }

            $grouped_accounts[$sub_type]['accounts'][] = $account;
            $grouped_accounts[$sub_type]['total'] = ($grouped_accounts[$sub_type]['total'] ?? 0) + $account->balance;
        }

        return $grouped_accounts;
    }
}
