<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('members', 'work_unit')) {
            Schema::table('members', function (Blueprint $table) {
                $table->string('nik', 20)->nullable()->change();
                $table->enum('gender', ['male', 'female'])->nullable()->change();
                $table->string('work_unit')->nullable()->after('gender');
            });
        }

        if (! Schema::hasColumn('members', 'employment_status')) {
            Schema::table('members', function (Blueprint $table) {
                $table->string('employment_status')->nullable()->after('status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            if (Schema::hasColumn('members', 'work_unit')) {
                $table->dropColumn('work_unit');
            }

            if (Schema::hasColumn('members', 'employment_status')) {
                $table->dropColumn('employment_status');
            }

            $table->string('nik', 20)->nullable(false)->change();
            $table->enum('gender', ['male', 'female'])->nullable(false)->change();
        });
    }
};
