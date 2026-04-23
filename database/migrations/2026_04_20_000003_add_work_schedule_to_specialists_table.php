<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('Specialist', function (Blueprint $table) {
            if (!Schema::hasColumn('Specialist', 'workSchedule')) {
                $table->text('workSchedule')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('Specialist', function (Blueprint $table) {
            if (Schema::hasColumn('Specialist', 'workSchedule')) {
                $table->dropColumn('workSchedule');
            }
        });
    }
};
