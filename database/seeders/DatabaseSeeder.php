<?php

namespace Database\Seeders;

use App\Models\Division;
use App\Models\DxItem;
use App\Models\Issuance;
use App\Models\IssuanceCategory;
use App\Models\Material;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Guidelines', 'Plans', 'Order', 'Memorandum'] as $category) {
            IssuanceCategory::query()->firstOrCreate(['name' => $category]);
        }

        $issuances = [
            ['title' => 'PES Memo 2024-001', 'category' => 'Memorandum', 'date' => '2024-01-15', 'division' => 'Planning Division', 'url' => 'https://example.com/issuances/pes-memo-2024-001'],
            ['title' => 'Special Order No. 45', 'category' => 'Order', 'date' => '2024-02-10', 'division' => 'Evaluation Division', 'url' => 'https://example.com/issuances/special-order-45'],
            ['title' => 'Planning Circular 2024-07', 'category' => 'Circular', 'date' => '2024-06-01', 'division' => 'Project Management Division', 'url' => 'https://example.com/issuances/planning-circular-2024-07'],
        ];

        foreach ($issuances as $issuance) {
            Issuance::query()->firstOrCreate(['title' => $issuance['title']], $issuance);
        }

        $materials = [
            ['title' => 'DOST Strategic Plan 2023-2028', 'type' => 'PowerPoint', 'date' => '2023-12-01', 'division' => 'Planning Division', 'url' => 'https://example.com/materials/strategic-plan'],
            ['title' => 'Impact Assessment Infographic', 'type' => 'Infographic', 'date' => '2024-01-20', 'division' => 'Evaluation Division', 'url' => 'https://example.com/materials/impact-assessment'],
            ['title' => 'PES Functions Video', 'type' => 'Video', 'date' => '2024-02-05', 'division' => 'Planning Division', 'url' => 'https://example.com/materials/pes-functions-video'],
        ];

        foreach ($materials as $material) {
            Material::query()->firstOrCreate(['title' => $material['title']], $material);
        }

        $divisions = [
            ['name' => 'Policy Development and Planning Division', 'description' => 'Creates science, technology, and innovation plans, reviews policy alignment with national priorities, and develops policy recommendations and implementation guidance.', 'head' => null],
            ['name' => 'Program Coordination and Monitoring Division', 'description' => 'Monitors agency performance, reviews project proposals, supports budget preparation, and coordinates required reporting and investment program validation.', 'head' => null],
            ['name' => 'S&T Resource Evaluation Division', 'description' => 'Manages S&T and R&D statistics, develops indicators and survey frameworks, and supports national and international statistical reporting.', 'head' => null],
            ['name' => 'Information Technology Division', 'description' => 'Maintains PES digital systems, applications, website, and IT facilities while strengthening information service frameworks and information systems planning.', 'head' => null],
        ];

        foreach ($divisions as $division) {
            Division::query()->firstOrCreate(['name' => $division['name']], $division);
        }

        $dxItems = [
            ['category' => 'domain', 'title' => 'Digital Infrastructure', 'description' => 'Modernizing the backbone of DOST operations with resilient connectivity and cloud-ready foundations.'],
            ['category' => 'domain', 'title' => 'Digital Governance', 'description' => 'Streamlining policy, workflows, and data stewardship for faster and more accountable delivery.'],
            ['category' => 'domain', 'title' => 'Digital Services', 'description' => 'Delivering citizen-centric digital platforms that are secure, responsive, and accessible.'],
            ['category' => 'program', 'title' => 'E-Government Systems', 'description' => 'Integrated systems that improve internal and public-facing service delivery.'],
            ['category' => 'program', 'title' => 'Data Analytics Hub', 'description' => 'Cross-functional analytics capabilities for planning and evaluation decisions.'],
            ['category' => 'program', 'title' => 'Cybersecurity Framework', 'description' => 'Security controls and governance for a trustworthy digital environment.'],
        ];

        foreach ($dxItems as $dxItem) {
            DxItem::query()->firstOrCreate(['title' => $dxItem['title']], $dxItem);
        }
    }
}
