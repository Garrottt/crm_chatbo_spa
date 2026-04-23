<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('Conversation', function (Blueprint $table) {
            if (!Schema::hasColumn('Conversation', 'bot_paused')) {
                $table->boolean('bot_paused')->default(false);
            }

            if (!Schema::hasColumn('Conversation', 'taken_over_by_agent')) {
                $table->boolean('taken_over_by_agent')->default(false);
            }

            if (!Schema::hasColumn('Conversation', 'taken_over_at')) {
                $table->timestamp('taken_over_at')->nullable();
            }

            if (!Schema::hasColumn('Conversation', 'taken_over_by_user_id')) {
                $table->string('taken_over_by_user_id')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('Conversation', function (Blueprint $table) {
            $columns = [
                'bot_paused',
                'taken_over_by_agent',
                'taken_over_at',
                'taken_over_by_user_id',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('Conversation', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
