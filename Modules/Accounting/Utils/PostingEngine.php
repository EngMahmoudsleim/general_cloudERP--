<?php

namespace Modules\Accounting\Utils;

use App\Transaction;
use App\TransactionPayment;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Modules\Accounting\Entities\AccountingAccTransMapping;
use Modules\Accounting\Entities\AccountingAccountsTransaction;
use Modules\Accounting\Entities\AccountingPostingRule;
use Modules\Accounting\Entities\AccountingPostingRuleLine;

class PostingEngine
{
    protected MappingResolver $mappingResolver;
    protected RuleResolver $ruleResolver;
    protected AccountingValidator $validator;

    public function __construct(
        ?MappingResolver $mappingResolver = null,
        ?RuleResolver $ruleResolver = null,
        ?AccountingValidator $validator = null
    ) {
        $this->mappingResolver = $mappingResolver ?? new MappingResolver();
        $this->ruleResolver = $ruleResolver ?? new RuleResolver();
        $this->validator = $validator ?? new AccountingValidator();
    }

    public function postSellInvoice(Transaction $transaction): array
    {
        $transaction->loadMissing('location');

        return $this->post([
            'module_key' => 'sales',
            'operation_key' => 'sell_invoice',
            'business_id' => (int) $transaction->business_id,
            'location_id' => $transaction->location_id ?? 0,
            'source_type' => 'transaction',
            'source_id' => $transaction->id,
            'transaction_id' => $transaction->id,
            'amount' => (float) $transaction->final_total,
            'reference' => $transaction->invoice_no ?? $transaction->ref_no ?? ('sell_'.$transaction->id),
            'operation_date' => $transaction->transaction_date ?? Carbon::now(),
            'created_by' => $transaction->created_by,
            'note' => $transaction->additional_notes ?? null,
        ]);
    }

    public function postSellPayment(TransactionPayment $payment): array
    {
        $payment->loadMissing('transaction');
        $paymentKey = $this->mapPaymentMethodToKey($payment->method);
        $businessId = (int) ($payment->business_id ?? $payment->transaction->business_id ?? 0);
        $locationId = $payment->transaction->location_id ?? 0;

        return $this->post([
            'module_key' => 'sales',
            'operation_key' => 'sell_payment',
            'business_id' => $businessId,
            'location_id' => $locationId,
            'source_type' => 'transaction_payment',
            'source_id' => $payment->id,
            'transaction_payment_id' => $payment->id,
            'transaction_id' => $payment->transaction_id,
            'amount' => (float) $payment->amount,
            'reference' => $payment->payment_ref_no ?? ('sell_payment_'.$payment->id),
            'operation_date' => $payment->paid_on ?? Carbon::now(),
            'created_by' => $payment->created_by,
            'note' => $payment->note ?? null,
            'payment_account' => $paymentKey,
        ]);
    }

    public function postPurchaseInvoice(Transaction $transaction): array
    {
        $transaction->loadMissing('location');

        return $this->post([
            'module_key' => 'purchases',
            'operation_key' => 'purchase_invoice',
            'business_id' => (int) $transaction->business_id,
            'location_id' => $transaction->location_id ?? 0,
            'source_type' => 'transaction',
            'source_id' => $transaction->id,
            'transaction_id' => $transaction->id,
            'amount' => (float) $transaction->final_total,
            'reference' => $transaction->ref_no ?? ('purchase_'.$transaction->id),
            'operation_date' => $transaction->transaction_date ?? Carbon::now(),
            'created_by' => $transaction->created_by,
            'note' => $transaction->additional_notes ?? null,
        ]);
    }

