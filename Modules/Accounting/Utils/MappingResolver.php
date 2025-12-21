<?php

namespace Modules\Accounting\Utils;

use App\BusinessLocation;
use Illuminate\Support\Arr;
use Modules\Accounting\Entities\AccountingAccount;
use Modules\Accounting\Entities\AccountingMapping;

class MappingResolver
{
    public const PAYMENT_KEY_MAP = [
        'PAYMENT_CASH' => 'cash',
        'PAYMENT_CARD' => 'card',
        'PAYMENT_CHEQUE' => 'cheque',
        'PAYMENT_BANK_TRANSFER' => 'bank_transfer',
        'PAYMENT_OTHER' => 'other',
        'PAYMENT_CUSTOM_1' => 'custom_pay_1',
        'PAYMENT_CUSTOM_2' => 'custom_pay_2',
        'PAYMENT_CUSTOM_3' => 'custom_pay_3',
        'PAYMENT_CUSTOM_4' => 'custom_pay_4',
        'PAYMENT_CUSTOM_5' => 'custom_pay_5',
        'PAYMENT_CUSTOM_6' => 'custom_pay_6',
        'PAYMENT_CUSTOM_7' => 'custom_pay_7',
    ];

    /**
     * Resolve a mapping key to an accounting account id.
     */
    public function resolve(int $businessId, ?int $locationId, string $key): ?int
    {
        $accountId = $this->resolveFromMappings($businessId, $locationId, $key);
        if (! is_null($accountId)) {
            return $accountId;
        }

        $accountId = $this->resolveFromDefaultPaymentAccounts($businessId, $locationId, $key);
        if (! is_null($accountId)) {
            return $accountId;
        }

        return $this->resolveFromAccountingDefaultMap($businessId, $locationId, $key);
    }

    /**
     * Create a normalized legacy key from an operation and field name.
     */
    public static function formatLegacyKey(string $operation, string $field): string
    {
        $operationPart = strtoupper(preg_replace('/[^A-Za-z0-9]+/', '_', $operation));
        $fieldPart = strtoupper($field);

        return 'LEGACY_'.$operationPart.'_'.$fieldPart;
    }

    /**
     * Convert a normalized legacy key into an operation + field pair.
     */
    public static function parseLegacyKey(string $key): ?array
    {
        if (! preg_match('/^((?:LEGACY_)?[A-Z0-9_]+)_(PAYMENT_ACCOUNT|DEPOSIT_TO)$/', $key, $matches)) {
            return null;
        }

        $operationRaw = $matches[1];
        $operation = preg_replace('/^LEGACY_/', '', $operationRaw);

        return [
            'operation' => strtolower($operation),
            'field' => strtolower($matches[2]),
        ];
    }

    private function resolveFromMappings(int $businessId, ?int $locationId, string $key): ?int
    {
        $query = AccountingMapping::where('business_id', $businessId)->where('key', $key);

        if (is_null($locationId)) {
            $query->where('location_id', 0)->orderByDesc('id');
        } else {
            $query->where(function ($q) use ($locationId) {
                $q->where('location_id', $locationId)
                    ->orWhere('location_id', 0);
            });
            $query->orderByRaw('(location_id = ?) DESC, id DESC', [$locationId]);
        }

        $mapping = $query->first();

        if (empty($mapping)) {
            return null;
        }

        return $this->isAccountActive($businessId, (int) $mapping->account_id) ? (int) $mapping->account_id : null;
    }

    private function resolveFromDefaultPaymentAccounts(int $businessId, ?int $locationId, string $key): ?int
    {
        if (! array_key_exists($key, self::PAYMENT_KEY_MAP)) {
            return null;
        }

        $location = $this->getLocation($businessId, $locationId);
        if (empty($location)) {
            return null;
        }

        $paymentConfig = json_decode($location->default_payment_accounts ?? '', true);
        if (! is_array($paymentConfig)) {
            return null;
        }

        $payload = $paymentConfig[self::PAYMENT_KEY_MAP[$key]] ?? null;
        if (! is_array($payload) || empty($payload['is_enabled'])) {
            return null;
        }

        $accountId = $payload['account'] ?? null;

        return $this->isAccountActive($businessId, $accountId) ? (int) $accountId : null;
    }

    /**
     * Legacy JSON fallback (accounting_default_map) is location-scoped only.
     * If no location is provided, no legacy fallback is attempted.
     */
    private function resolveFromAccountingDefaultMap(int $businessId, ?int $locationId, string $key): ?int
    {
        $parsed = self::parseLegacyKey($key);
        if (empty($parsed)) {
            return null;
        }

        $location = $this->getLocation($businessId, $locationId);
        if (empty($location)) {
            return null;
        }

        $defaultMap = json_decode($location->accounting_default_map ?? '', true);
        if (! is_array($defaultMap)) {
            return null;
        }

        $operation = $parsed['operation'];
        $field = $parsed['field'];

        $accountId = Arr::get($defaultMap, $operation.'.'.$field);

        return $this->isAccountActive($businessId, $accountId) ? (int) $accountId : null;
    }

    private function getLocation(int $businessId, ?int $locationId): ?BusinessLocation
    {
        if (empty($locationId)) {
            return null;
        }

        return BusinessLocation::where('business_id', $businessId)
            ->where('id', $locationId)
            ->first();
    }

    private function isAccountActive(int $businessId, $accountId): bool
    {
        if (empty($accountId)) {
            return false;
        }

        return AccountingAccount::where('business_id', $businessId)
            ->where('status', 'active')
            ->where('id', $accountId)
            ->exists();
    }
}
