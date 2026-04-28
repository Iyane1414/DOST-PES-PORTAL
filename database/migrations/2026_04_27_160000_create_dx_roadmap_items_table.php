<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dx_roadmap_items', function (Blueprint $table): void {
            $table->id();
            $table->string('year_label', 50);
            $table->string('title');
            $table->text('description');
            $table->json('milestones')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        DB::table('dx_roadmap_items')->insert([
            [
                'year_label' => '2021',
                'title' => 'Plan & Assess',
                'description' => 'The initial stage focused on defining the DOST-DX direction, assessing readiness, and securing the first set of approvals.',
                'milestones' => json_encode(['Roadmap', 'Gap Analysis', 'Initial Approvals']),
                'sort_order' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'year_label' => '2022',
                'title' => 'Build Foundations',
                'description' => 'This stage established the first operational building blocks needed to support transformation initiatives across DOST.',
                'milestones' => json_encode(['Software Dev Pack', 'Structure Rationalization', 'iLab Launch']),
                'sort_order' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'year_label' => '2023',
                'title' => 'Strengthen Governance',
                'description' => 'Governance, portfolio coordination, and cyber capability-building were reinforced to prepare for wider digital delivery.',
                'milestones' => json_encode(['Portfolio Groups', 'EA Training', 'Cybersecurity Expansion']),
                'sort_order' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'year_label' => '2024',
                'title' => 'Standardize Processes',
                'description' => 'DOST-DX advanced process standardization, project management structures, and broader stakeholder engagement.',
                'milestones' => json_encode(['PM Process', 'DX Core Team Proposal', 'Engagement Expansion']),
                'sort_order' => 4,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'year_label' => '2025',
                'title' => 'System Integration',
                'description' => 'Major integration workstreams were lined up to harmonize systems and strengthen portal and knowledge-management development.',
                'milestones' => json_encode(['Harmonized iHRMIS', 'Portal & KM Dev', 'Phase 2 IS']),
                'sort_order' => 5,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'year_label' => '2026-2028',
                'title' => 'Modernize & Expand',
                'description' => 'The long-range phase focuses on infrastructure modernization, harmonized rollouts, cyber capability expansion, and broader transformation support.',
                'milestones' => json_encode(['Data Centers', 'Cloud Plans', 'Harmonized IS Rollout', 'Cybersecurity Unit and ITD Transformation', 'Geospatial']),
                'sort_order' => 6,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('dx_roadmap_items');
    }
};