    public function postPurchasePayment(TransactionPayment $payment): array
    {
        $payment->loadMissing('transaction');
        $paymentKey = $this->mapPaymentMethodToKey($payment->method);
        $businessId = (int) ($payment->business_id ?? $payment->transaction->business_id ?? 0);
        $locationId = $payment->transaction->location_id ?? 0;

        return $this->post([
            'module_key' => 'purchases',
            'operation_key' => 'purchase_payment',
            'business_id' => $businessId,
            'location_id' => $locationId,
            'source_type' => 'transaction_payment',
            'source_id' => $payment->id,
            'transaction_payment_id' => $payment->id,
            'transaction_id' => $payment->transaction_id,
            'amount' => (float) $payment->amount,
            'reference' => $payment->payment_ref_no ?? ('purchase_payment_'.$payment->id),
            'operation_date' => $payment->paid_on ?? Carbon::now(),
            'created_by' => $payment->created_by,
            'note' => $payment->note ?? null,
            'payment_account' => $paymentKey,
        ]);
    }

    public function postExpense(Transaction $transaction): array
    {
        $transaction->loadMissing('location');
        $paymentKey = $this->mapPaymentMethodToKey($transaction->payment_method ?? 'other');

        return $this->post([
            'module_key' => 'expenses',
            'operation_key' => 'expense',
            'business_id' => (int) $transaction->business_id,
            'location_id' => $transaction->location_id ?? 0,
            'source_type' => 'transaction',
            'source_id' => $transaction->id,
            'transaction_id' => $transaction->id,
            'amount' => (float) $transaction->final_total,
            'reference' => $transaction->ref_no ?? ('expense_'.$transaction->id),
            'operation_date' => $transaction->transaction_date ?? Carbon::now(),
            'created_by' => $transaction->created_by,
            'note' => $transaction->additional_notes ?? null,
            'payment_account' => $paymentKey,
        ]);
    }

    /**
     * Core posting routine.
     */
    public function post(array $context): array
    {
        $businessId = (int) Arr::get($context, 'business_id');
        $locationId = Arr::get($context, 'location_id');
        $moduleKey = Arr::get($context, 'module_key', 'accounting');
        $operationKey = Arr::get($context, 'operation_key');
        $strict = $this->validator->isStrictMode($businessId);

        $rule = $this->ruleResolver->resolve($businessId, $locationId, $moduleKey, $operationKey);
        if (empty($rule)) {
            $this->validator->logWarning($businessId, $operationKey, Arr::get($context, 'source_type'), Arr::get($context, 'source_id'), [
                'missing_keys' => ['posting_rule_missing'],
                'invalid_accounts' => [],
            ], $strict);

            if ($strict) {
                throw new \Exception(__('accounting::lang.mapping_required_error'));
            }

            return [
                'success' => true,
                'posted' => false,
                'warnings' => ['posting_rule_missing' => true],
            ];
        }

        $lines = $this->buildLines($rule, $context, $businessId, $locationId);

        if (!empty($lines['warnings'])) {
            if (!$strict) {
                $this->validator->logWarning(
                    $businessId,
                    $operationKey,
                    Arr::get($context, 'source_type'),
                    Arr::get($context, 'source_id'),
                    [
                        'missing_keys' => $lines['warnings']['missing_keys'] ?? [],
                        'invalid_accounts' => $lines['warnings']['invalid_accounts'] ?? [],
                    ],
                    $strict
                );
            }

            if ($strict) {
                throw new \Exception(__('accounting::lang.mapping_required_error'));
            }

            return ['success' => true, 'posted' => false, 'warnings' => $lines['warnings']];
        }

        $balance = $this->validator->validateBalancePayload($lines['rows']);
        if (!$balance['balanced']) {
            if (!$strict) {
                $this->validator->logWarning(
                    $businessId,
                    $operationKey,
                    Arr::get($context, 'source_type'),
                    Arr::get($context, 'source_id'),
                    ['missing_keys' => [], 'invalid_accounts' => []],
                    $strict
                );
            }

            if ($strict) {
                throw new \Exception(__('accounting::lang.balance_error'));
            }

            return ['success' => true, 'posted' => false, 'warnings' => ['balance' => $balance]];
        }

        $result = DB::transaction(function () use ($context, $lines, $operationKey, $strict) {
            $mapping = $this->getOrCreateMapping($context, $operationKey, true);

            AccountingAccountsTransaction::where('acc_trans_mapping_id', $mapping->id)->delete();

            foreach ($lines['rows'] as $row) {
                AccountingAccountsTransaction::create(array_merge($row, [
                    'acc_trans_mapping_id' => $mapping->id,
                    'transaction_id' => Arr::get($context, 'transaction_id'),
                    'transaction_payment_id' => Arr::get($context, 'transaction_payment_id'),
                    'created_by' => Arr::get($context, 'created_by', 0),
                    'operation_date' => Arr::get($context, 'operation_date', Carbon::now()),
                    'note' => Arr::get($context, 'note'),
                ]));
            }

            $persistedBalance = $this->validator->validateBalancePersisted($mapping->id);
            if ($strict && !$persistedBalance['balanced']) {
                throw new \Exception(__('accounting::lang.balance_error'));
            }

            return $mapping;
        });

        return [
            'success' => true,
            'posted' => true,
            'mapping' => $result,
        ];
    }

