<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class SeedAccountingPostingRules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $now = now();
        $keys = DB::table('accounting_mapping_keys')->pluck('id', 'key');

        $rules = [
            'sell_invoice' => [
                'module_key' => 'sales',
                'lines' => [
                    ['direction' => 'debit', 'mapping_key' => 'AR_CONTROL'],
                    ['direction' => 'credit', 'mapping_key' => 'REVENUE_GENERAL'],
                ],
            ],
            'sell_payment' => [
                'module_key' => 'sales',
                'lines' => [
                    ['direction' => 'debit', 'context_key' => 'payment_account'],
                    ['direction' => 'credit', 'mapping_key' => 'AR_CONTROL'],
                ],
            ],
            'purchase_invoice' => [
                'module_key' => 'purchases',
                'lines' => [
                    ['direction' => 'debit', 'mapping_key' => 'INVENTORY_ASSET'],
                    ['direction' => 'credit', 'mapping_key' => 'AP_CONTROL'],
                ],
            ],
            'purchase_payment' => [
                'module_key' => 'purchases',
                'lines' => [
                    ['direction' => 'debit', 'mapping_key' => 'AP_CONTROL'],
                    ['direction' => 'credit', 'context_key' => 'payment_account'],
                ],
            ],
            'expense' => [
                'module_key' => 'expenses',
                'lines' => [
                    ['direction' => 'debit', 'mapping_key' => 'COGS'],
                    ['direction' => 'credit', 'context_key' => 'payment_account'],
                ],
            ],
        ];

        $ruleIds = [];
        foreach ($rules as $operation => $config) {
            $ruleIds[$operation] = DB::table('accounting_posting_rules')->insertGetId([
                'business_id' => 0,
                'location_id' => 0,
                'module_key' => $config['module_key'],
                'operation_key' => $operation,
                'is_active' => true,
                'meta' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            foreach ($config['lines'] as $line) {
                $mappingKey = $line['mapping_key'] ?? null;
                $mappingKeyId = $mappingKey && isset($keys[$mappingKey]) ? $keys[$mappingKey] : null;

                DB::table('accounting_posting_rule_lines')->insert([
                    'posting_rule_id' => $ruleIds[$operation],
                    'mapping_key_id' => $mappingKeyId,
                    'context_key' => $line['context_key'] ?? null,
                    'direction' => $line['direction'],
                    'amount_multiplier' => $line['amount_multiplier'] ?? 1,
                    'meta' => $line['meta'] ?? null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $operations = ['sell_invoice', 'sell_payment', 'purchase_invoice', 'purchase_payment', 'expense'];
        $rules = DB::table('accounting_posting_rules')
            ->whereIn('operation_key', $operations)
            ->where('business_id', 0)
            ->where('location_id', 0)
            ->pluck('id')
            ->toArray();

        DB::table('accounting_posting_rule_lines')->whereIn('posting_rule_id', $rules)->delete();
        DB::table('accounting_posting_rules')->whereIn('id', $rules)->delete();
    }
}
