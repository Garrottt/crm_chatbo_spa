<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('Conversation')) {
            DB::statement('CREATE INDEX IF NOT EXISTS conversation_updated_at_idx ON "Conversation" ("updatedAt" DESC)');
            DB::statement('CREATE INDEX IF NOT EXISTS conversation_client_id_idx ON "Conversation" ("clientId")');
        }

        if (Schema::hasTable('Message')) {
            DB::statement('CREATE INDEX IF NOT EXISTS message_conversation_created_at_idx ON "Message" ("conversationId", "createdAt" DESC)');
        }

        if (Schema::hasTable('Booking')) {
            DB::statement('CREATE INDEX IF NOT EXISTS booking_scheduled_at_idx ON "Booking" ("scheduledAt")');
            DB::statement('CREATE INDEX IF NOT EXISTS booking_specialist_id_idx ON "Booking" ("specialistId")');
            DB::statement('CREATE INDEX IF NOT EXISTS booking_client_id_idx ON "Booking" ("clientId")');
        }
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS conversation_updated_at_idx');
        DB::statement('DROP INDEX IF EXISTS conversation_client_id_idx');
        DB::statement('DROP INDEX IF EXISTS message_conversation_created_at_idx');
        DB::statement('DROP INDEX IF EXISTS booking_scheduled_at_idx');
        DB::statement('DROP INDEX IF EXISTS booking_specialist_id_idx');
        DB::statement('DROP INDEX IF EXISTS booking_client_id_idx');
    }
};
