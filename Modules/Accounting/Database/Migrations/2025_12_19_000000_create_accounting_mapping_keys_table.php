<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateAccountingMappingKeysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounting_mapping_keys', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('key')->unique();
            $table->string('label')->nullable();
            $table->timestamps();
        });

        $now = now();
        $seeds = [
            ['key' => 'AR_CONTROL', 'label' => 'Accounts Receivable Control'],
            ['key' => 'AP_CONTROL', 'label' => 'Accounts Payable Control'],
            ['key' => 'REVENUE_GENERAL', 'label' => 'Revenue'],
            ['key' => 'INVENTORY_ASSET', 'label' => 'Inventory Asset'],
            ['key' => 'COGS', 'label' => 'Cost of Goods Sold'],
            ['key' => 'SALES_RETURNS', 'label' => 'Sales Returns'],
            ['key' => 'CASH_MAIN', 'label' => 'Cash'],
            ['key' => 'UNDEPOSITED_FUNDS', 'label' => 'Undeposited Funds'],
            ['key' => 'PAYMENT_CASH', 'label' => 'Payment - Cash'],
            ['key' => 'PAYMENT_CARD', 'label' => 'Payment - Card'],
            ['key' => 'PAYMENT_CHEQUE', 'label' => 'Payment - Cheque'],
            ['key' => 'PAYMENT_BANK_TRANSFER', 'label' => 'Payment - Bank Transfer'],
            ['key' => 'PAYMENT_OTHER', 'label' => 'Payment - Other'],
            ['key' => 'PAYMENT_CUSTOM_1', 'label' => 'Payment - Custom 1'],
            ['key' => 'PAYMENT_CUSTOM_2', 'label' => 'Payment - Custom 2'],
            ['key' => 'PAYMENT_CUSTOM_3', 'label' => 'Payment - Custom 3'],
            ['key' => 'PAYMENT_CUSTOM_4', 'label' => 'Payment - Custom 4'],
            ['key' => 'PAYMENT_CUSTOM_5', 'label' => 'Payment - Custom 5'],
            ['key' => 'PAYMENT_CUSTOM_6', 'label' => 'Payment - Custom 6'],
            ['key' => 'PAYMENT_CUSTOM_7', 'label' => 'Payment - Custom 7'],
        ];

        DB::table('accounting_mapping_keys')->insert(
            array_map(function ($row) use ($now) {
                return array_merge($row, [
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }, $seeds)
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accounting_mapping_keys');
    }
}
