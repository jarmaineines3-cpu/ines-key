<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_issuances', function (Blueprint $table) {
            if (! Schema::hasColumn('purchase_issuances', 'inventory_details')) {
                $table->longText('inventory_details')->nullable()->after('inventory_lifespan');
            }
        });
    }

    public function down(): void
    {
        Schema::table('purchase_issuances', function (Blueprint $table) {
            if (Schema::hasColumn('purchase_issuances', 'inventory_details')) {
                $table->dropColumn('inventory_details');
            }
        });
    }
};
