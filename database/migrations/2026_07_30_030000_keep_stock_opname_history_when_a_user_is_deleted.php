<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * stock_opnames.user_id cascaded on delete, so removing a user erased every
 * stock take they had ever recorded. In an asset audit system that is the audit
 * trail itself — it should survive somebody leaving the company.
 *
 * Two changes together:
 *
 *   1. The foreign key becomes nullOnDelete and the column nullable, so the row
 *      outlives the account.
 *   2. checked_by_name records who did the check at the time it happened. A
 *      nulled foreign key keeps the row but loses the name, which is the part of
 *      the record anyone auditing actually needs.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_opnames', function (Blueprint $table) {
            $table->string('checked_by_name')->nullable()->after('user_id');
        });

        // Existing rows still have their user; take the name while it is there.
        DB::table('stock_opnames')
            ->whereNull('checked_by_name')
            ->whereNotNull('user_id')
            ->update([
                'checked_by_name' => DB::raw('(select name from users where users.id = stock_opnames.user_id)'),
            ]);

        Schema::table('stock_opnames', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('stock_opnames', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->change();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        // Rows whose user is gone cannot be restored to a cascading foreign key,
        // so they are removed first. Reversing this loses history by design.
        DB::table('stock_opnames')->whereNull('user_id')->delete();

        Schema::table('stock_opnames', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('stock_opnames', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable(false)->change();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->dropColumn('checked_by_name');
        });
    }
};
