<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSourceColumnsToAccountingAccTransMappings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('accounting_acc_trans_mappings', function (Blueprint $table) {
            $table->string('source_type')->nullable()->after('business_id');
            $table->unsignedBigInteger('source_id')->nullable()->after('source_type');
            $table->string('operation_key')->nullable()->after('type');

            $table->unique(
                ['business_id', 'source_type', 'source_id', 'operation_key'],
                'acc_trans_mappings_source_unique'
            );
            $table->index(['source_type', 'source_id', 'operation_key'], 'acc_trans_mappings_source_idx');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('accounting_acc_trans_mappings', function (Blueprint $table) {
            $table->dropUnique('acc_trans_mappings_source_unique');
            $table->dropIndex('acc_trans_mappings_source_idx');
            $table->dropColumn(['source_type', 'source_id', 'operation_key']);
        });
    }
}
