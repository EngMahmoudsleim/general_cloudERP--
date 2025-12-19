<?php

namespace Modules\Accounting\Utils;

use App\Transaction;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Accounting\Entities\AccountingAccount;

class AccountingValidator
{
    public const BALANCE_TOLERANCE = 0.01;

    /**
     * Check if strict validation is enabled for a business.
     */
    public function isStrictMode(int $businessId): bool
    {
        $settings = (new AccountingUtil())->getAccountingSettings($businessId);

        return (bool) Arr::get($settings, 'STRICT_ACCOUNTING_VALIDATION', false);
    }

    /**
     * Validate that all referenced accounts are active and belong to the business.
     */
    public function validateAccountsActive(int $businessId, array $accountIds): array
    {
        $accountIds = array_unique(array_filter($accountIds));
        if (empty($accountIds)) {
            return [];
        }

        $active = AccountingAccount::where('business_id', $businessId)
            ->where('status', 'active')
            ->whereIn('id', $accountIds)
            ->pluck('id')
            ->toArray();

        return array_values(array_diff($accountIds, $active));
    }

    /**
     * Validate required mapping keys for an operation.
     */
    public function validateRequiredMappingKeys(
        string $operation,
        int $businessId,
        ?int $locationId,
        array $defaultMap,
        array $context = [],
        bool $strict = false
    ): array {
        $matrix = config("accounting.required_mappings.{$operation}", []);
        $missingKeys = [];
        $invalidAccounts = [];

        // Determine variant (e.g., purchase inventory vs expense)
        $variant = null;
        if ($operation === 'purchase') {
            $variant = Arr::get($context, 'purchase_variant');
            if (is_null($variant)) {
                $variant = $this->inferPurchaseVariant(Arr::get($context, 'transaction'));
            }
        }

        $requiredSets = [$matrix['required_keys'] ?? []];
        if ($operation === 'purchase' && !empty($matrix['variants'])) {
            if (!empty($variant) && !empty($matrix['variants'][$variant]['required_keys'])) {
                $requiredSets[] = $matrix['variants'][$variant]['required_keys'];
            } elseif (is_null($variant)) {
                // unknown purchase nature
                $missingKeys[] = 'purchase_variant';
            }
        }

        $requiredKeys = [];
        foreach ($requiredSets as $set) {
            $requiredKeys = array_merge($requiredKeys, $set);
        }

        foreach ($requiredKeys as $mapKey => $label) {
            if (!array_key_exists($mapKey, $defaultMap) || empty($defaultMap[$mapKey])) {
                $missingKeys[] = "{$mapKey}({$label})";
                continue;
            }
            $invalidAccounts = array_merge(
                $invalidAccounts,
                $this->validateAccountsActive($businessId, [(int) $defaultMap[$mapKey]])
            );
        }

        $result = [
            'missing_keys' => array_values(array_unique($missingKeys)),
            'invalid_accounts' => array_values(array_unique($invalidAccounts)),
        ];

        if (!empty($result['missing_keys']) || !empty($result['invalid_accounts'])) {
            $this->logWarning($businessId, $operation, Arr::get($context, 'doc_type'), Arr::get($context, 'doc_id'), $result, $strict);
        }

        return $result;
    }

    /**
     * Validate balance from payload lines (not yet persisted).
     */
    public function validateBalancePayload(array $lines): array
    {
        $debits = 0;
        $credits = 0;
        foreach ($lines as $line) {
            if (!isset($line['amount'], $line['type'])) {
                continue;
            }
            if ($line['type'] === 'debit') {
                $debits += (float) $line['amount'];
            } elseif ($line['type'] === 'credit') {
                $credits += (float) $line['amount'];
            }
        }

        $balanced = $this->isBalanced($debits, $credits);

        return compact('debits', 'credits', 'balanced');
    }

    /**
     * Validate balance from persisted lines.
     */
    public function validateBalancePersisted(int $mappingId): array
    {
        $row = DB::table('accounting_accounts_transactions')
            ->selectRaw("SUM(CASE WHEN type='debit' THEN amount ELSE 0 END) as debits")
            ->selectRaw("SUM(CASE WHEN type='credit' THEN amount ELSE 0 END) as credits")
            ->where('acc_trans_mapping_id', $mappingId)
            ->first();

        $debits = (float) ($row->debits ?? 0);
        $credits = (float) ($row->credits ?? 0);
        $balanced = $this->isBalanced($debits, $credits);

        return compact('debits', 'credits', 'balanced');
    }

    /**
     * Determine purchase variant from transaction lines.
     */
    public function inferPurchaseVariant(?Transaction $transaction): ?string
    {
        if (empty($transaction)) {
            return 'expense';
        }

        $transaction->loadMissing(['purchase_lines.product']);

        $hasInventory = false;
        $hasLines = false;
        foreach ($transaction->purchase_lines as $line) {
            $hasLines = true;
            if (!empty($line->product) && !empty($line->product->enable_stock)) {
                $hasInventory = true;
                break;
            }
        }

        if (!$hasLines) {
            return 'expense';
        }

        return $hasInventory ? 'inventory' : 'expense';
    }

    /**
     * Structured warning logging.
     */
    public function logWarning(
        int $businessId,
        string $operation,
        ?string $docType,
        $docId,
        array $details,
        bool $strict
    ): void {
        Log::warning('Accounting validation warning', [
            'business_id' => $businessId,
            'operation' => $operation,
            'doc_type' => $docType,
            'doc_id' => $docId,
            'missing_keys' => $details['missing_keys'] ?? [],
            'invalid_accounts' => $details['invalid_accounts'] ?? [],
            'strict_mode' => $strict,
        ]);
    }

    private function isBalanced(float $debits, float $credits): bool
    {
        return abs(round($debits - $credits, 2)) <= self::BALANCE_TOLERANCE;
    }
}
