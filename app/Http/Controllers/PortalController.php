<?php

namespace App\Http\Controllers;

use App\Models\AiSetting;
use App\Models\ContactMessage;
use App\Models\DxItem;
use App\Models\GatesProject;
use App\Models\Issuance;
use App\Models\IssuanceCategory;
use App\Models\Material;
use App\Models\News;
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
        $gatesProjects = GatesProject::query()->where('is_active', true)->orderBy('sort_order')->latest('date')->get();
        $newsItems = News::query()->latest('date')->take(6)->get();
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
        $dxItems = DxItem::query()->whereNotNull('slug')->orderBy('category')->orderBy('sort_order')->orderBy('title')->get();
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
            ->concat($materials->map(fn (Material $item) => [
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

        $pesInActionItems = $newsItems;

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
            'gatesProjects' => $gatesProjects,
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
            'gatesProjectCount' => $gatesProjects->count(),
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
        $domain = $this->dxAllDomains()->firstWhere('slug', $domainSlug);
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

    public function gatesCollection(Request $request, string $collectionSlug): View
    {
        $gatesProjects = GatesProject::query()->where('is_active', true)->latest('date')->get();
        $gatesCollections = $this->gatesCollections($gatesProjects);
        $gatesCollection = $gatesCollections->firstWhere('slug', $collectionSlug);

        abort_unless($gatesCollection, 404);

        $search = $request->string('search')->toString();
        $year = $request->string('year')->toString();
        $collectionProjects = $gatesProjects->filter(fn (GatesProject $item) => $this->matchesGatesCollection($item, $gatesCollection))->values();

        $availableYears = $collectionProjects
            ->map(fn (GatesProject $item) => optional($item->date)->format('Y'))
            ->filter()
            ->unique()
            ->sortDesc()
            ->values();

        $filteredProjects = $collectionProjects->filter(function (GatesProject $item) use ($search, $year) {
            if ($search === '') {
                $matchesSearch = true;
            } else {
                $normalizedSearch = Str::lower(trim($search));
                $titleMatch = str_starts_with(Str::lower($item->title), $normalizedSearch);
                $codeMatch = str_starts_with(Str::lower($item->code ?? ''), $normalizedSearch);
                $matchesSearch = $titleMatch || $codeMatch;
            }
            $matchesYear = $year === '' || $year === 'All' || optional($item->date)->format('Y') === $year;

            return $matchesSearch && $matchesYear;
        })->values();

        return view('portal.gates.show', [
            'title' => $gatesCollection['label'].' - DOST GATES',
            'gatesCollection' => $gatesCollection,
            'gatesCollections' => $gatesCollections,
            'gatesProjects' => $filteredProjects,
            'gatesProjectsCount' => $filteredProjects->count(),
            'totalGatesProjectsCount' => $collectionProjects->count(),
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
        return ['Guidelines', 'Letter', 'Memorandum', 'Notice', 'Order'];
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
    private function gatesCollections($gatesProjects)
    {
        return collect([
            [
                'slug' => 'projects',
                'label' => 'Projects',
                'filter' => 'Project',
                'anchor' => 'gates-projects',
                'icon' => 'bi-briefcase',
                'eyebrow' => 'Project',
                'description' => 'GATES program projects and initiatives.',
                'page_copy' => 'No GATES projects have been uploaded yet.',
            ],
            [
                'slug' => 'video-presentations',
                'label' => 'Video Presentations',
                'filter' => 'Video Presentation',
                'anchor' => 'gates-videos',
                'icon' => 'bi-play-circle',
                'eyebrow' => 'Video',
                'description' => 'GATES video presentations and demos.',
                'page_copy' => 'No GATES video presentations have been uploaded yet.',
            ],
        ])->map(function (array $collection) use ($gatesProjects) {
            $collection['count'] = $gatesProjects->filter(fn (GatesProject $item) => $this->matchesGatesCollection($item, $collection))->count();

            return $collection;
        });
    }
    private function dxCoreDomains()
    {
        $coreDomainKeys = ['people', 'process', 'technology'];

        return DxItem::query()
            ->with(['children' => fn ($query) => $query->where('category', 'program')->where('is_active', true)->orderBy('sort_order')->orderBy('title')])
            ->where('category', 'domain')
            ->where('is_active', true)
            ->whereNotNull('slug')
            ->whereIn('domain_key', $coreDomainKeys)
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get()
            ->map(function (DxItem $item) {
                $domainKey = $item->domain_key ?: $item->slug;
                $visuals = $this->defaultDxDomainVisuals($domainKey);

                return [
                    'key' => $domainKey,
                    'slug' => $domainKey,
                    'title' => $item->title,
                    'icon' => $item->icon ?: $visuals['icon'],
                    'image' => $item->image_path ?: $visuals['image'],
                    'description' => $item->description,
                    'default_sub_program' => optional($item->children->first())->slug,
                ];
            })
            ->values();
    }

    private function dxAllDomains()
    {
        $domains = $this->dxCoreDomains();

        $othersDomain = [
            'key' => 'other',
            'slug' => 'other',
            'title' => 'Others',
            'icon' => 'bi-grid-3x3-gap',
            'image' => 'images/technology.png',
            'description' => 'Cross-cutting and special DOST DX projects that do not need to sit inside the three main core-domain cards.',
            'default_sub_program' => 'others',
        ];

        if ($domains->contains(fn (array $item) => $item['slug'] === 'other')) {
            return $domains;
        }

        return $domains->push($othersDomain)->values();
    }

    private function dxSubPrograms()
    {
        return DxItem::query()
            ->with([
                'parent',
                'children' => fn ($query) => $query->where('category', 'project')->where('is_active', true)->orderBy('sort_order')->orderBy('title'),
            ])
            ->where('category', 'program')
            ->where('is_active', true)
            ->whereNotNull('slug')
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get()
            ->map(function (DxItem $item) {
                return [
                    'slug' => $item->slug,
                    'domain' => $item->domain_key,
                    'domain_label' => $item->parent?->title ?: ($item->domain_key === 'other' ? 'Others' : Str::headline($item->domain_key)),
                    'title' => $item->title,
                    'description' => $item->description,
                    'accent' => 'cyan',
                    'projects' => $item->children->map(fn (DxItem $project) => [
                        'slug' => $project->slug,
                        'code' => $project->code ?: 'DX',
                        'title' => $project->title,
                        'description' => $project->description,
                        'file_url' => $project->file_url,
                    ])->values()->all(),
                ];
            })
            ->values();
    }

    private function defaultDxDomainVisuals(string $domainKey): array
    {
        return match ($domainKey) {
            'people' => ['icon' => 'bi-person', 'image' => 'images/people.png'],
            'process' => ['icon' => 'bi-activity', 'image' => 'images/process.png'],
            default => ['icon' => 'bi-pc-display', 'image' => 'images/technology.png'],
        };
    }

    private function matchesResourceCollection(Material $item, array $collection): bool
    {
        $type = Str::lower($item->type);
        $title = Str::lower($item->title);

        return match ($collection['slug']) {
            'policies' => str_contains($type, 'policy') || str_contains($type, 'guideline'),
            'annual-report' => str_contains($type, 'annual'),
            'rd-survey' => str_contains($type, 'survey') || str_contains($type, 'r&d') || str_contains($title, 'survey'),
            'presentations' => str_contains($type, 'presentation')
                || str_contains($type, 'ppt')
                || str_contains($type, 'powerpoint')
                || str_contains($type, 'slide')
                || str_contains($type, 'deck')
                || str_contains($type, 'video')
                || str_contains($type, 'infographic'),
            default => false,
        };
    }

    private function matchesGatesCollection(GatesProject $item, array $collection): bool
    {
        $type = Str::lower($item->type);

        return match ($collection['slug']) {
            'projects' => str_contains($type, 'project') && ! str_contains($type, 'video'),
            'video-presentations' => str_contains($type, 'video'),
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
            GatesProject::query()->exists() ? 'DOST GATES project records are also available in the portal.' : null,
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

        $newsSources = News::query()->latest('date')->get()->map(function (News $news) use ($scoreText) {
            $summary = $news->title.' | type: '.($news->eyebrow ?: 'News').' | date: '.(optional($news->date)->format('F d, Y') ?: 'No date').' | '.$news->summary;

            return [
                'score' => $scoreText($summary),
                'citation' => '[Source: News - '.$news->title.']',
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

        $gatesSources = GatesProject::query()->latest('date')->get()->map(function (GatesProject $project) use ($scoreText) {
            $summary = $project->title.' | code: '.($project->code ?: 'GATES').' | '.($project->description ?: 'GATES project');

            return [
                'score' => $scoreText($summary),
                'citation' => '[Source: DOST GATES - '.$project->title.']',
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
            ->concat($newsSources)
            ->concat($divisionSources)
            ->concat($dxSources)
            ->concat($gatesSources)
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
            'pes', 'planning', 'evaluation', 'mandate', 'division', 'issuance', 'material', 'news',
            'policy', 'report', 'survey', 'presentation', 'dx', 'digital', 'contact',
            'office', 'taguig', 'dost', 'pcmd', 'pdpd', 'stred', 'itd', 'gates',
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

        if (str_contains($message, 'news') || str_contains($message, 'story')) {
            $latest = News::query()->latest('date')->take(3)->pluck('title')->implode('; ');

            return $latest
                ? 'Latest PES news includes '.$latest.'.'
                : 'No PES news stories are published yet.';
        }

        if (str_contains($message, 'dx') || str_contains($message, 'digital')) {
            $domains = DxItem::query()->where('category', 'domain')->pluck('title')->implode(', ');

            return $domains
                ? 'DOST DX currently highlights these core domains: '.$domains.'.'
                : 'DOST DX content is available through the admin dashboard.';
        }

        if (str_contains($message, 'gates')) {
            $latest = GatesProject::query()->orderBy('sort_order')->latest('date')->take(3)->pluck('title')->implode('; ');

            return $latest !== ''
                ? 'DOST GATES currently highlights these projects: '.$latest.'.'
                : 'DOST GATES project records will appear once they are added by the admin team.';
        }

        return 'I can help with PES mandates, divisions, issuances, materials, DOST GATES, and DOST DX updates.';
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
