<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->decimal('projected_price', 15, 2)->default(0)->after('unit');
        });

        Schema::table('purchase_item', function (Blueprint $table) {
            $table->decimal('projected_price', 15, 2)->default(0)->after('item_quantity');
        });
    }

    public function down(): void
    {
        Schema::table('purchase_item', function (Blueprint $table) {
            $table->dropColumn('projected_price');
        });

        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn('projected_price');
        });
    }
};
