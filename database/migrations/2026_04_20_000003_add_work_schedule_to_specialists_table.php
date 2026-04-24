<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('Specialist')) {
            return;
        }

        Schema::table('Specialist', function (Blueprint $table) {
            if (!Schema::hasColumn('Specialist', 'workSchedule')) {
                $table->text('workSchedule')->nullable();
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('Specialist')) {
            return;
        }

        Schema::table('Specialist', function (Blueprint $table) {
            if (Schema::hasColumn('Specialist', 'workSchedule')) {
                $table->dropColumn('workSchedule');
            }
        });
    }
};
