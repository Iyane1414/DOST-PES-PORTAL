<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Division;
use App\Models\DxItem;
use App\Models\Issuance;
use App\Models\IssuanceCategory;
use App\Models\Material;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ResourceController extends Controller
{
    public function storeIssuance(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'division' => ['required', 'string', 'max:255'],
            'document' => ['required', 'file', 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx', 'max:20480'],
        ]);

        $path = $request->file('document')->store('issuances', 'public');

        Issuance::query()->create([
            'title' => $data['title'],
            'category' => $data['category'],
            'date' => $data['date'],
            'division' => $data['division'],
            'url' => Storage::disk('public')->url($path),
        ]);

        return $this->redirectWithTab('issuances', 'Issuance published.');
    }

    public function destroyIssuance(Issuance $issuance): RedirectResponse
    {
        if ($issuance->url && str_starts_with($issuance->url, '/storage/')) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $issuance->url));
        }

        $issuance->delete();

        return $this->redirectWithTab('issuances', 'Issuance deleted.');
    }

    public function storeMaterial(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'division' => ['required', 'string', 'max:255'],
            'url' => ['nullable', 'url', 'max:2048'],
        ]);

        Material::query()->create($data);

        return $this->redirectWithTab('materials', 'Material saved.');
    }

    public function destroyMaterial(Material $material): RedirectResponse
    {
        $material->delete();

        return $this->redirectWithTab('materials', 'Material deleted.');
    }

    public function storeDivision(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'head' => ['nullable', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:2000'],
        ]);

        Division::query()->create($data);

        return $this->redirectWithTab('divisions', 'Division saved.');
    }

    public function destroyDivision(Division $division): RedirectResponse
    {
        $division->delete();

        return $this->redirectWithTab('divisions', 'Division deleted.');
    }

    public function storeDxItem(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'category' => ['required', Rule::in(['domain', 'program'])],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:2000'],
        ]);

        DxItem::query()->create($data);

        return $this->redirectWithTab('dx', 'DX content saved.');
    }

    public function destroyDxItem(DxItem $dxItem): RedirectResponse
    {
        $dxItem->delete();

        return $this->redirectWithTab('dx', 'DX content deleted.');
    }

    public function storeCategory(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:issuance_categories,name'],
        ]);

        IssuanceCategory::query()->create($data);

        return $this->redirectWithTab('categories', 'Category added.');
    }

    public function destroyCategory(IssuanceCategory $issuanceCategory): RedirectResponse
    {
        $issuanceCategory->delete();

        return $this->redirectWithTab('categories', 'Category deleted.');
    }

    private function redirectWithTab(string $tab, string $status): RedirectResponse
    {
        return redirect()->route('admin.dashboard', ['tab' => $tab])->with('status', $status);
    }
}
