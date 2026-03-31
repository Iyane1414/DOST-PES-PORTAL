<?php

namespace App\Http\Controllers;

use App\Models\AiSetting;
use App\Models\ContactMessage;
use App\Models\DxItem;
use App\Models\Issuance;
use App\Models\IssuanceCategory;
use App\Models\Material;
use App\Models\Subscription;
use App\Models\WebsiteVisit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PortalController extends Controller
{
    public function index(Request $request): View
    {
        $this->recordWebsiteVisit($request);

        $issuances = Issuance::query()->latest('date')->get();
        $materials = Material::query()->latest('date')->get();
        $materialTypes = $materials
            ->pluck('type')
            ->filter()
            ->unique()
            ->sort()
            ->values();
        $divisions = $this->organizationDivisions();
        $dxCoreDomains = $this->dxCoreDomains();
        $dxSubPrograms = $this->dxSubPrograms();
        $categories = IssuanceCategory::query()->orderBy('name')->get();
        if ($categories->isEmpty()) {
            $categories = collect($this->defaultIssuanceCategories())->map(fn (string $name) => (object) ['name' => $name]);
        }
        $dxItems = DxItem::query()->orderBy('category')->orderBy('title')->get();
        $search = $request->string('search')->toString();
        $categoryFilter = $request->string('category')->toString();
        $materialSearch = $request->string('material_search')->toString();
        $materialTypeFilter = $request->string('material_type')->toString();
        $normalizedIssuanceSearch = Str::lower(trim($search));
        $normalizedCategoryFilter = trim($categoryFilter);

        $filteredIssuances = $issuances->filter(function (Issuance $item) use ($normalizedIssuanceSearch, $normalizedCategoryFilter) {
            $searchableText = Str::lower(implode(' ', array_filter([
                $item->title,
                $item->category,
                $item->division,
                optional($item->date)->format('F d, Y'),
                optional($item->date)->format('Y-m-d'),
            ])));

            $matchesSearch = $normalizedIssuanceSearch === '' || str_contains($searchableText, $normalizedIssuanceSearch);
            $matchesCategory = $normalizedCategoryFilter === '' || $normalizedCategoryFilter === 'All' || $item->category === $normalizedCategoryFilter;

            return $matchesSearch && $matchesCategory;
        })->values();

        $latestItems = $issuances
            ->map(fn (Issuance $item) => [
                'label' => 'Issuance',
                'title' => $item->title,
                'date' => optional($item->date)->format('M d, Y'),
                'url' => $item->url ?: '#',
            ])
            ->merge($materials->map(fn (Material $item) => [
                'label' => 'Material',
                'title' => $item->title,
                'date' => optional($item->date)->format('M d, Y'),
                'url' => $item->url ?: '#',
            ]))
            ->sortByDesc('date')
            ->take(6)
            ->values();

        $analytics = collect(range(1, 6))->map(function (int $offset) {
            $date = now()->subMonths(6 - $offset);

            return [
                'label' => $date->format('M'),
                'issuances' => Issuance::query()->whereYear('date', $date->year)->whereMonth('date', $date->month)->count(),
                'materials' => Material::query()->whereYear('date', $date->year)->whereMonth('date', $date->month)->count(),
            ];
        });

        $pesInActionItems = collect([
            [
                'id' => 'project-lodi',
                'eyebrow' => 'Featured',
                'title' => 'DOST Project LODI',
                'date' => 'August 4, 2022',
                'summary' => 'Boosting DOST\'s digital transformation while giving solid IT internship support for science scholars.',
                'copy' => 'The Department of Science and Technology-Science Education Institute (DOST-SEI) and the DOST Planning and Evaluation Service (DOST-PES) signed a partnership deal for the Project League of Developers Initiative (Project LODI) on August 4, 2022 at the DOST Compound in Taguig City. Project LODI enables the digital transformation of DOST with the help of IT students and DOST scholars, strengthening collaboration between institutional planning and digital capability-building.',
                'image' => 'images/p1.png',
                'image_alt' => 'DOST Project LODI',
                'accent' => 'cyan',
            ],
            [
                'id' => 'dx-roadmap',
                'eyebrow' => 'Update',
                'title' => 'DOST PES Digital Transformation Roadmap Released',
                'date' => 'February 18, 2026',
                'summary' => 'A new roadmap aligns service modernization, planning workflows, and data-ready operations across PES.',
                'copy' => 'PES introduced its digital transformation roadmap to guide service improvements in planning, monitoring, records management, and information systems coordination. The roadmap outlines priority workstreams for process redesign, internal platforms, and stronger cross-office data sharing to support faster and more consistent decision-making.',
                'image' => null,
                'image_alt' => 'DX Roadmap',
                'accent' => 'blue',
            ],
            [
                'id' => 'planning-workshop',
                'eyebrow' => 'Event',
                'title' => 'PES Annual Planning & Evaluation Workshop 2026',
                'date' => 'January 28, 2026',
                'summary' => 'Planning and evaluation leads gathered to align targets, reporting cycles, and priority outcomes for the year.',
                'copy' => 'The annual PES workshop convened division representatives and partner offices to align targets, refine milestone indicators, and review reporting expectations for the year. The session also focused on stronger coordination between planning activities and evaluation outputs so that institutional reporting stays timely and evidence-based.',
                'image' => null,
                'image_alt' => 'Workshop',
                'accent' => 'gold',
            ],
            [
                'id' => 'rd-survey',
                'eyebrow' => 'Report',
                'title' => 'R&D Survey Coordination Sessions Strengthened',
                'date' => 'November 15, 2025',
                'summary' => 'Preparatory sessions improved coordination for upcoming research and development data collection activities.',
                'copy' => 'PES conducted coordination sessions to support upcoming R&D survey activities, improve indicator alignment, and clarify reporting timelines with partner units. These sessions help ensure the resulting data remains useful for policy analysis, planning support, and national statistical reporting.',
                'image' => null,
                'image_alt' => 'R&D Survey',
                'accent' => 'mint',
            ],
            [
                'id' => 'stred-updates',
                'eyebrow' => 'Division',
                'title' => 'STRED Data Framework Review',
                'date' => 'October 3, 2025',
                'summary' => 'Statistical frameworks and reporting references were reviewed to keep S&T indicators relevant and consistent.',
                'copy' => 'The STRED-led review revisited statistical frameworks, metadata references, and reporting formats used for S&T and R&D monitoring. The activity helps maintain consistency in how datasets are prepared, interpreted, and shared with planners, policymakers, and partner institutions.',
                'image' => null,
                'image_alt' => 'STRED',
                'accent' => 'violet',
            ],
            [
                'id' => 'pcmd-monitoring',
                'eyebrow' => 'Monitoring',
                'title' => 'PCMD Investment Program Validation Support',
                'date' => 'September 9, 2025',
                'summary' => 'Validation work helped align proposed projects with monitoring requirements and investment planning priorities.',
                'copy' => 'PCMD continued technical support for investment program validation by consolidating project details, checking indicator completeness, and assisting coordination with reporting units. The work supports stronger budget preparation and clearer alignment between proposed initiatives and institutional performance targets.',
                'image' => null,
                'image_alt' => 'PCMD',
                'accent' => 'slate',
            ],
        ]);

        $resourceHighlights = collect([
            [
                'type' => 'Policy',
                'title' => 'PES Policy Framework 2026',
                'summary' => 'A sample policy reference covering planning, monitoring, and institutional alignment priorities.',
                'division' => 'Planning Division',
                'date' => 'Mar 01, 2026',
                'url' => '#',
                'icon' => 'bi-shield-check',
            ],
            [
                'type' => 'Annual Report',
                'title' => 'PES Annual Report 2025',
                'summary' => 'Dummy annual performance report with accomplishments, milestone indicators, and institutional highlights.',
                'division' => 'Planning and Evaluation Service',
                'date' => 'Feb 15, 2026',
                'url' => '#',
                'icon' => 'bi-bar-chart-line',
            ],
            [
                'type' => 'R&D Survey',
                'title' => 'R&D Survey Snapshot 2025',
                'summary' => 'Illustrative survey summary for research and development trends, program relevance, and reporting support.',
                'division' => 'Evaluation Division',
                'date' => 'Jan 20, 2026',
                'url' => '#',
                'icon' => 'bi-clipboard-data',
            ],
            [
                'type' => 'Presentation',
                'title' => 'Strategic Planning Presentation Deck',
                'summary' => 'Presentation material for planning sessions, roadmap alignment, and cross-office coordination.',
                'division' => 'Planning Division',
                'date' => 'Jan 12, 2026',
                'url' => '#',
                'icon' => 'bi-easel2',
            ],
        ]);

        $resourceCollections = $this->resourceCollections($materials);

        $selectedResourceCollection = $resourceCollections->first(function (array $collection) use ($materialTypeFilter) {
            return $materialTypeFilter !== '' && $materialTypeFilter !== 'All' && Str::lower($materialTypeFilter) === Str::lower($collection['filter']);
        });

        return view('portal.index', [
            'issuances' => $issuances,
            'materials' => $materials,
            'divisions' => $divisions,
            'dxCoreDomains' => $dxCoreDomains,
            'dxSubPrograms' => $dxSubPrograms,
            'materialTypes' => $materialTypes,
            'categories' => $categories,
            'dxDomains' => $dxItems->where('category', 'domain')->values(),
            'dxPrograms' => $dxItems->where('category', 'program')->values(),
            'latestItems' => $latestItems,
            'analytics' => $analytics,
            'issuanceCount' => $issuances->count(),
            'materialCount' => $materials->count(),
            'filteredIssuances' => $filteredIssuances,
            'search' => $search,
            'categoryFilter' => $categoryFilter,
            'materialSearch' => $materialSearch,
            'materialTypeFilter' => $materialTypeFilter,
            'resourceCollections' => $resourceCollections,
            'selectedResourceCollection' => $selectedResourceCollection,
            'pesInActionItems' => $pesInActionItems,
            'resourceHighlights' => $resourceHighlights,
        ]);
    }

    public function dxProgramShow(Request $request, string $domainSlug, string $subProgramSlug): View
    {
        $domain = $this->dxCoreDomains()->firstWhere('slug', $domainSlug);
        abort_unless($domain, 404);

        $subProgram = $this->dxSubPrograms()->first(function (array $item) use ($domainSlug, $subProgramSlug) {
            return $item['domain'] === $domainSlug && $item['slug'] === $subProgramSlug;
        });

        abort_unless($subProgram, 404);

        $search = $request->string('search')->toString();
        $projects = collect($subProgram['projects']);
        $filteredProjects = $projects
            ->filter(fn (array $project) => $this->dxProjectMatchesSearch($project, $search))
            ->values();

        return view('portal.dx.show', [
            'title' => $subProgram['title'].' - DOST DX',
            'dxDomain' => $domain,
            'dxSubProgram' => $subProgram,
            'dxProjects' => $projects->map(function (array $project) use ($search) {
                $project['matches_search'] = $this->dxProjectMatchesSearch($project, $search);

                return $project;
            })->values(),
            'dxProjectCount' => $filteredProjects->count(),
            'dxTotalProjectCount' => $projects->count(),
            'search' => $search,
        ]);
    }

    private function dxProjectMatchesSearch(array $project, string $search): bool
    {
        $normalizedSearch = Str::of($search)->lower()->trim()->value();

        if ($normalizedSearch === '') {
            return true;
        }

        $projectCode = Str::of($project['code'] ?? '')->lower()->trim()->value();
        $projectTitle = Str::of($project['title'] ?? '')->lower()->trim()->value();
        $titleWords = preg_split('/\s+/', $projectTitle, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        if ($projectCode !== '' && str_starts_with($projectCode, $normalizedSearch)) {
            return true;
        }

        if ($projectTitle !== '' && str_starts_with($projectTitle, $normalizedSearch)) {
            return true;
        }

        foreach ($titleWords as $word) {
            if (str_starts_with($word, $normalizedSearch)) {
                return true;
            }
        }

        return str_contains($projectTitle, $normalizedSearch);
    }

    public function materialCollection(Request $request, string $collectionSlug): View
    {
        $materials = Material::query()->latest('date')->get();
        $resourceCollections = $this->resourceCollections($materials);
        $resourceCollection = $resourceCollections->firstWhere('slug', $collectionSlug);

        abort_unless($resourceCollection, 404);

        $search = $request->string('search')->toString();
        $year = $request->string('year')->toString();
        $collectionMaterials = $materials->filter(fn (Material $item) => $this->matchesResourceCollection($item, $resourceCollection))->values();

        $availableYears = $collectionMaterials
            ->map(fn (Material $item) => optional($item->date)->format('Y'))
            ->filter()
            ->unique()
            ->sortDesc()
            ->values();

        $filteredMaterials = $collectionMaterials->filter(function (Material $item) use ($search, $year) {
            $matchesSearch = $search === '' || str_contains(Str::lower($item->title.' '.$item->division.' '.$item->type), Str::lower($search));
            $matchesYear = $year === '' || $year === 'All' || optional($item->date)->format('Y') === $year;

            return $matchesSearch && $matchesYear;
        })->values();

        return view('portal.resources.show', [
            'title' => $resourceCollection['label'].' - DOST PES',
            'resourceCollection' => $resourceCollection,
            'resourceCollections' => $resourceCollections,
            'materials' => $filteredMaterials,
            'materialsCount' => $filteredMaterials->count(),
            'totalMaterialsCount' => $collectionMaterials->count(),
            'search' => $search,
            'year' => $year,
            'availableYears' => $availableYears,
        ]);
    }

    private function organizationDivisions()
    {
        return collect([
            (object) [
                'id' => 'pdpd',
                'name' => 'Policy Development and Planning Division',
                'abbr' => 'PDPD',
                'description' => 'The Policy Development and Planning Division, headed by a Planning Officer V, has the following functions: (a) formulates science, technology and innovation (STI) plans based on the country\'s overall development vision; (b) undertakes continuing review of policies to ensure their conformity with national development plans, consistent with scientific requirements, relevance and desirability in the light of changing situations; (c) identifies and analyzes STI policy issues and directs research studies to formulate appropriate policy alternatives and recommendations; (d) provides information, recommendations and guidance on STI policy issues; and (e) plans the continuous improvement of the STI policy formulation framework and provides overall direction in the implementation and/or utilization of policy-oriented research studies.',
                'head' => null,
            ],
            (object) [
                'id' => 'pcmd',
                'name' => 'Program Coordination and Monitoring Division',
                'abbr' => 'PCMD',
                'description' => 'The Program Coordination and Monitoring Division, headed by a Project Development Officer V, has the following functions: (a) monitor and evaluate performance of DOST agencies based on developed and identified major final outputs (MFO) and performance indicators; (b) evaluate and endorse project proposals to National Economic Development Authority (NEDA) for foreign assistance; (c) provide technical support to the analysis and preparation of the DOST-Wide budgetary proposal; (d) prepare DOST performance report and other pertinent DOST reports required by national authorities (e.g. OP-PMS, NEDA, Congress, Senate, DBM, DSWD, DOLE, TESDA, NAPC, etc.); and (e) coordinate the preparation/validation of the Public Investment Program (PIP).',
                'head' => null,
            ],
            (object) [
                'id' => 'straed',
                'name' => 'S&T Resource Assessment and Evaluation Division',
                'abbr' => 'STRAED',
                'description' => 'The S&T Resource Assessment and Evaluation Division, headed by Planning Officer V, has the following functions: (a) develop the S&T and R&D Statistical System and generate basic data as basis for policy decisions and measures; (b) formulate the framework for S&T statistics and indicators and generates S&T indicators based on framework; (c) implement the periodic nationwide survey (every 2 years) of R&D expenditures and human resources as basis for white papers released by government on science and technology; (d) disseminates S&T/R&D statistics and indicators to S&T planners, policy makers and other stakeholders; (e) coordinate with local and foreign statistical bodies concerned with S&T statistics; and (f) update S&T and R&D statistics for Philippine Yearbook, World/Global Competitiveness Report, UNESCO Institute for Statistics and ASEAN S&T Indicators.',
                'head' => null,
            ],
            (object) [
                'id' => 'itd',
                'name' => 'Information Technology Division',
                'abbr' => 'ITD',
                'description' => 'The Information Technology Division is tasked to: (a) administer, maintain and enhance existing network infrastructure, application systems, websites and other programs that serve as tools in the provision of information necessary for decision-making; (b) implement proper security measures to ensure the efficient operation of the available information-technology facilities; and, (c) formulate framework for the management information services, including but not limited to the DOST-CO Information Systems Strategic Plan (ISSP).',
                'head' => null,
            ],
        ]);
    }

    private function defaultIssuanceCategories(): array
    {
        return ['Circular', 'Letter', 'Memorandum', 'Notice', 'Order'];
    }

    private function resourceCollections($materials)
    {
        return collect([
            [
                'slug' => 'policies',
                'label' => 'Policies',
                'filter' => 'Policy',
                'anchor' => 'materials-policies',
                'icon' => 'bi-file-earmark-text',
                'eyebrow' => 'Policy',
                'description' => 'Official PES policy documents and guidelines.',
                'page_copy' => 'No policies have been uploaded yet.',
                'artwork' => 'policy',
            ],
            [
                'slug' => 'annual-report',
                'label' => 'Annual Report',
                'filter' => 'Annual Report',
                'anchor' => 'materials-annual-report',
                'icon' => 'bi-bar-chart',
                'eyebrow' => 'Report',
                'description' => 'Year-end performance and accomplishment reports.',
                'page_copy' => 'No annual reports have been uploaded yet.',
                'artwork' => 'report',
            ],
            [
                'slug' => 'rd-survey',
                'label' => 'R&D Survey',
                'filter' => 'R&D Survey',
                'anchor' => 'materials-rd-survey',
                'icon' => 'bi-search',
                'eyebrow' => 'Survey',
                'description' => 'National R&D expenditure and human resource data.',
                'page_copy' => 'No R&D survey files have been uploaded yet.',
                'artwork' => 'survey',
            ],
            [
                'slug' => 'presentations',
                'label' => 'Presentations',
                'filter' => 'Presentation',
                'anchor' => 'materials-presentations',
                'icon' => 'bi-display',
                'eyebrow' => 'Slides',
                'description' => 'Slide decks and visual reports from PES divisions.',
                'page_copy' => 'No presentations have been uploaded yet.',
                'artwork' => 'slides',
            ],
        ])->map(function (array $collection) use ($materials) {
            $collection['count'] = $materials->filter(fn (Material $item) => $this->matchesResourceCollection($item, $collection))->count();

            return $collection;
        });
    }

    private function dxCoreDomains()
    {
        return collect([
            [
                'key' => 'people',
                'slug' => 'people',
                'title' => 'People',
                'icon' => 'bi-person',
                'image' => 'images/people.png',
                'description' => 'Individuals within the organization, their skills, knowledge, and how they interact with processes and technology including organizational structures.',
                'default_sub_program' => 'structure-rationalization',
            ],
            [
                'key' => 'process',
                'slug' => 'process',
                'title' => 'Process',
                'icon' => 'bi-activity',
                'image' => 'images/process.png',
                'description' => 'Encompasses the workflows, procedures, and methodologies used to complete tasks and achieve goals.',
                'default_sub_program' => 'process-harmonization',
            ],
            [
                'key' => 'technology',
                'slug' => 'technology',
                'title' => 'Technology',
                'icon' => 'bi-pc-display',
                'image' => 'images/technology.png',
                'description' => 'Infrastructure, tools, information systems, and software used to support and enhance processes and the work of individuals.',
                'default_sub_program' => 'cybersecurity',
            ],
        ]);
    }

    private function dxSubPrograms()
    {
        return collect([
            [
                'slug' => 'structure-rationalization',
                'domain' => 'people',
                'domain_label' => 'People',
                'title' => 'Structure Rationalization',
                'description' => 'Organizational structuring, chartering, planning, budgeting, and assessment initiatives under the People domain.',
                'accent' => 'amber',
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
                'domain_label' => 'Technology',
                'title' => 'Cybersecurity',
                'description' => 'Security, access, privacy, and cyber-readiness projects supporting safe digital operations.',
                'accent' => 'cyan',
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
                'domain_label' => 'Technology',
                'title' => 'IS Harmonization',
                'description' => 'Integration, portal, analytics, and harmonization workstreams for DOST information systems.',
                'accent' => 'green',
                'projects' => [
                    ['slug' => 'depmis', 'code' => 'ISH', 'title' => 'DEPMIS'],
                    ['slug' => 'integrations', 'code' => 'ISH', 'title' => 'Integrations'],
                    ['slug' => 'is-harmonization', 'code' => 'ISH', 'title' => 'IS Harmonization'],
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
                'domain_label' => 'Technology',
                'title' => 'Infra Harmonization',
                'description' => 'Connectivity, cloud, repository, and infrastructure standardization initiatives across DOST.',
                'accent' => 'blue',
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
                'domain_label' => 'Technology',
                'title' => 'I.T. Governance',
                'description' => 'Governance, prioritization, PM capability, and organization development for sustained DX delivery.',
                'accent' => 'ice',
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
                'domain_label' => 'Process',
                'title' => 'Process Harmonization',
                'description' => 'Proposal, project, transfer, architecture, capability, and process improvement initiatives.',
                'accent' => 'violet',
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
        ]);
    }

    private function matchesResourceCollection(Material $item, array $collection): bool
    {
        $type = Str::lower($item->type);
        $title = Str::lower($item->title);

        return match ($collection['slug']) {
            'policies' => str_contains($type, 'policy'),
            'annual-report' => str_contains($type, 'annual'),
            'rd-survey' => str_contains($type, 'survey') || str_contains($title, 'survey'),
            'presentations' => str_contains($type, 'presentation') || str_contains($type, 'ppt') || str_contains($type, 'powerpoint'),
            default => false,
        };
    }

    public function contact(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        ContactMessage::query()->create($data);

        return back()->with('contact_status', 'Message sent successfully.');
    }

    public function subscribe(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:255', 'unique:subscriptions,email'],
        ]);

        Subscription::query()->create($data);

        return back()->with('subscription_status', 'Thank you for subscribing.');
    }

    public function assistant(Request $request): JsonResponse
    {
        $data = $request->validate([
            'message' => ['required', 'string', 'max:1000'],
        ]);

        $message = trim($data['message']);
        $normalizedMessage = Str::lower($message);
        $fallback = $this->buildFallbackResponse($normalizedMessage);
        $openAiKey = config('services.openai.key');
        $openAiModel = config('services.openai.model', 'gpt-4o-mini');
        $aiSetting = AiSetting::query()->first();
        $sources = $this->assistantSourcesForMessage($message);
        $refusalMessage = $aiSetting?->refusal_message ?: 'I can only help with PES-related information available in this portal, such as mandates, divisions, issuances, materials, contact details, and DOST DX content.';

        if (! $openAiKey) {
            return response()->json(['reply' => $fallback]);
        }

        if ($this->isOutsidePesScope($normalizedMessage, $sources)) {
            return response()->json(['reply' => $refusalMessage]);
        }

        try {
            $assistantContext = $this->buildAssistantContext($sources);
            $systemPrompt = $aiSetting?->system_prompt ?: 'You are the PES AI Assistant for the DOST Planning and Evaluation Service. Answer only with PES-related information found in the provided portal context. Be concise, factual, and helpful. Use citation-style references from the supplied source list when possible.';
            $scopePrompt = $aiSetting?->scope_prompt ?: 'Only answer questions about PES mandates, divisions, issuances, materials, contact details, DOST DX, and information clearly present in the portal database context. If a question is outside PES scope, refuse briefly.';
            $response = Http::timeout(20)
                ->withToken($openAiKey)
                ->acceptJson()
                ->post('https://api.openai.com/v1/responses', [
                    'model' => $openAiModel,
                    'instructions' => $systemPrompt."\n\n".$scopePrompt."\n\n".'If you answer, cite matching portal sources inline using the exact citation labels provided in the context, for example [Source: Issuance - Sample Title]. If there is not enough support in the provided context, say so briefly and do not invent details.'."\n\n".$assistantContext,
                    'input' => $message,
                ]);

            if ($response->failed()) {
                throw new \RuntimeException('OpenAI request failed.');
            }

            $payload = $response->json();
            $outputTextChunk = collect(data_get($payload, 'output', []))
                ->flatMap(fn ($item) => data_get($item, 'content', []))
                ->firstWhere('type', 'output_text');
            $text = data_get($payload, 'output_text') ?: data_get($outputTextChunk, 'text');

            return response()->json(['reply' => $text ?: $fallback]);
        } catch (\Throwable) {
            return response()->json(['reply' => $fallback]);
        }
    }

    private function buildAssistantContext(array $sources = []): string
    {
        $divisionSummary = $this->organizationDivisions()
            ->map(fn ($division) => '[Source: Division - '.$division->name.'] '.$division->abbr.': '.$division->name)
            ->implode('; ');

        $dxDomains = DxItem::query()
            ->where('category', 'domain')
            ->pluck('title')
            ->implode(', ');

        $dxPrograms = DxItem::query()
            ->where('category', 'program')
            ->take(6)
            ->pluck('title')
            ->implode(', ');

        $sourceLines = collect($sources)
            ->map(function (array $source) {
                return $source['citation'].' '.$source['summary'];
            })
            ->implode("\n");

        return implode("\n", array_filter([
            'PES mandate: PES leads strategic planning and evaluation for DOST, aligning programs with national priorities and impact assessment frameworks.',
            $divisionSummary !== '' ? 'Current PES divisions: '.$divisionSummary.'.' : null,
            $dxDomains !== '' ? 'DOST DX core domains: '.$dxDomains.'.' : null,
            $dxPrograms !== '' ? 'DOST DX sub-programs: '.$dxPrograms.'.' : null,
            'Official contact details: DOST Complex, Gen Santos Ave., Bicutan, Taguig City, Philippines; phone +63 (2) 8837-2071 to 82; email pes@dost.gov.ph; office hours Monday to Thursday, 8:00AM to 5:00PM.',
            $sourceLines !== '' ? "Matched portal sources:\n".$sourceLines : 'Matched portal sources: none strongly matched for this question.',
        ]));
    }

    private function assistantSourcesForMessage(string $message): array
    {
        $needle = Str::lower($message);
        $terms = collect(preg_split('/\s+/', $needle) ?: [])
            ->map(fn ($term) => preg_replace('/[^a-z0-9&-]/', '', $term ?? ''))
            ->filter(fn ($term) => filled($term) && strlen($term) >= 2)
            ->values();

        $scoreText = function (string $text) use ($needle, $terms): int {
            $haystack = Str::lower($text);
            $score = $needle !== '' && str_contains($haystack, $needle) ? 12 : 0;

            foreach ($terms as $term) {
                if (str_contains($haystack, $term)) {
                    $score += 3;
                }
            }

            return $score;
        };

        $issuanceSources = Issuance::query()->latest('date')->get()->map(function (Issuance $issuance) use ($scoreText) {
            $summary = $issuance->title.' | category: '.($issuance->category ?: 'Uncategorized').' | division: '.($issuance->division ?: 'PES').' | date: '.(optional($issuance->date)->format('F d, Y') ?: 'No date');

            return [
                'score' => $scoreText($summary),
                'citation' => '[Source: Issuance - '.$issuance->title.']',
                'summary' => $summary,
            ];
        });

        $materialSources = Material::query()->latest('date')->get()->map(function (Material $material) use ($scoreText) {
            $summary = $material->title.' | type: '.($material->type ?: 'Material').' | division: '.($material->division ?: 'PES').' | date: '.(optional($material->date)->format('F d, Y') ?: 'No date');

            return [
                'score' => $scoreText($summary),
                'citation' => '[Source: Material - '.$material->title.']',
                'summary' => $summary,
            ];
        });

        $divisionSources = $this->organizationDivisions()->map(function ($division) use ($scoreText) {
            $summary = $division->name.' | abbreviation: '.$division->abbr.' | '.$division->description;

            return [
                'score' => $scoreText($summary),
                'citation' => '[Source: Division - '.$division->name.']',
                'summary' => $summary,
            ];
        });

        $dxSources = DxItem::query()->orderBy('category')->orderBy('title')->get()->map(function (DxItem $item) use ($scoreText) {
            $summary = $item->title.' | category: '.$item->category.' | '.$item->description;

            return [
                'score' => $scoreText($summary),
                'citation' => '[Source: DOST DX - '.$item->title.']',
                'summary' => $summary,
            ];
        });

        $staticSources = collect([
            [
                'score' => $scoreText('PES mandate planning evaluation service DOST mandate strategic planning evaluation national priorities impact assessment'),
                'citation' => '[Source: PES Mandate]',
                'summary' => 'PES leads strategic planning and evaluation for DOST, aligning programs with national priorities and impact assessment frameworks.',
            ],
            [
                'score' => $scoreText('PES contact office address DOST Complex Gen Santos Ave Bicutan Taguig City phone +63 (2) 8837-2071 to 82 email pes@dost.gov.ph office hours Monday to Thursday 8:00AM to 5:00PM'),
                'citation' => '[Source: PES Contact Information]',
                'summary' => 'Office address: DOST Complex, Gen Santos Ave., Bicutan, Taguig City, Philippines. Phone: +63 (2) 8837-2071 to 82. Email: pes@dost.gov.ph. Office hours: Monday to Thursday, 8:00AM to 5:00PM.',
            ],
        ]);

        return $issuanceSources
            ->concat($materialSources)
            ->concat($divisionSources)
            ->concat($dxSources)
            ->concat($staticSources)
            ->filter(fn (array $source) => $source['score'] > 0)
            ->sortByDesc('score')
            ->take(8)
            ->values()
            ->all();
    }

    private function isOutsidePesScope(string $message, array $sources): bool
    {
        if ($message === '') {
            return false;
        }

        $scopeKeywords = [
            'pes', 'planning', 'evaluation', 'mandate', 'division', 'issuance', 'material',
            'policy', 'report', 'survey', 'presentation', 'dx', 'digital', 'contact',
            'office', 'taguig', 'dost', 'pcmd', 'pdpd', 'stred', 'itd',
        ];

        $hasScopeKeyword = collect($scopeKeywords)->contains(fn ($keyword) => str_contains($message, $keyword));

        return ! $hasScopeKeyword && count($sources) === 0;
    }

    private function buildFallbackResponse(string $message): string
    {
        if (str_contains($message, 'mandate')) {
            return 'PES leads strategic planning and evaluation for DOST, aligning programs with national priorities and impact assessment frameworks.';
        }

        if (str_contains($message, 'division')) {
            $divisions = $this->organizationDivisions()->pluck('name')->implode(', ');

            return $divisions
                ? 'Current PES divisions include '.$divisions.'.'
                : 'Division records are available in the portal once populated by the admin team.';
        }

        if (str_contains($message, 'issuance')) {
            $latest = Issuance::query()->latest('date')->take(3)->pluck('title')->implode('; ');

            return $latest
                ? 'Latest issuances include '.$latest.'.'
                : 'No issuances are published yet.';
        }

        if (str_contains($message, 'dx') || str_contains($message, 'digital')) {
            $domains = DxItem::query()->where('category', 'domain')->pluck('title')->implode(', ');

            return $domains
                ? 'DOST DX currently highlights these core domains: '.$domains.'.'
                : 'DOST DX content is available through the admin dashboard.';
        }

        return 'I can help with PES mandates, divisions, issuances, materials, and DOST DX updates.';
    }

    private function recordWebsiteVisit(Request $request): void
    {
        $sessionKey = 'portal_home_visit_recorded';

        if ($request->session()->get($sessionKey)) {
            return;
        }

        WebsiteVisit::query()->create([
            'page' => 'home',
            'session_id' => $request->session()->getId(),
            'ip_address' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 1000, ''),
            'visited_at' => now(),
        ]);

        $request->session()->put($sessionKey, true);
    }
}
