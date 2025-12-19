<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transaction_exchanges', function (Blueprint $table) {
            $table->integer('return_transaction_id')->unsigned()->nullable()->after('exchange_transaction_id')->comment('Sell return transaction for returned items');

            $table->foreign('return_transaction_id')->references('id')->on('transactions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transaction_exchanges', function (Blueprint $table) {
            $table->dropForeign(['return_transaction_id']);
            $table->dropColumn('return_transaction_id');
        });
    }
};
