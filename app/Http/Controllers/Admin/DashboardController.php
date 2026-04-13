<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiSetting;
use App\Models\ContactMessage;
use App\Models\Division;
use App\Models\DxItem;
use App\Models\GatesProject;
use App\Models\Issuance;
use App\Models\IssuanceCategory;
use App\Models\Material;
use App\Models\News;
use App\Models\WebsiteVisit;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function overview(): View
    {
        $data = $this->dashboardData();

        return view('admin.overview', [
            ...$data,
            'activeSection' => 'overview',
            'recentIssuances' => $data['issuances']->take(5),
            'recentMaterials' => $data['materials']->take(5),
            'recentNews' => $data['news']->take(5),
            'recentMessages' => $data['messages']->take(4),
            'latestDxPrograms' => $data['dxPrograms']->take(6),
            'quickLinks' => [
                [
                    'title' => 'Manage Issuances',
                    'copy' => 'Publish official memoranda, letters, and administrative orders.',
                    'route' => route('admin.workspace', ['tab' => 'issuances']),
                    'icon' => 'bi-briefcase',
                ],
                [
                    'title' => 'Manage Materials',
                    'copy' => 'Maintain reports, presentations, surveys, and linked resources.',
                    'route' => route('admin.workspace', ['tab' => 'materials']),
                    'icon' => 'bi-collection-play',
                ],
                [
                    'title' => 'Manage News',
                    'copy' => 'Publish PES in Action stories with thumbnails and optional article links.',
                    'route' => route('admin.workspace', ['tab' => 'news']),
                    'icon' => 'bi-newspaper',
                ],
                [
                    'title' => 'Update DOST DX',
                    'copy' => 'Add domains and sub-programs tied to digital transformation efforts.',
                    'route' => route('admin.workspace', ['tab' => 'dx']),
                    'icon' => 'bi-cpu',
                ],
                [
                    'title' => 'Open Public Portal',
                    'copy' => 'Review the live PES homepage experience in a new tab.',
                    'route' => route('portal.home'),
                    'icon' => 'bi-box-arrow-up-right',
                ],
            ],
        ]);
    }

    public function workspace(Request $request, ?string $tab = null): View
    {
        $data = $this->dashboardData();
        $allowedTabs = ['issuances', 'materials', 'news', 'divisions', 'dx', 'categories', 'messages', 'ai', 'gates-projects', 'gates-issuances', 'gates-news', 'gates'];
        $requestedTab = $tab ?: $request->string('tab')->toString() ?: 'issuances';
        if ($requestedTab === 'gates') {
            $requestedTab = 'gates-projects';
        }
        $activeTab = in_array($requestedTab, $allowedTabs, true) ? $requestedTab : 'issuances';
        $isGatesWorkspaceTab = $this->isGatesWorkspaceTab($activeTab);
        $gatesFilterType = $this->gatesWorkspaceFilterType($activeTab);
        $issuanceSearch = trim($request->string('issuance_search')->toString());
        $materialSearch = trim($request->string('material_search')->toString());
        $newsSearch = trim($request->string('news_search')->toString());
        $dxSearch = trim($request->string('dx_search')->toString());
        $messageSearch = trim($request->string('message_search')->toString());
        $messageSort = $request->string('message_sort')->toString() ?: 'newest';
        $issuances = $data['issuances'];
        $materials = $data['materials'];
        $gatesProjects = $data['gatesProjects'];
        $news = $data['news'];
        $dxItems = $data['dxItems'];
        $messages = $data['messages'];
        $selectedIssuance = $activeTab === 'issuances' ? Issuance::query()->find($request->integer('edit_issuance')) : null;
        $selectedMaterial = $activeTab === 'materials' ? Material::query()->find($request->integer('edit_material')) : null;
        $selectedGatesProject = $isGatesWorkspaceTab ? GatesProject::query()->find($request->integer('edit_gate')) : null;
        $selectedNews = $activeTab === 'news' ? News::query()->find($request->integer('edit_news')) : null;
        $selectedDxItem = $activeTab === 'dx'
            ? DxItem::query()->with('parent')->where('category', 'project')->find($request->integer('edit_dx'))
            : null;

        if ($issuanceSearch !== '') {
            $issuances = $issuances->filter(function (Issuance $issuance) use ($issuanceSearch) {
                return $this->matchesWorkspaceIssuanceSearch($issuance, $issuanceSearch);
            })->values();
        }

        if ($materialSearch !== '') {
            $materials = $materials->filter(function (Material $material) use ($materialSearch) {
                return $this->matchesWorkspaceMaterialSearch($material, $materialSearch);
            })->values();
        }

        if ($newsSearch !== '') {
            $news = $news->filter(function (News $newsItem) use ($newsSearch) {
                return $this->matchesWorkspaceNewsSearch($newsItem, $newsSearch);
            })->values();
        }

        if ($isGatesWorkspaceTab) {
            $gatesProjects = $gatesProjects
                ->filter(fn (GatesProject $project) => $this->matchesWorkspaceGatesType($project, $gatesFilterType))
                ->values();
        }

        if ($isGatesWorkspaceTab && $materialSearch !== '') {
            $gatesProjects = $gatesProjects->filter(function (GatesProject $project) use ($materialSearch) {
                return $this->matchesWorkspaceGatesSearch($project, $materialSearch);
            })->values();
        }

        if ($dxSearch !== '') {
            $dxItems = $dxItems->filter(function (DxItem $dxItem) use ($dxSearch) {
                return $dxItem->category === 'project' && $this->matchesWorkspaceDxSearch($dxItem, $dxSearch);
            })->values();
        }

        if ($messageSearch !== '') {
            $needle = Str::lower($messageSearch);
            $messages = $messages->filter(function (ContactMessage $message) use ($needle) {
                $searchable = Str::lower(implode(' ', [
                    $message->subject,
                    $message->name,
                    $message->email,
                    $message->message,
                ]));

                return str_contains($searchable, $needle);
            })->values();
        }

        if ($messageSort === 'oldest') {
            $messages = $messages->sortBy('created_at')->values();
        } else {
            $messageSort = 'newest';
            $messages = $messages->sortByDesc('created_at')->values();
        }

        return view('admin.dashboard', [
            ...$data,
            'activeSection' => 'workspace',
            'activeTab' => $activeTab,
            'workspaceIssuances' => $issuances,
            'workspaceMaterials' => $materials,
            'workspaceGatesProjects' => $gatesProjects,
            'workspaceNews' => $news,
            'workspaceDxItems' => $dxItems,
            'issuanceSearch' => $issuanceSearch,
            'materialSearch' => $materialSearch,
            'newsSearch' => $newsSearch,
            'dxSearch' => $dxSearch,
            'workspaceMessages' => $messages,
            'messageSearch' => $messageSearch,
            'messageSort' => $messageSort,
            'selectedMessageId' => $request->integer('message'),
            'selectedIssuance' => $selectedIssuance,
            'selectedMaterial' => $selectedMaterial,
            'selectedGatesProject' => $selectedGatesProject,
            'selectedNews' => $selectedNews,
            'selectedDxItem' => $selectedDxItem,
        ]);
    }

    public function showMessage(ContactMessage $contactMessage): View
    {
        $data = $this->dashboardData();

        if ($contactMessage->opened_at === null) {
            $contactMessage->forceFill(['opened_at' => now()])->save();
            $contactMessage->refresh();
        }

        return view('admin.messages.show', [
            ...$data,
            'activeSection' => 'workspace',
            'activeTab' => 'messages',
            'messageItem' => $contactMessage,
            'messageList' => $data['messages']->take(8),
        ]);
    }

    private function dashboardData(): array
    {
        $issuances = Issuance::query()->latest('date')->get();
        $materials = Material::query()->latest('date')->get();
        $gatesProjects = GatesProject::query()->where('is_active', true)->orderBy('sort_order')->latest('date')->get();
        $news = News::query()->latest('date')->get();
        $divisions = Division::query()->orderBy('name')->get();
        $dxItems = DxItem::query()
            ->with('parent')
            ->whereNotNull('slug')
            ->orderBy('category')
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();
        $categories = IssuanceCategory::query()->orderBy('name')->get();
        if ($categories->isEmpty()) {
            $categories = collect($this->defaultIssuanceCategories())->map(fn (string $name) => (object) ['name' => $name]);
        }
        $messages = ContactMessage::query()->latest()->get();
        $dxPrograms = $dxItems->where('category', 'program')->values();
        $dxProjects = $dxItems->where('category', 'project')->values();
        $dxDomains = $dxItems->where('category', 'domain')->values();
        $aiSetting = AiSetting::query()->first();
        $projectAnalytics = $this->projectAnalytics($dxPrograms);
        $viewStats = $this->websiteViewStats();

        return [
            'issuances' => $issuances,
            'materials' => $materials,
            'gatesProjects' => $gatesProjects,
            'news' => $news,
            'divisions' => $divisions,
            'messages' => $messages,
            'dxItems' => $dxItems,
            'dxPrograms' => $dxPrograms,
            'dxProjects' => $dxProjects,
            'dxDomains' => $dxDomains,
            'categories' => $categories,
            'aiSetting' => $aiSetting,
            'projectAnalytics' => $projectAnalytics,
            'viewStats' => $viewStats,
            'stats' => [
                'issuances' => $issuances->count(),
                'materials' => $materials->count(),
                'gates_projects' => $gatesProjects->count(),
                'news' => $news->count(),
                'dx_programs' => $dxProjects->count(),
                'website_views' => $viewStats['total'],
            ],
        ];
    }

    private function matchesWorkspaceIssuanceSearch(Issuance $issuance, string $search): bool
    {
        $normalizedSearch = Str::of($search)->lower()->trim()->value();

        if ($normalizedSearch === '') {
            return true;
        }

        $title = Str::of($issuance->title ?? '')->lower()->trim()->value();
        $ermNumber = Str::of($issuance->erm_number ?? '')->lower()->trim()->value();
        $category = Str::of($issuance->category ?? '')->lower()->trim()->value();
        $division = Str::of($issuance->division ?? '')->lower()->trim()->value();
        $titleWords = preg_split('/\s+/', $title, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        if ($ermNumber !== '' && str_starts_with($ermNumber, $normalizedSearch)) {
            return true;
        }

        if ($title !== '' && str_starts_with($title, $normalizedSearch)) {
            return true;
        }

        foreach ($titleWords as $word) {
            if (str_starts_with($word, $normalizedSearch)) {
                return true;
            }
        }

        if ($category !== '' && str_starts_with($category, $normalizedSearch)) {
            return true;
        }

        if ($division !== '' && str_starts_with($division, $normalizedSearch)) {
            return true;
        }

        return str_contains($ermNumber, $normalizedSearch)
            || str_contains($title, $normalizedSearch)
            || str_contains($category, $normalizedSearch)
            || str_contains($division, $normalizedSearch);
    }

    private function matchesWorkspaceDxSearch(DxItem $dxItem, string $search): bool
    {
        $normalizedSearch = Str::of($search)->lower()->trim()->value();

        if ($normalizedSearch === '') {
            return true;
        }

        $projectTitle = Str::of($dxItem->title ?? '')->lower()->trim()->value();
        $programTitle = Str::of($dxItem->parent?->title ?? '')->lower()->trim()->value();
        $titleWords = preg_split('/\s+/', $projectTitle, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        if ($projectTitle !== '' && str_starts_with($projectTitle, $normalizedSearch)) {
            return true;
        }

        foreach ($titleWords as $word) {
            if (str_starts_with($word, $normalizedSearch)) {
                return true;
            }
        }

        if ($programTitle !== '' && str_starts_with($programTitle, $normalizedSearch)) {
            return true;
        }

        return str_contains($projectTitle, $normalizedSearch)
            || str_contains($programTitle, $normalizedSearch);
    }

    private function matchesWorkspaceMaterialSearch(Material $material, string $search): bool
    {
        $normalizedSearch = Str::of($search)->lower()->trim()->value();

        if ($normalizedSearch === '') {
            return true;
        }

        $title = Str::of($material->title ?? '')->lower()->trim()->value();
        $type = Str::of($material->type ?? '')->lower()->trim()->value();
        $division = Str::of($material->division ?? '')->lower()->trim()->value();
        $titleWords = preg_split('/\s+/', $title, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        if ($title !== '' && str_starts_with($title, $normalizedSearch)) {
            return true;
        }

        foreach ($titleWords as $word) {
            if (str_starts_with($word, $normalizedSearch)) {
                return true;
            }
        }

        if ($type !== '' && str_starts_with($type, $normalizedSearch)) {
            return true;
        }

        if ($division !== '' && str_starts_with($division, $normalizedSearch)) {
            return true;
        }

        return str_contains($title, $normalizedSearch)
            || str_contains($type, $normalizedSearch)
            || str_contains($division, $normalizedSearch);
    }

    private function matchesWorkspaceNewsSearch(News $news, string $search): bool
    {
        $normalizedSearch = Str::of($search)->lower()->trim()->value();

        if ($normalizedSearch === '') {
            return true;
        }

        $title = Str::of($news->title ?? '')->lower()->trim()->value();
        $eyebrow = Str::of($news->eyebrow ?? '')->lower()->trim()->value();
        $summary = Str::of($news->summary ?? '')->lower()->trim()->value();
        $titleWords = preg_split('/\s+/', $title, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        if ($title !== '' && str_starts_with($title, $normalizedSearch)) {
            return true;
        }

        foreach ($titleWords as $word) {
            if (str_starts_with($word, $normalizedSearch)) {
                return true;
            }
        }

        if ($eyebrow !== '' && str_starts_with($eyebrow, $normalizedSearch)) {
            return true;
        }

        return str_contains($title, $normalizedSearch)
            || str_contains($eyebrow, $normalizedSearch)
            || str_contains($summary, $normalizedSearch);
    }

    private function matchesWorkspaceGatesSearch(GatesProject $project, string $search): bool
    {
        $normalizedSearch = Str::of($search)->lower()->trim()->value();

        if ($normalizedSearch === '') {
            return true;
        }

        $title = Str::of($project->title ?? '')->lower()->trim()->value();
        $code = Str::of($project->code ?? '')->lower()->trim()->value();
        $description = Str::of($project->description ?? '')->lower()->trim()->value();
        $titleWords = preg_split('/\s+/', $title, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        if ($code !== '' && str_starts_with($code, $normalizedSearch)) {
            return true;
        }

        if ($title !== '' && str_starts_with($title, $normalizedSearch)) {
            return true;
        }

        foreach ($titleWords as $word) {
            if (str_starts_with($word, $normalizedSearch)) {
                return true;
            }
        }

        return str_contains($title, $normalizedSearch)
            || str_contains($code, $normalizedSearch)
            || str_contains($description, $normalizedSearch);
    }

    private function isGatesWorkspaceTab(string $tab): bool
    {
        return in_array($tab, ['gates-projects', 'gates-issuances', 'gates-news'], true);
    }

    private function gatesWorkspaceFilterType(string $tab): ?string
    {
        return match ($tab) {
            'gates-projects' => 'project_library',
            'gates-issuances' => 'issuance',
            'gates-news' => 'gates_p1_news',
            default => null,
        };
    }

    private function matchesWorkspaceGatesType(GatesProject $project, ?string $expectedType): bool
    {
        if ($expectedType === null) {
            return true;
        }

        $type = Str::lower(trim((string) $project->type));

        return match ($expectedType) {
            'project_library' => (str_contains($type, 'project') && ! str_contains($type, 'issuance') && ! str_contains($type, 'news'))
                || str_contains($type, 'video'),
            'project' => str_contains($type, 'project')
                && ! str_contains($type, 'video')
                && ! str_contains($type, 'issuance')
                && ! str_contains($type, 'news'),
            'issuance' => str_contains($type, 'issuance'),
            'gates_p1_news' => str_contains($type, 'p1 news') || str_contains($type, 'gates p1 news') || str_contains($type, 'news'),
            default => false,
        };
    }

    private function defaultIssuanceCategories(): array
    {
        return ['Guidelines', 'Letter', 'Memorandum', 'Notice', 'Order'];
    }

    private function projectAnalytics($dxPrograms): array
    {
        $doneKeywords = ['done', 'completed', 'complete', 'launched', 'released', 'implemented', 'deployed', 'operational'];
        $newProjects = $dxPrograms->filter(fn (DxItem $item) => optional($item->created_at)?->greaterThanOrEqualTo(now()->subDays(30)))->count();
        $doneProjects = $dxPrograms->filter(function (DxItem $item) use ($doneKeywords) {
            $text = Str::lower($item->title.' '.$item->description);

            return collect($doneKeywords)->contains(fn ($keyword) => str_contains($text, $keyword));
        })->count();

        $pendingProjects = max($dxPrograms->count() - $doneProjects - $newProjects, 0);
        $total = max($pendingProjects + $doneProjects + $newProjects, 1);

        return [
            'pending' => [
                'count' => $pendingProjects,
                'percent' => round(($pendingProjects / $total) * 100, 1),
                'color' => '#ff8a3d',
            ],
            'done' => [
                'count' => $doneProjects,
                'percent' => round(($doneProjects / $total) * 100, 1),
                'color' => '#a94dff',
            ],
            'new' => [
                'count' => $newProjects,
                'percent' => round(($newProjects / $total) * 100, 1),
                'color' => '#1fb6ff',
            ],
        ];
    }

    private function websiteViewStats(): array
    {
        $today = today();
        $weekStart = now()->subDays(6)->startOfDay();
        $monthStart = now()->startOfMonth();

        return [
            'total' => WebsiteVisit::query()->count(),
            'today' => WebsiteVisit::query()->whereDate('visited_at', $today)->count(),
            'week' => WebsiteVisit::query()->where('visited_at', '>=', $weekStart)->count(),
            'month' => WebsiteVisit::query()->where('visited_at', '>=', $monthStart)->count(),
        ];
    }
}
