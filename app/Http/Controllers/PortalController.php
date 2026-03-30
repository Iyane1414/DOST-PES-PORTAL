<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\DxItem;
use App\Models\Issuance;
use App\Models\IssuanceCategory;
use App\Models\Material;
use App\Models\Subscription;
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
        $issuances = Issuance::query()->latest('date')->get();
        $materials = Material::query()->latest('date')->get();
        $materialTypes = $materials
            ->pluck('type')
            ->filter()
            ->unique()
            ->sort()
            ->values();
        $divisions = $this->organizationDivisions();
        $categories = IssuanceCategory::query()->orderBy('name')->get();
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
                'description' => 'The Policy Development and Planning Division. Its main responsibilities are to create plans for science, technology, and innovation (STI) that support the country\'s development goals. It also reviews existing policies to make sure they match national plans and remain useful as situations change. The division studies STI policy issues, conducts research, and develops recommendations and policy options. In addition, it provides information and guidance on STI policies and works to improve the process of creating and implementing STI policies.',
                'head' => null,
            ],
            (object) [
                'id' => 'pcmd',
                'name' => 'Program Coordination and Monitoring Division',
                'abbr' => 'PCMD',
                'description' => 'The Program Coordination and Monitoring Division monitors and evaluates the performance of DOST agencies using their major outputs and performance indicators. The division also reviews and recommends project proposals to the National Economic and Development Authority (NEDA) for foreign funding. In addition, it provides technical support in preparing the DOST budget proposal, prepares performance and other required reports for national agencies, and helps coordinate and validate the Public Investment Program (PIP).',
                'head' => null,
            ],
            (object) [
                'id' => 'stred',
                'name' => 'S&T Resource Evaluation Division',
                'abbr' => 'STRED',
                'description' => 'The S&T Resource Assessment and Evaluation Division develops and manages the statistical system for science and technology (S&T) and research and development (R&D) to provide data for policy decisions. The division also creates frameworks and indicators for S&T statistics and conducts a nationwide survey every two years on R&D spending and human resources. In addition, it shares S&T and R&D statistics with planners, policymakers, and stakeholders, coordinates with local and international statistical organizations, and updates S&T data for national and international reports.',
                'head' => null,
            ],
            (object) [
                'id' => 'itd',
                'name' => 'Information Technology Division',
                'abbr' => 'ITD',
                'description' => 'The Information Technology Division manages and improves the organization network system, applications, website, and other digital tools used for decision-making. It also ensures the security and proper operation of IT facilities. In addition, the division develops frameworks for managing information services, including the DOST Central Office Information Systems Strategic Plan (ISSP).',
                'head' => null,
            ],
        ]);
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

        $message = Str::lower($data['message']);
        $fallback = $this->buildFallbackResponse($message);

        if (! config('services.gemini.key')) {
            return response()->json(['reply' => $fallback]);
        }

        try {
            $response = Http::timeout(15)
                ->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key='.config('services.gemini.key'), [
                    'system_instruction' => [
                        'parts' => [[
                            'text' => 'You are the PES AI Assistant for the DOST Planning and Evaluation Service. Be concise, factual, and focus on PES mandates, divisions, issuances, materials, and DOST DX.',
                        ]],
                    ],
                    'contents' => [[
                        'parts' => [[
                            'text' => $data['message'],
                        ]],
                    ]],
                ]);

            $text = data_get($response->json(), 'candidates.0.content.parts.0.text');

            return response()->json(['reply' => $text ?: $fallback]);
        } catch (\Throwable) {
            return response()->json(['reply' => $fallback]);
        }
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
}
