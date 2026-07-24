<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bac_members', function (Blueprint $table) {
            $table->dropUnique(['school_id', 'role']);
        });

        DB::statement(
            "CREATE UNIQUE INDEX bac_members_school_id_role_non_member_unique
            ON bac_members (school_id, role)
            WHERE role <> 'member'"
        );
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS bac_members_school_id_role_non_member_unique');

        Schema::table('bac_members', function (Blueprint $table) {
            $table->unique(['school_id', 'role']);
        });
    }
};
