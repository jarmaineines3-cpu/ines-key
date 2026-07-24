<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_issuances', function (Blueprint $table) {
            $table->string('rsmi_serial_no')->nullable()->after('rspi_serial_no');
        });
    }

    public function down(): void
    {
        Schema::table('purchase_issuances', function (Blueprint $table) {
            $table->dropColumn('rsmi_serial_no');
        });
    }
};
