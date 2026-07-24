<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_issuances', function (Blueprint $table) {
            $table->string('rspi_serial_no')->nullable()->after('inventory_no');
        });
    }

    public function down(): void
    {
        Schema::table('purchase_issuances', function (Blueprint $table) {
            $table->dropColumn('rspi_serial_no');
        });
    }
};
