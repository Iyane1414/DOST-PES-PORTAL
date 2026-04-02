<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contact_messages', function (Blueprint $table): void {
            $table->timestamp('opened_at')->nullable()->after('message');
            $table->timestamp('replied_at')->nullable()->after('opened_at');
            $table->string('admin_reply_subject')->nullable()->after('replied_at');
            $table->text('admin_reply_body')->nullable()->after('admin_reply_subject');
        });
    }

    public function down(): void
    {
        Schema::table('contact_messages', function (Blueprint $table): void {
            $table->dropColumn([
                'opened_at',
                'replied_at',
                'admin_reply_subject',
                'admin_reply_body',
            ]);
        });
    }
};
