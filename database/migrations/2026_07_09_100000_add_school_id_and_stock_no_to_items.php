<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            if (! Schema::hasColumn('items', 'school_id')) {
                $table->foreignId('school_id')->nullable()->constrained('schools')->nullOnDelete()->after('id');
            }

            if (! Schema::hasColumn('items', 'stock_no')) {
                $table->integer('stock_no')->nullable()->after('school_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            if (Schema::hasColumn('items', 'stock_no')) {
                $table->dropColumn('stock_no');
            }

            if (Schema::hasColumn('items', 'school_id')) {
                $table->dropForeign(['school_id']);
                $table->dropColumn('school_id');
            }
        });
    }
};
