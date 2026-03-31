<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dx_items', function (Blueprint $table): void {
            $table->string('slug')->nullable()->after('category');
            $table->foreignId('parent_id')->nullable()->after('slug')->constrained('dx_items')->nullOnDelete();
            $table->string('domain_key')->nullable()->after('parent_id');
            $table->string('code')->nullable()->after('domain_key');
            $table->string('icon')->nullable()->after('code');
            $table->string('image_path')->nullable()->after('icon');
            $table->string('file_url')->nullable()->after('image_path');
            $table->unsignedInteger('sort_order')->default(0)->after('file_url');
            $table->boolean('is_active')->default(true)->after('sort_order');
        });

        $this->seedStructuredDxItems();
    }

    public function down(): void
    {
        Schema::table('dx_items', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('parent_id');
            $table->dropColumn([
                'slug',
                'domain_key',
                'code',
                'icon',
                'image_path',
                'file_url',
                'sort_order',
                'is_active',
            ]);
        });
    }

    private function seedStructuredDxItems(): void
    {
        $hasStructuredItems = DB::table('dx_items')
            ->whereIn('category', ['domain', 'program', 'project'])
            ->whereNotNull('slug')
            ->exists();

        if ($hasStructuredItems) {
            return;
        }

        $now = now();
        $domainIds = [];
        $programIds = [];

        foreach ($this->domains() as $index => $domain) {
            $domainId = DB::table('dx_items')->insertGetId([
                'category' => 'domain',
                'slug' => $domain['slug'],
                'parent_id' => null,
                'domain_key' => $domain['slug'],
                'code' => null,
                'icon' => $domain['icon'],
                'image_path' => $domain['image'],
                'file_url' => null,
                'sort_order' => $index + 1,
                'is_active' => true,
                'title' => $domain['title'],
                'description' => $domain['description'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $domainIds[$domain['slug']] = $domainId;
        }

        foreach ($this->programs() as $index => $program) {
            $programId = DB::table('dx_items')->insertGetId([
                'category' => 'program',
                'slug' => $program['slug'],
                'parent_id' => $domainIds[$program['domain']] ?? null,
                'domain_key' => $program['domain'],
                'code' => null,
                'icon' => null,
                'image_path' => null,
                'file_url' => null,
                'sort_order' => $index + 1,
                'is_active' => true,
                'title' => $program['title'],
                'description' => $program['description'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $programIds[$program['slug']] = $programId;

            foreach ($program['projects'] as $projectIndex => $project) {
                DB::table('dx_items')->insert([
                    'category' => 'project',
                    'slug' => $project['slug'],
                    'parent_id' => $programId,
                    'domain_key' => $program['domain'],
                    'code' => $project['code'],
                    'icon' => null,
                    'image_path' => null,
                    'file_url' => null,
                    'sort_order' => $projectIndex + 1,
                    'is_active' => true,
                    'title' => $project['title'],
                    'description' => $program['title'].' project under the '.Str::headline($program['domain']).' domain.',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    private function domains(): array
    {
        return [
            [
                'slug' => 'people',
                'title' => 'People',
                'icon' => 'bi-person',
                'image' => 'images/people.png',
                'description' => 'Individuals within the organization, their skills, knowledge, and how they interact with processes and technology including organizational structures.',
            ],
            [
                'slug' => 'process',
                'title' => 'Process',
                'icon' => 'bi-activity',
                'image' => 'images/process.png',
                'description' => 'Encompasses the workflows, procedures, and methodologies used to complete tasks and achieve goals.',
            ],
            [
                'slug' => 'technology',
                'title' => 'Technology',
                'icon' => 'bi-pc-display',
                'image' => 'images/technology.png',
                'description' => 'Infrastructure, tools, information systems, and software used to support and enhance processes and the work of individuals.',
            ],
        ];
    }

    private function programs(): array
    {
        return [
            [
                'slug' => 'structure-rationalization',
                'domain' => 'people',
                'title' => 'Structure Rationalization',
                'description' => 'Organizational structuring, chartering, planning, budgeting, and assessment initiatives under the People domain.',
                'projects' => [
                    ['slug' => 'structure-organization', 'code' => 'SRZ', 'title' => 'Structure & Organization'],
                    ['slug' => 'structure-optimization', 'code' => 'SRZ', 'title' => 'Structure Optimization'],
                    ['slug' => 'new-units', 'code' => 'SRZ', 'title' => 'New Units'],
                    ['slug' => 'epmo-establishment', 'code' => 'SRZ', 'title' => 'ePMO Establishment'],
                    ['slug' => 'foresight-unit-creation', 'code' => 'SRZ', 'title' => 'Foresight Unit Creation'],
                    ['slug' => 'nt-unit-creation', 'code' => 'SRZ', 'title' => 'NT (New Technology) Unit Creation'],
                    ['slug' => 'core-team-creation-chartering', 'code' => 'PMT', 'title' => 'Core Team Creation & Chartering'],
                    ['slug' => 'dx-organization-chartering', 'code' => 'PMT', 'title' => 'DX Organization Chartering'],
                    ['slug' => 'planning', 'code' => 'PES', 'title' => 'Planning'],
                    ['slug' => 'budgeting', 'code' => 'PES', 'title' => 'Budgeting'],
                    ['slug' => 'merc', 'code' => 'PES', 'title' => 'MERC'],
                    ['slug' => 'impact-assessment', 'code' => 'PES', 'title' => 'Impact Assessment'],
                ],
            ],
            [
                'slug' => 'cybersecurity',
                'domain' => 'technology',
                'title' => 'Cybersecurity',
                'description' => 'Security, access, privacy, and cyber-readiness projects supporting safe digital operations.',
                'projects' => [
                    ['slug' => 'cybersecurity-101', 'code' => 'CSP', 'title' => 'Cybersecurity 101'],
                    ['slug' => 'information-radiators', 'code' => 'CSP', 'title' => 'Information Radiators'],
                    ['slug' => 'use-of-vpn', 'code' => 'CSP', 'title' => 'Use of VPN'],
                    ['slug' => 'zero-trust', 'code' => 'CSP', 'title' => 'Zero-trust'],
                    ['slug' => 'two-factor-authentication', 'code' => 'CSP', 'title' => '2-Factor Authentication'],
                    ['slug' => 'admin-privileges', 'code' => 'CSP', 'title' => 'Admin Privileges'],
                    ['slug' => 'usb-disabled', 'code' => 'CSP', 'title' => 'USB Disabled'],
                    ['slug' => 'ciso', 'code' => 'CSP', 'title' => 'CISO'],
                    ['slug' => 'ethical-hacking', 'code' => 'CSP', 'title' => 'Ethical Hacking'],
                    ['slug' => 'versim', 'code' => 'CSP', 'title' => 'Versim'],
                    ['slug' => 'scf', 'code' => 'CSP', 'title' => 'SCF'],
                    ['slug' => 'iso-27001', 'code' => 'CSP', 'title' => 'ISO 27001'],
                    ['slug' => 'nsoc', 'code' => 'CSP', 'title' => 'NSOC'],
                    ['slug' => 'reporting', 'code' => 'CSP', 'title' => 'Reporting'],
                    ['slug' => '321-back-up', 'code' => 'CSP', 'title' => '321 Back-up'],
                    ['slug' => 'email-hosting', 'code' => 'CSP', 'title' => 'Email Hosting'],
                    ['slug' => 'dost-im', 'code' => 'CSP', 'title' => 'DOST IM'],
                    ['slug' => 'dost-encryption-code', 'code' => 'CSP', 'title' => 'DOST Encryption Code'],
                    ['slug' => 'digital-signature', 'code' => 'CSP', 'title' => 'Digital Signature'],
                    ['slug' => 'vpn-implementation', 'code' => 'CSP', 'title' => 'VPN Implementation'],
                    ['slug' => 'ad-implementation', 'code' => 'CSP', 'title' => 'AD Implementation'],
                    ['slug' => 'sso-implementation', 'code' => 'CSP', 'title' => 'SSO Implementation'],
                    ['slug' => 'infosec', 'code' => 'CSP', 'title' => 'InfoSec'],
                    ['slug' => 'ict-policies', 'code' => 'CSP', 'title' => 'ICT Policies'],
                    ['slug' => 'data-privacy', 'code' => 'CSP', 'title' => 'Data Privacy'],
                    ['slug' => 'password-policy', 'code' => 'CSP', 'title' => 'Password Policy'],
                    ['slug' => 'ict-usage', 'code' => 'CSP', 'title' => 'ICT Usage'],
                    ['slug' => 'identity-and-access-management', 'code' => 'CSP', 'title' => 'Identity and Access Management'],
                    ['slug' => 'communications-plan', 'code' => 'PMT', 'title' => 'Communications Plan'],
                ],
            ],
            [
                'slug' => 'is-harmonization',
                'domain' => 'technology',
                'title' => 'IS Harmonization',
                'description' => 'Integration, portal, analytics, and harmonization workstreams for DOST information systems.',
                'projects' => [
                    ['slug' => 'depmis', 'code' => 'ISH', 'title' => 'DEPMIS'],
                    ['slug' => 'integrations', 'code' => 'ISH', 'title' => 'Integrations'],
                    ['slug' => 'is-harmonization-project', 'code' => 'ISH', 'title' => 'IS Harmonization'],
                    ['slug' => 'in-depth-analysis', 'code' => 'ISH', 'title' => 'In-depth Analysis'],
                    ['slug' => 'is-ranking', 'code' => 'ISH', 'title' => 'IS Ranking'],
                    ['slug' => 'phase-1-harmonization', 'code' => 'ISH', 'title' => 'Phase 1 Harmonization'],
                    ['slug' => 'phase-2-integrated-is-development', 'code' => 'ISH', 'title' => 'Phase 2 Integrated IS Development'],
                    ['slug' => 'harmonized-ihrmis', 'code' => 'ISH', 'title' => 'Harmonized iHRMIS'],
                    ['slug' => 'integrated-eulims', 'code' => 'ISH', 'title' => 'Integrated eULIMS (now iLab)'],
                    ['slug' => 'km-portal', 'code' => 'ISH', 'title' => 'KM Portal'],
                    ['slug' => 'dost-portal', 'code' => 'ISH', 'title' => 'DOST Portal'],
                    ['slug' => 'monitoring-analytics-standard-tools', 'code' => 'ISH', 'title' => 'Monitoring and Analytics Standard Tools'],
                    ['slug' => 'daas', 'code' => 'ISH', 'title' => 'DaaS'],
                    ['slug' => 'executive-information-system', 'code' => 'ISH', 'title' => 'Executive Information System (EIS)'],
                ],
            ],
            [
                'slug' => 'infra-harmonization',
                'domain' => 'technology',
                'title' => 'Infra Harmonization',
                'description' => 'Connectivity, cloud, repository, and infrastructure standardization initiatives across DOST.',
                'projects' => [
                    ['slug' => 'dost-primary-connectivity', 'code' => 'INH', 'title' => 'DOST Primary Connectivity'],
                    ['slug' => 'dost-integrated-cloud', 'code' => 'INH', 'title' => 'DOST Integrated Cloud'],
                    ['slug' => 'smart-workplace', 'code' => 'INH', 'title' => 'Smart Workplace'],
                    ['slug' => 'central-repository', 'code' => 'INH', 'title' => 'Central Repository'],
                    ['slug' => 'dost-cloud-production', 'code' => 'INH', 'title' => 'DOST Cloud Production'],
                    ['slug' => 'dost-dc1', 'code' => 'INH', 'title' => 'DOST DC1'],
                    ['slug' => 'dost-dc2', 'code' => 'INH', 'title' => 'DOST DC2'],
                    ['slug' => 'tools-standardization', 'code' => 'INH', 'title' => 'Tools Standardization'],
                    ['slug' => 'oneissp', 'code' => 'ISS', 'title' => 'OneISSP'],
                ],
            ],
            [
                'slug' => 'it-governance',
                'domain' => 'technology',
                'title' => 'I.T. Governance',
                'description' => 'Governance, prioritization, PM capability, and organization development for sustained DX delivery.',
                'projects' => [
                    ['slug' => 'opm3', 'code' => 'GOV', 'title' => 'OPM3'],
                    ['slug' => 'global-pm-training', 'code' => 'GOV', 'title' => 'Global PM Training'],
                    ['slug' => 'prioritization-model', 'code' => 'GOV', 'title' => 'Prioritization Model'],
                    ['slug' => 'agency-regional-offices-engagement', 'code' => 'GOV', 'title' => 'Agency & Regional Offices Engagement'],
                    ['slug' => 'secsta', 'code' => 'GOV', 'title' => 'SecSta'],
                    ['slug' => 'lnd-planning-system', 'code' => 'GOV', 'title' => 'LnD Planning System'],
                    ['slug' => 'dost-software-development-pack', 'code' => 'GOV', 'title' => 'DOST Software Development Pack'],
                    ['slug' => 'pscm', 'code' => 'GOV', 'title' => 'PSCM'],
                    ['slug' => 'itsm', 'code' => 'GOV', 'title' => 'ITSM'],
                    ['slug' => 'productivity-analysis', 'code' => 'GOV', 'title' => 'Productivity Analysis'],
                    ['slug' => 'knowledge-management', 'code' => 'GOV', 'title' => 'Knowledge Management'],
                    ['slug' => 'spms', 'code' => 'PES', 'title' => 'SPMS'],
                    ['slug' => 'nstip-development', 'code' => 'PES', 'title' => 'NSTIP Development'],
                    ['slug' => 'planning-officers-capacity-building', 'code' => 'PES', 'title' => 'Planning Officers Capacity Building'],
                    ['slug' => 'organizational-development-plan', 'code' => 'PES', 'title' => 'Organizational Development Plan'],
                ],
            ],
            [
                'slug' => 'process-harmonization',
                'domain' => 'process',
                'title' => 'Process Harmonization',
                'description' => 'Proposal, project, transfer, architecture, capability, and process improvement initiatives.',
                'projects' => [
                    ['slug' => 'enhanced-proposal-process', 'code' => 'PRH', 'title' => 'Enhanced Proposal Process'],
                    ['slug' => 'project-management-process', 'code' => 'PRH', 'title' => 'Project Management Process'],
                    ['slug' => 'project-change-management', 'code' => 'PRH', 'title' => 'Project Change Management'],
                    ['slug' => 'tech-transfer-framework', 'code' => 'PRH', 'title' => 'Tech Transfer Framework'],
                    ['slug' => 'process-mapping', 'code' => 'PRH', 'title' => 'Process Mapping'],
                    ['slug' => 'configuration-management', 'code' => 'PRH', 'title' => 'Configuration Management'],
                    ['slug' => 'change-management', 'code' => 'PRH', 'title' => 'Change Management'],
                    ['slug' => 'resource-management', 'code' => 'PRH', 'title' => 'Resource Management'],
                    ['slug' => 'capacity-management', 'code' => 'PRH', 'title' => 'Capacity Management'],
                    ['slug' => 'asset-lifecycle-management', 'code' => 'PRH', 'title' => 'Asset Lifecycle Management'],
                    ['slug' => 'foresight-framework-development', 'code' => 'PRH', 'title' => 'Foresight Framework Development'],
                    ['slug' => 'togaf', 'code' => 'PRH', 'title' => 'TOGAF'],
                    ['slug' => 'enterprise-architecture-development', 'code' => 'PRH', 'title' => 'Enterprise Architecture Development'],
                    ['slug' => 'dost-pmm', 'code' => 'PRH', 'title' => 'DOST PMM'],
                    ['slug' => 'qms', 'code' => 'PRH', 'title' => 'QMS'],
                    ['slug' => 'pqa', 'code' => 'PRH', 'title' => 'PQA'],
                    ['slug' => 'cmmi', 'code' => 'PRH', 'title' => 'CMMI'],
                    ['slug' => 'curriculum-based-learning', 'code' => 'PRH', 'title' => 'Curriculum Based Learning'],
                    ['slug' => 'competency-based-development', 'code' => 'PRH', 'title' => 'Competency Based Development'],
                ],
            ],
        ];
    }
};
