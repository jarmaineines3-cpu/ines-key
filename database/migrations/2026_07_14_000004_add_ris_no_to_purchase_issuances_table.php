<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('purchase_issuances', function (Blueprint $table) {
            $table->string('ris_no')->nullable()->after('issued_at');
        });
    }

    public function down()
    {
        Schema::table('purchase_issuances', function (Blueprint $table) {
            $table->dropColumn('ris_no');
        });
    }
};
