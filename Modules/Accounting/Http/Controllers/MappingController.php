<?php

namespace Modules\Accounting\Http\Controllers;

use App\BusinessLocation;
use App\Utils\ModuleUtil;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Accounting\Entities\AccountingAccount;
use Modules\Accounting\Entities\AccountingMapping;
use Modules\Accounting\Entities\AccountingMappingKey;
use Modules\Accounting\Utils\MappingResolver;
use Modules\Accounting\Utils\RuleResolver;

class MappingController extends Controller
{
    protected ModuleUtil $moduleUtil;

    protected MappingResolver $mappingResolver;

    protected RuleResolver $ruleResolver;

    public function __construct(
        ModuleUtil $moduleUtil,
        MappingResolver $mappingResolver,
        RuleResolver $ruleResolver
    ) {
        $this->moduleUtil = $moduleUtil;
        $this->mappingResolver = $mappingResolver;
        $this->ruleResolver = $ruleResolver;
    }

    public function index(Request $request)
    {
        $businessId = (int) $request->session()->get('user.business_id');

        $this->authorizeAction($businessId);

        $locationId = (int) $request->input('location_id', $request->session()->get('user.default_location', 0));
        $locationId = max(0, $locationId);

        $locations = BusinessLocation::forDropdown($businessId, false, false, false);
        $locations->prepend(__('accounting::lang.global') !== 'accounting::lang.global' ? __('accounting::lang.global') : __('report.all_locations'), 0);

        $mappingKeys = AccountingMappingKey::orderBy('key')->get();
        $mappings = AccountingMapping::where('business_id', $businessId)
            ->whereIn('location_id', [$locationId, 0])
            ->orderByDesc('location_id')
            ->get();

        $selectedMappings = [];
        foreach ($mappings as $mapping) {
            if (! array_key_exists($mapping->key, $selectedMappings)) {
                $selectedMappings[$mapping->key] = (int) $mapping->account_id;
            }
        }

        $fallbackAccounts = [];
        foreach ($mappingKeys as $mappingKey) {
            if (! array_key_exists($mappingKey->key, $selectedMappings)) {
                $resolvedAccountId = $this->mappingResolver->resolve($businessId, $locationId ?: null, $mappingKey->key);
                if (! is_null($resolvedAccountId)) {
                    $fallbackAccounts[$mappingKey->key] = $resolvedAccountId;
                }
            }
        }

        $accountIds = array_unique(array_merge(array_values($selectedMappings), array_values($fallbackAccounts)));
        $accountsById = empty($accountIds)
            ? collect()
            : AccountingAccount::where('business_id', $businessId)
                ->whereIn('id', $accountIds)
                ->pluck('name', 'id');

        $groups = $this->groupMappingKeys($mappingKeys);

        $operations = [
            'sell_invoice' => 'sales',
            'sell_payment' => 'sales',
            'purchase_invoice' => 'purchases',
            'purchase_payment' => 'purchases',
            'expense' => 'expenses',
        ];

        $coverage = [];
        $requiredKeys = [];
        foreach ($operations as $operation => $moduleKey) {
            $coverage[$operation] = $this->computeCoverage(
                $businessId,
                $locationId,
                $moduleKey,
                $operation,
                $selectedMappings
            );
            foreach ($coverage[$operation]['required'] as $key) {
                $requiredKeys[$key] = true;
            }
        }

        $missingKeys = [];
        foreach ($coverage as $summary) {
            foreach ($summary['missing'] as $key) {
                $missingKeys[$key] = true;
            }
        }

        return view('accounting::mappings.index')->with(compact(
            'accountsById',
            'coverage',
            'groups',
            'locations',
            'locationId',
            'mappingKeys',
            'requiredKeys',
            'selectedMappings',
            'fallbackAccounts',
            'missingKeys'
        ));
    }

