<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            if (! Schema::hasColumn('items', 'uacs_code_id')) {
                $table->foreignId('uacs_code_id')->nullable()->constrained('uacs_codes')->nullOnDelete()->after('school_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            if (Schema::hasColumn('items', 'uacs_code_id')) {
                $table->dropForeign(['uacs_code_id']);
                $table->dropColumn('uacs_code_id');
            }
        });
    }
};
