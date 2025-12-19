<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateInventoryResetTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create inventory_reset_logs table
        if (!Schema::hasTable('inventory_reset_logs')) {
            Schema::create('inventory_reset_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('business_id');
                $table->unsignedInteger('user_id');
                $table->enum('reset_type', ['all_products', 'selected_products']);
                $table->enum('reset_mode', ['all_levels', 'positive_only', 'negative_only', 'zero_only'])->default('all_levels');
                $table->decimal('target_quantity', 10, 2)->nullable();
                $table->unsignedInteger('location_id')->nullable();
                $table->text('reason');
                $table->enum('status', ['processing', 'completed', 'failed'])->default('processing');
                $table->integer('items_reset')->default(0);
                $table->text('error_message')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();

                // Indexes for performance
                $table->index(['business_id', 'created_at']);
                $table->index(['business_id', 'status']);
                $table->index(['user_id', 'created_at']);
            });
        }

        // Create inventory_reset_items table
        if (!Schema::hasTable('inventory_reset_items')) {
            Schema::create('inventory_reset_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('inventory_reset_log_id');
                $table->unsignedInteger('product_id');
                $table->unsignedInteger('variation_id')->nullable();
                $table->unsignedInteger('location_id');
                $table->decimal('quantity_before', 22, 4);
                $table->decimal('quantity_after', 22, 4);
                $table->timestamps();

                // Indexes for performance
                $table->index(['inventory_reset_log_id']);
                $table->index(['product_id', 'location_id']);
                $table->index(['variation_id', 'location_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inventory_reset_items');
        Schema::dropIfExists('inventory_reset_logs');
    }
}