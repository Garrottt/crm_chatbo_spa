<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('Specialist')) {
            Schema::create('Specialist', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->foreignId('userId')->nullable()->constrained('users')->nullOnDelete();
                $table->string('name');
                $table->string('specialty')->nullable();
                $table->boolean('active')->default(true);
                $table->text('workSchedule')->nullable();
            });
        }

        if (!Schema::hasTable('Availability')) {
            Schema::create('Availability', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->string('specialistId');
                $table->unsignedTinyInteger('dayOfWeek');
                $table->time('startTime');
                $table->time('endTime');

                $table->foreign('specialistId')->references('id')->on('Specialist')->cascadeOnDelete();
            });
        }

        if (!Schema::hasTable('_SpecialistServices')) {
            Schema::create('_SpecialistServices', function (Blueprint $table) {
                $table->string('A');
                $table->string('B');

                $table->primary(['A', 'B']);
                $table->foreign('A')->references('id')->on('Service')->cascadeOnDelete();
                $table->foreign('B')->references('id')->on('Specialist')->cascadeOnDelete();
            });
        }

        if (Schema::hasTable('Booking') && !Schema::hasColumn('Booking', 'specialistId')) {
            Schema::table('Booking', function (Blueprint $table) {
                $table->string('specialistId')->nullable();
                $table->foreign('specialistId')->references('id')->on('Specialist')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('Booking') && Schema::hasColumn('Booking', 'specialistId')) {
            Schema::table('Booking', function (Blueprint $table) {
                $table->dropForeign(['specialistId']);
                $table->dropColumn('specialistId');
            });
        }

        Schema::dropIfExists('_SpecialistServices');
        Schema::dropIfExists('Availability');
        Schema::dropIfExists('Specialist');
    }
};
