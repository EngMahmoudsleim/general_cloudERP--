<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountingPostingRuleLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounting_posting_rule_lines', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('posting_rule_id');
            $table->unsignedBigInteger('mapping_key_id')->nullable();
            $table->string('context_key')->nullable();
            $table->enum('direction', ['debit', 'credit']);
            $table->decimal('amount_multiplier', 22, 4)->default(1);
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index('posting_rule_id');
            $table->index('mapping_key_id');

            $table->foreign('posting_rule_id')
                ->references('id')->on('accounting_posting_rules')
                ->onUpdate('cascade')
                ->onDelete('cascade');

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
        Schema::dropIfExists('accounting_posting_rule_lines');
    }
}
