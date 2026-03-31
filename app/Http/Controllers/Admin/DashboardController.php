<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiSetting;
use App\Models\ContactMessage;
use App\Models\Division;
use App\Models\DxItem;
use App\Models\Issuance;
use App\Models\IssuanceCategory;
use App\Models\Material;
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
        $allowedTabs = ['issuances', 'materials', 'divisions', 'dx', 'categories', 'messages', 'ai'];
        $requestedTab = $tab ?: $request->string('tab')->toString() ?: 'issuances';
        $activeTab = in_array($requestedTab, $allowedTabs, true) ? $requestedTab : 'issuances';
        $issuanceSearch = trim($request->string('issuance_search')->toString());
        $messageSearch = trim($request->string('message_search')->toString());
        $messageSort = $request->string('message_sort')->toString() ?: 'newest';
        $issuances = $data['issuances'];
        $messages = $data['messages'];
        $selectedIssuance = $activeTab === 'issuances' ? Issuance::query()->find($request->integer('edit_issuance')) : null;
        $selectedMaterial = $activeTab === 'materials' ? Material::query()->find($request->integer('edit_material')) : null;
        $selectedDxItem = $activeTab === 'dx' ? DxItem::query()->with('parent')->find($request->integer('edit_dx')) : null;

        if ($issuanceSearch !== '') {
            $issuances = $issuances->filter(function (Issuance $issuance) use ($issuanceSearch) {
                return $this->matchesWorkspaceIssuanceSearch($issuance, $issuanceSearch);
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
            'issuanceSearch' => $issuanceSearch,
            'workspaceMessages' => $messages,
            'messageSearch' => $messageSearch,
            'messageSort' => $messageSort,
            'selectedMessageId' => $request->integer('message'),
            'selectedIssuance' => $selectedIssuance,
            'selectedMaterial' => $selectedMaterial,
            'selectedDxItem' => $selectedDxItem,
        ]);
    }

    private function dashboardData(): array
    {
        $issuances = Issuance::query()->latest('date')->get();
        $materials = Material::query()->latest('date')->get();
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
        $dxDomains = $dxItems->where('category', 'domain')->values();
        $aiSetting = AiSetting::query()->first();
        $projectAnalytics = $this->projectAnalytics($dxPrograms);
        $viewStats = $this->websiteViewStats();

        return [
            'issuances' => $issuances,
            'materials' => $materials,
            'divisions' => $divisions,
            'messages' => $messages,
            'dxItems' => $dxItems,
            'dxPrograms' => $dxPrograms,
            'dxDomains' => $dxDomains,
            'categories' => $categories,
            'aiSetting' => $aiSetting,
            'projectAnalytics' => $projectAnalytics,
            'viewStats' => $viewStats,
            'stats' => [
                'issuances' => $issuances->count(),
                'materials' => $materials->count(),
                'dx_programs' => $dxPrograms->count(),
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
        $category = Str::of($issuance->category ?? '')->lower()->trim()->value();
        $division = Str::of($issuance->division ?? '')->lower()->trim()->value();
        $titleWords = preg_split('/\s+/', $title, -1, PREG_SPLIT_NO_EMPTY) ?: [];

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

        return str_contains($title, $normalizedSearch)
            || str_contains($category, $normalizedSearch)
            || str_contains($division, $normalizedSearch);
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
