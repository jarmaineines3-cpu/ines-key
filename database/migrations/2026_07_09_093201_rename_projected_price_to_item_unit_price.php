<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            if (Schema::hasColumn('items', 'projected_price') && ! Schema::hasColumn('items', 'item_unit_price')) {
                $table->renameColumn('projected_price', 'item_unit_price');
            } elseif (! Schema::hasColumn('items', 'item_unit_price')) {
                $table->decimal('item_unit_price', 15, 2)->default(0)->after('unit');
            }
        });

        Schema::table('purchase_item', function (Blueprint $table) {
            if (Schema::hasColumn('purchase_item', 'projected_price') && ! Schema::hasColumn('purchase_item', 'item_unit_price')) {
                $table->renameColumn('projected_price', 'item_unit_price');
            } elseif (! Schema::hasColumn('purchase_item', 'item_unit_price')) {
                $table->decimal('item_unit_price', 15, 2)->default(0)->after('item_quantity');
            }
        });
    }

    public function down(): void
    {
        Schema::table('purchase_item', function (Blueprint $table) {
            if (Schema::hasColumn('purchase_item', 'item_unit_price') && ! Schema::hasColumn('purchase_item', 'projected_price')) {
                $table->renameColumn('item_unit_price', 'projected_price');
            }
        });

        Schema::table('items', function (Blueprint $table) {
            if (Schema::hasColumn('items', 'item_unit_price') && ! Schema::hasColumn('items', 'projected_price')) {
                $table->renameColumn('item_unit_price', 'projected_price');
            }
        });
    }
};
