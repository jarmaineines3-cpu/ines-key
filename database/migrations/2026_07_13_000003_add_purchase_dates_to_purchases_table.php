<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->date('po_date')->nullable()->after('pr_date');
            $table->date('dv_date')->nullable()->after('po_date');
            $table->date('inspection_date')->nullable()->after('dv_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropColumn(['po_date', 'dv_date', 'inspection_date']);
        });
    }
};
