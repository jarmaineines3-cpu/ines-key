<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('purchase_issuances', function (Blueprint $table) {
            $table->string('inventory_no')->nullable()->unique()->after('inventory_lifespan');
        });
    }

    public function down()
    {
        Schema::table('purchase_issuances', function (Blueprint $table) {
            $table->dropUnique(['inventory_no']);
            $table->dropColumn('inventory_no');
        });
    }
};
