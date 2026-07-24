<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_issuances', function (Blueprint $table) {
            $table->unsignedInteger('inventory_lifespan')->nullable()->after('ris_no');
        });
    }

    public function down(): void
    {
        Schema::table('purchase_issuances', function (Blueprint $table) {
            $table->dropColumn('inventory_lifespan');
        });
    }
};
