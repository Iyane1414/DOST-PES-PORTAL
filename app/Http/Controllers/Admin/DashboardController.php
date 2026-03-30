<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\Division;
use App\Models\DxItem;
use App\Models\Issuance;
use App\Models\IssuanceCategory;
use App\Models\Material;
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
        $allowedTabs = ['issuances', 'materials', 'divisions', 'dx', 'categories'];
        $requestedTab = $tab ?: $request->string('tab')->toString() ?: 'issuances';
        $activeTab = in_array($requestedTab, $allowedTabs, true) ? $requestedTab : 'issuances';

        return view('admin.dashboard', [
            ...$data,
            'activeSection' => 'workspace',
            'activeTab' => $activeTab,
        ]);
    }

    private function dashboardData(): array
    {
        $issuances = Issuance::query()->latest('date')->get();
        $materials = Material::query()->latest('date')->get();
        $divisions = Division::query()->orderBy('name')->get();
        $dxItems = DxItem::query()->orderBy('category')->orderBy('title')->get();
        $categories = IssuanceCategory::query()->orderBy('name')->get();
        $messages = ContactMessage::query()->latest()->take(10)->get();
        $dxPrograms = $dxItems->where('category', 'program')->values();
        $dxDomains = $dxItems->where('category', 'domain')->values();

        return [
            'issuances' => $issuances,
            'materials' => $materials,
            'divisions' => $divisions,
            'dxItems' => $dxItems,
            'dxPrograms' => $dxPrograms,
            'dxDomains' => $dxDomains,
            'categories' => $categories,
            'messages' => $messages,
            'stats' => [
                'issuances' => $issuances->count(),
                'materials' => $materials->count(),
                'dx_programs' => $dxPrograms->count(),
                'messages' => ContactMessage::query()->count(),
            ],
        ];
    }
}
