<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->unsignedBigInteger('senior_bookkeeper_id')->nullable()->after('approved_by');
            $table->string('senior_bookkeeper_name')->nullable()->after('senior_bookkeeper_id');

            $table->foreign('senior_bookkeeper_id')
                ->references('id')
                ->on('employees')
                ->onDelete('set null');
        });

        // Backfill existing purchases with the current senior bookkeeper for their school
        $purchases = DB::table('purchases')->get();
        foreach ($purchases as $purchase) {
            if (! empty($purchase->senior_bookkeeper_id) || ! empty($purchase->senior_bookkeeper_name)) {
                continue;
            }

            $senior = DB::table('bac_members')
                ->where('school_id', $purchase->school)
                ->where('role', 'senior bookkeeper')
                ->first();

            if ($senior && $senior->employee_id) {
                $employeeName = DB::table('employees')->where('id', $senior->employee_id)->value('full_name');

                DB::table('purchases')
                    ->where('id', $purchase->id)
                    ->update([
                        'senior_bookkeeper_id' => $senior->employee_id,
                        'senior_bookkeeper_name' => $employeeName,
                    ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropForeign(['senior_bookkeeper_id']);
            $table->dropColumn(['senior_bookkeeper_id', 'senior_bookkeeper_name']);
        });
    }
};
