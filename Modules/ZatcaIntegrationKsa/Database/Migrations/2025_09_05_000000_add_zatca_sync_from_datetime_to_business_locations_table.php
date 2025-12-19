<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('business_locations', function (Blueprint $table) {
            $table->dateTime('zatca_sync_from_datetime')->nullable()->after('sale_invoice_scheme_id');
        });

        // Initialize the new column for existing locations that already have ZATCA details
        // Set zatca_sync_from_datetime to the row's updated_at timestamp for a sensible default
        DB::table('business_locations')
            ->whereNotNull('zatca_details')
            ->update(['zatca_sync_from_datetime' => DB::raw('updated_at')]);
    }

    public function down(): void
    {
        Schema::table('business_locations', function (Blueprint $table) {
            $table->dropColumn('zatca_sync_from_datetime');
        });
    }
};


