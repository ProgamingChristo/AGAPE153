<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contact_messages', function (Blueprint $table): void {
            $table->string('reply_subject')->nullable()->after('message');
            $table->text('reply_message')->nullable()->after('reply_subject');
            $table->foreignId('replied_by')->nullable()->after('reply_message')->constrained('users')->nullOnDelete();
            $table->timestamp('replied_at')->nullable()->after('replied_by');
        });
    }

    public function down(): void
    {
        Schema::table('contact_messages', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('replied_by');
            $table->dropColumn(['reply_subject', 'reply_message', 'replied_at']);
        });
    }
};