    protected function buildLines(AccountingPostingRule $rule, array $context, int $businessId, ?int $locationId): array
    {
        $baseAmount = (float) Arr::get($context, 'amount', 0);
        $rows = [];
        $warnings = [];

        foreach ($rule->lines as $line) {
            $mappingKey = $this->resolveMappingKey($line, $context);
            if (empty($mappingKey)) {
                $warnings['missing_keys'][] = $line->context_key ?? 'mapping_key_'.$line->id;
                continue;
            }

            $accountId = $this->mappingResolver->resolve($businessId, $locationId, $mappingKey);
            if (is_null($accountId)) {
                $warnings['invalid_accounts'][] = $mappingKey;
                continue;
            }

            $rows[] = [
                'accounting_account_id' => $accountId,
                'amount' => round($baseAmount * (float) $line->amount_multiplier, 2),
                'type' => $line->direction,
                'sub_type' => $rule->operation_key,
                'map_type' => $mappingKey,
            ];
        }

        return compact('rows', 'warnings');
    }

    protected function resolveMappingKey(AccountingPostingRuleLine $line, array $context): ?string
    {
        if (!empty($line->context_key)) {
            return Arr::get($context, $line->context_key);
        }

        return optional($line->mappingKey)->key;
    }

    protected function getOrCreateMapping(array $context, string $operationKey, bool $lock = false): AccountingAccTransMapping
    {
        $query = AccountingAccTransMapping::where('business_id', Arr::get($context, 'business_id'))
            ->where('source_type', Arr::get($context, 'source_type'))
            ->where('source_id', Arr::get($context, 'source_id'))
            ->where('operation_key', $operationKey);

        if ($lock) {
            $query->lockForUpdate();
        }

        $mapping = $query->first();

        $payload = [
            'business_id' => Arr::get($context, 'business_id'),
            'ref_no' => Arr::get($context, 'reference', $operationKey),
            'type' => $operationKey,
            'operation_key' => $operationKey,
            'source_type' => Arr::get($context, 'source_type'),
            'source_id' => Arr::get($context, 'source_id'),
            'note' => Arr::get($context, 'note'),
            'operation_date' => Arr::get($context, 'operation_date', Carbon::now()),
            'created_by' => Arr::get($context, 'created_by', 0),
        ];

        if ($mapping) {
            $mapping->fill($payload);
            $mapping->save();

            return $mapping;
        }

        return AccountingAccTransMapping::create($payload);
    }

    protected function mapPaymentMethodToKey(?string $method): string
    {
        $reverse = [];
        foreach (MappingResolver::PAYMENT_KEY_MAP as $key => $value) {
            $reverse[$value] = $key;
        }

        return $reverse[$method] ?? 'PAYMENT_OTHER';
    }
}
