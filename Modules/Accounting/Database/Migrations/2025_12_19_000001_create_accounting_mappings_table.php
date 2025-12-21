<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountingMappingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounting_mappings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('mapping_key_id')->nullable();
            $table->integer('business_id');
            $table->integer('location_id')->default(0);
            $table->unsignedBigInteger('account_id');
            $table->string('key');
            $table->timestamps();

            $table->unique(['business_id', 'location_id', 'key'], 'accounting_mappings_business_location_key_unique');
            $table->index('mapping_key_id');
            $table->index('account_id');

            $table->foreign('account_id')
                ->references('id')->on('accounting_accounts')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreign('mapping_key_id')
                ->references('id')->on('accounting_mapping_keys')
                ->onUpdate('cascade')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accounting_mappings');
    }
}
