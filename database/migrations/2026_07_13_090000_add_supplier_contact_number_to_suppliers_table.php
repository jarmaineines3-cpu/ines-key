<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('suppliers', 'supplier_contact_number')) {
            Schema::table('suppliers', function (Blueprint $table) {
                $table->string('supplier_contact_number')->nullable()->after('supplier_tin');
            });
        }

        if (Schema::hasColumn('suppliers', 'supplier_contact_numbera') && Schema::hasColumn('suppliers', 'supplier_contact_number')) {
            DB::table('suppliers')
                ->whereNotNull('supplier_contact_numbera')
                ->update([
                    'supplier_contact_number' => DB::raw('supplier_contact_numbera'),
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('suppliers', 'supplier_contact_number')) {
            Schema::table('suppliers', function (Blueprint $table) {
                $table->dropColumn('supplier_contact_number');
            });
        }
    }
};
