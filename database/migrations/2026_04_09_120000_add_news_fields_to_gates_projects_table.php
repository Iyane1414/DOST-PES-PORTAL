<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gates_projects', function (Blueprint $table): void {
            $table->string('news_eyebrow', 100)->nullable()->after('type');
            $table->text('news_summary')->nullable()->after('description');
            $table->longText('news_content')->nullable()->after('news_summary');
            $table->string('news_link_url')->nullable()->after('url');
            $table->string('news_accent', 30)->nullable()->after('news_link_url');
            $table->string('news_image_alt')->nullable()->after('news_accent');
            $table->string('thumbnail_path')->nullable()->after('news_image_alt');
        });
    }

    public function down(): void
    {
        Schema::table('gates_projects', function (Blueprint $table): void {
            $table->dropColumn([
                'news_eyebrow',
                'news_summary',
                'news_content',
                'news_link_url',
                'news_accent',
                'news_image_alt',
                'thumbnail_path',
            ]);
        });
    }
};