    public function store(Request $request)
    {
        $businessId = (int) $request->session()->get('user.business_id');

        $this->authorizeAction($businessId);

        $locationId = (int) $request->input('location_id', 0);
        $payload = $request->input('mappings', []);

        $payload = array_filter($payload, fn ($value) => ! empty($value));

        $mappingKeys = AccountingMappingKey::whereIn('key', array_keys($payload))
            ->get()
            ->keyBy('key');

        DB::transaction(function () use ($payload, $mappingKeys, $businessId, $locationId) {
            foreach ($payload as $key => $accountId) {
                $mappingKey = $mappingKeys[$key] ?? AccountingMappingKey::firstOrCreate(['key' => $key]);

                AccountingMapping::updateOrCreate(
                    [
                        'business_id' => $businessId,
                        'location_id' => $locationId,
                        'key' => $key,
                    ],
                    [
                        'account_id' => $accountId,
                        'mapping_key_id' => $mappingKey->id,
                    ]
                );
            }
        });

        return redirect()
            ->back()
            ->with('status', ['success' => 1, 'msg' => __('lang_v1.updated_success')]);
    }

    public function searchAccounts(Request $request)
    {
        $businessId = (int) $request->session()->get('user.business_id');
        $this->authorizeAction($businessId);

        $term = $request->input('q', '');

        $query = AccountingAccount::where('business_id', $businessId)
            ->where('status', 'active');

        if (! empty($term)) {
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                    ->orWhere('gl_code', 'like', "%{$term}%");
            });
        }

        $results = $query->orderBy('name')
            ->limit(20)
            ->get()
            ->map(function ($account) {
                $label = $account->name;
                if (! empty($account->gl_code)) {
                    $label .= ' ('.$account->gl_code.')';
                }

                return [
                    'id' => $account->id,
                    'text' => $label,
                ];
            });

        return [
            'results' => $results,
            'pagination' => ['more' => false],
        ];
    }

    protected function authorizeAction(int $businessId): void
    {
        if (! (
            auth()->user()->can('superadmin') ||
            (
                $this->moduleUtil->hasThePermissionInSubscription($businessId, 'accounting_module') &&
                auth()->user()->can('accounting.map_transactions')
            )
        )) {
            abort(403, 'Unauthorized action.');
        }
    }

    protected function groupMappingKeys($mappingKeys): array
    {
        $paymentKeys = array_keys(MappingResolver::PAYMENT_KEY_MAP);

        $groups = [
            'core' => [],
            'payments' => [],
            'sales' => [],
            'purchases' => [],
            'expenses' => [],
        ];

        foreach ($mappingKeys as $key) {
            if (in_array($key->key, $paymentKeys)) {
                $groups['payments'][] = $key;
                continue;
            }

            switch ($key->key) {
                case 'AR_CONTROL':
                case 'REVENUE_GENERAL':
                case 'SALES_RETURNS':
                    $groups['sales'][] = $key;
                    break;
                case 'AP_CONTROL':
                case 'INVENTORY_ASSET':
                    $groups['purchases'][] = $key;
                    break;
                case 'COGS':
                    $groups['expenses'][] = $key;
                    break;
                default:
                    $groups['core'][] = $key;
            }
        }

        return $groups;
    }

    protected function computeCoverage(
        int $businessId,
        int $locationId,
        string $moduleKey,
        string $operationKey,
        array $selectedMappings
    ): array {
        $rule = $this->ruleResolver->resolve($businessId, $locationId ?: null, $moduleKey, $operationKey);

        $required = [];
        $missing = [];

        if (empty($rule)) {
            return compact('required', 'missing');
        }

        foreach ($rule->lines as $line) {
            if (! empty($line->mappingKey)) {
                $required[] = $line->mappingKey->key;
                continue;
            }

            if ($line->context_key === 'payment_account') {
                $required = array_merge($required, array_keys(MappingResolver::PAYMENT_KEY_MAP));
            }
        }

        $required = array_values(array_unique($required));

        foreach ($required as $key) {
            $accountId = $selectedMappings[$key] ?? $this->mappingResolver->resolve($businessId, $locationId ?: null, $key);
            if (empty($accountId)) {
                $missing[] = $key;
            }
        }

        return compact('required', 'missing');
    }
}
