<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('Alert')) {
            return;
        }

        Schema::create('Alert', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('eventKey')->unique();
            $table->string('type');
            $table->string('title');
            $table->text('body');
            $table->unsignedBigInteger('recipientUserId')->nullable();
            $table->string('recipientRole')->nullable();
            $table->string('clientId')->nullable();
            $table->string('bookingId')->nullable();
            $table->string('conversationId')->nullable();
            $table->string('actionUrl')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('readAt')->nullable();
            $table->timestamp('createdAt')->useCurrent();
            $table->timestamp('updatedAt')->useCurrent();

            $table->index(['recipientUserId', 'readAt', 'createdAt']);
            $table->index(['recipientRole', 'readAt', 'createdAt']);
            $table->index('bookingId');
            $table->index('conversationId');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Alert');
    }
};
