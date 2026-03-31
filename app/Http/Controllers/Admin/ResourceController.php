<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiSetting;
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
            'document' => ['required', 'file', 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,mp4,mov,jpg,jpeg,png', 'max:20480'],
        ]);

        $path = $request->file('document')->store('materials', 'public');
        $normalizedType = $this->normalizeMaterialType($data['type']);

        Material::query()->create([
            'title' => $data['title'],
            'type' => $normalizedType,
            'date' => $data['date'],
            'division' => $data['division'],
            'url' => Storage::disk('public')->url($path),
        ]);

        return $this->redirectWithTab('materials', 'Material saved.');
    }

    public function destroyMaterial(Material $material): RedirectResponse
    {
        if ($material->url && str_starts_with($material->url, '/storage/')) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $material->url));
        }

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

    public function storeAiSettings(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'system_prompt' => ['required', 'string', 'max:5000'],
            'scope_prompt' => ['required', 'string', 'max:3000'],
            'refusal_message' => ['required', 'string', 'max:500'],
        ]);

        AiSetting::query()->updateOrCreate(
            ['id' => 1],
            $data
        );

        return $this->redirectWithTab('ai', 'AI settings updated.');
    }

    public function destroyCategory(IssuanceCategory $issuanceCategory): RedirectResponse
    {
        $issuanceCategory->delete();

        return $this->redirectWithTab('categories', 'Category deleted.');
    }

    private function redirectWithTab(string $tab, string $status): RedirectResponse
    {
        return redirect()->route('admin.workspace', ['tab' => $tab])->with('status', $status);
    }

    private function normalizeMaterialType(string $type): string
    {
        $normalized = strtolower(trim($type));

        if (str_contains($normalized, 'policy') || str_contains($normalized, 'guideline')) {
            return 'Policy';
        }

        if (str_contains($normalized, 'annual') || str_contains($normalized, 'report')) {
            return 'Annual Report';
        }

        if (str_contains($normalized, 'survey') || str_contains($normalized, 'r&d') || str_contains($normalized, 'research')) {
            return 'R&D Survey';
        }

        return 'Presentation';
    }
}
