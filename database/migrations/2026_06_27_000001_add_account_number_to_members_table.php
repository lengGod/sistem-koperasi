<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('members', 'account_number')) {
            Schema::table('members', function (Blueprint $table) {
                $table->string('account_number', 32)->nullable()->unique()->after('member_number');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('members', 'account_number')) {
            Schema::table('members', function (Blueprint $table) {
                $table->dropUnique(['account_number']);
                $table->dropColumn('account_number');
            });
        }
    }
};