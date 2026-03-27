<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\Division;
use App\Models\DxItem;
use App\Models\Issuance;
use App\Models\IssuanceCategory;
use App\Models\Material;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $issuances = Issuance::query()->latest('date')->get();
        $materials = Material::query()->latest('date')->get();
        $divisions = Division::query()->orderBy('name')->get();
        $dxItems = DxItem::query()->orderBy('category')->orderBy('title')->get();
        $categories = IssuanceCategory::query()->orderBy('name')->get();
        $subscriptions = Subscription::query()->latest()->take(10)->get();
        $messages = ContactMessage::query()->latest()->take(10)->get();

        return view('admin.dashboard', [
            'activeTab' => $request->string('tab')->toString() ?: 'issuances',
            'issuances' => $issuances,
            'materials' => $materials,
            'divisions' => $divisions,
            'dxItems' => $dxItems,
            'categories' => $categories,
            'subscriptions' => $subscriptions,
            'messages' => $messages,
            'stats' => [
                'issuances' => $issuances->count(),
                'materials' => $materials->count(),
                'subscribers' => Subscription::query()->count(),
                'messages' => ContactMessage::query()->count(),
            ],
        ]);
    }
}
