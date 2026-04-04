<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('news', function (Blueprint $table): void {
            $table->id();
            $table->string('eyebrow', 100);
            $table->string('title');
            $table->date('date');
            $table->text('summary');
            $table->longText('content');
            $table->string('link_url')->nullable();
            $table->string('thumbnail_path')->nullable();
            $table->string('accent', 50)->default('cyan');
            $table->string('image_alt')->nullable();
            $table->timestamps();
        });

        DB::table('news')->insert([
            [
                'eyebrow' => 'Featured',
                'title' => 'DOST Project LODI',
                'date' => '2022-08-04',
                'summary' => 'Boosting DOST digital transformation while giving strong IT internship support for science scholars.',
                'content' => 'The Department of Science and Technology-Science Education Institute and the DOST Planning and Evaluation Service signed a partnership for Project LODI to support digital transformation initiatives and strengthen institutional collaboration through scholar engagement.',
                'link_url' => null,
                'thumbnail_path' => 'images/p1.png',
                'accent' => 'cyan',
                'image_alt' => 'DOST Project LODI',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'eyebrow' => 'Update',
                'title' => 'DOST PES Digital Transformation Roadmap Released',
                'date' => '2026-02-18',
                'summary' => 'A new roadmap aligns service modernization, planning workflows, and data-ready operations across PES.',
                'content' => 'PES introduced its digital transformation roadmap to guide service improvements in planning, monitoring, records management, and information systems coordination. The roadmap outlines priority workstreams for process redesign, internal platforms, and stronger cross-office data sharing.',
                'link_url' => null,
                'thumbnail_path' => null,
                'accent' => 'blue',
                'image_alt' => 'DX Roadmap',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'eyebrow' => 'Event',
                'title' => 'PES Annual Planning & Evaluation Workshop 2026',
                'date' => '2026-01-28',
                'summary' => 'Planning and evaluation leads gathered to align targets, reporting cycles, and priority outcomes for the year.',
                'content' => 'The annual PES workshop convened division representatives and partner offices to align targets, refine milestone indicators, and review reporting expectations for the year. The session also focused on stronger coordination between planning activities and evaluation outputs.',
                'link_url' => null,
                'thumbnail_path' => null,
                'accent' => 'gold',
                'image_alt' => 'Workshop',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};
