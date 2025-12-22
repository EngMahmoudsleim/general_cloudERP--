<?php

namespace Modules\Accounting\Utils;

use Modules\Accounting\Entities\AccountingPostingRule;

class RuleResolver
{
    /**
     * Resolve an active posting rule for a business + location with fallback.
     */
    public function resolve(int $businessId, ?int $locationId, string $moduleKey, string $operationKey): ?AccountingPostingRule
    {
        $locations = [0];
        if (! is_null($locationId)) {
            array_unshift($locations, $locationId);
        }

        $businesses = [$businessId];
        if ($businessId !== 0) {
            $businesses[] = 0;
        }

        $query = AccountingPostingRule::where('module_key', $moduleKey)
            ->where('operation_key', $operationKey)
            ->where('is_active', true)
            ->whereIn('business_id', $businesses)
            ->whereIn('location_id', $locations)
            ->orderByRaw('business_id = ? DESC', [$businessId]);

        if (! is_null($locationId)) {
            $query->orderByRaw('location_id = ? DESC', [$locationId]);
        } else {
            $query->orderByDesc('location_id');
        }

        return $query->orderByDesc('id')->first();
    }
}
