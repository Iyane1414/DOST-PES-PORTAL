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
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ResourceController extends Controller
{
    public function storeIssuance(Request $request): RedirectResponse
    {
        $data = $this->validateIssuance($request);
        $path = $request->file('document')->store('issuances', 'public');

        Issuance::query()->create($this->issuancePayload($data, Storage::disk('public')->url($path)));

        return $this->redirectWithTab('issuances', 'Issuance published.');
    }

    public function updateIssuance(Request $request, Issuance $issuance): RedirectResponse
    {
        $data = $this->validateIssuance($request, false);
        $url = $issuance->url;

        if ($request->hasFile('document')) {
            $this->deletePublicFile($issuance->url);
            $url = Storage::disk('public')->url($request->file('document')->store('issuances', 'public'));
        }

        $issuance->update($this->issuancePayload($data, $url));

        return $this->redirectWithTab('issuances', 'Issuance updated.');
    }

    public function destroyIssuance(Issuance $issuance): RedirectResponse
    {
        $this->deletePublicFile($issuance->url);

        $issuance->delete();

        return $this->redirectWithTab('issuances', 'Issuance deleted.');
    }

    public function storeMaterial(Request $request): RedirectResponse
    {
        $data = $this->validateMaterial($request);
        $path = $request->file('document')->store('materials', 'public');

        Material::query()->create($this->materialPayload($data, Storage::disk('public')->url($path)));

        return $this->redirectWithTab('materials', 'Material saved.');
    }

    public function updateMaterial(Request $request, Material $material): RedirectResponse
    {
        $data = $this->validateMaterial($request, false);
        $url = $material->url;

        if ($request->hasFile('document')) {
            $this->deletePublicFile($material->url);
            $url = Storage::disk('public')->url($request->file('document')->store('materials', 'public'));
        }

        $material->update($this->materialPayload($data, $url));

        return $this->redirectWithTab('materials', 'Material updated.');
    }

    public function destroyMaterial(Material $material): RedirectResponse
    {
        $this->deletePublicFile($material->url);

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
        $data = $this->validateDxItem($request);

        DxItem::query()->create($this->dxItemPayload($request, $data));

        return $this->redirectWithTab('dx', 'DX content saved.');
    }

    public function updateDxItem(Request $request, DxItem $dxItem): RedirectResponse
    {
        $data = $this->validateDxItem($request, $dxItem);
        $dxItem->update($this->dxItemPayload($request, $data, $dxItem));

        return $this->redirectWithTab('dx', 'DX content updated.');
    }

    public function destroyDxItem(DxItem $dxItem): RedirectResponse
    {
        $this->deleteDxItemTree($dxItem);

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

    private function validateIssuance(Request $request, bool $documentRequired = true): array
    {
        return $request->validate(
            [
                'title' => ['required', 'string', 'max:255'],
                'category' => ['required', 'string', 'max:255'],
                'date' => ['required', 'date'],
                'division' => ['required', 'string', 'max:255'],
                'document' => array_filter([
                    $documentRequired ? 'required' : 'nullable',
                    'file',
                    'extensions:pdf,doc,docx,xls,xlsx,ppt,pptx',
                    'max:51200',
                ]),
            ],
            $this->uploadValidationMessages('document', 50)
        );
    }

    private function issuancePayload(array $data, string $url): array
    {
        return [
            'title' => $data['title'],
            'category' => $data['category'],
            'date' => $data['date'],
            'division' => $data['division'],
            'url' => $url,
        ];
    }

    private function validateMaterial(Request $request, bool $documentRequired = true): array
    {
        return $request->validate(
            [
                'title' => ['required', 'string', 'max:255'],
                'type' => ['required', 'string', 'max:255'],
                'date' => ['required', 'date'],
                'division' => ['required', 'string', 'max:255'],
                'document' => array_filter([
                    $documentRequired ? 'required' : 'nullable',
                    'file',
                    'extensions:pdf,doc,docx,xls,xlsx,ppt,pptx,mp4,mov,jpg,jpeg,png',
                    'max:102400',
                ]),
            ],
            $this->uploadValidationMessages('document', 100)
        );
    }

    private function materialPayload(array $data, string $url): array
    {
        return [
            'title' => $data['title'],
            'type' => $this->normalizeMaterialType($data['type']),
            'date' => $data['date'],
            'division' => $data['division'],
            'url' => $url,
        ];
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

    private function validateDxItem(Request $request, ?DxItem $dxItem = null): array
    {
        return $request->validate([
            'category' => ['required', Rule::in(['domain', 'program', 'project'])],
            'domain_key' => ['required', Rule::in(['people', 'process', 'technology'])],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:2000'],
            'parent_id' => ['nullable', 'integer', 'exists:dx_items,id'],
            'code' => ['nullable', 'string', 'max:50'],
            'icon' => ['nullable', 'string', 'max:100'],
            'image' => ['nullable', 'image', 'max:5120'],
            'document' => ['nullable', 'file', 'extensions:pdf,doc,docx,xls,xlsx,ppt,pptx,mp4,mov,jpg,jpeg,png', 'max:102400'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
    }

    private function dxItemPayload(Request $request, array $data, ?DxItem $dxItem = null): array
    {
        $parentId = $this->resolveDxParentId($data['category'], $data['parent_id'] ?? null);
        $imagePath = $dxItem?->image_path;
        $fileUrl = $dxItem?->file_url;

        if ($request->hasFile('image')) {
            $this->deletePublicFile($dxItem?->image_path, ['images/people.png', 'images/process.png', 'images/technology.png']);
            $imagePath = Storage::disk('public')->url($request->file('image')->store('dx/images', 'public'));
        }

        if ($request->hasFile('document')) {
            $this->deletePublicFile($dxItem?->file_url);
            $fileUrl = Storage::disk('public')->url($request->file('document')->store('dx/files', 'public'));
        }

        if ($data['category'] !== 'domain') {
            $imagePath = null;
        }

        if ($data['category'] !== 'project') {
            $fileUrl = null;
        }

        return [
            'category' => $data['category'],
            'slug' => $dxItem?->slug ?: $this->makeUniqueDxSlug($data['title']),
            'parent_id' => $parentId,
            'domain_key' => $data['domain_key'],
            'code' => $data['category'] === 'project' ? ($data['code'] ?: null) : null,
            'icon' => $data['category'] === 'domain' ? ($data['icon'] ?: ($dxItem?->icon ?: $this->defaultDxDomainIcon($data['domain_key']))) : null,
            'image_path' => $data['category'] === 'domain' ? ($imagePath ?: $this->defaultDxDomainImage($data['domain_key'])) : null,
            'file_url' => $data['category'] === 'project' ? $fileUrl : null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => true,
            'title' => $data['title'],
            'description' => $data['description'],
        ];
    }

    private function deleteDxItemTree(DxItem $dxItem): void
    {
        $dxItem->load('children');

        foreach ($dxItem->children as $child) {
            $this->deleteDxItemTree($child);
        }

        $this->deletePublicFile($dxItem->image_path, ['images/people.png', 'images/process.png', 'images/technology.png']);
        $this->deletePublicFile($dxItem->file_url);

        $dxItem->delete();
    }

    private function deletePublicFile(?string $url, array $protectedPaths = []): void
    {
        if (! $url || ! str_starts_with($url, '/storage/')) {
            return;
        }

        $path = str_replace('/storage/', '', $url);

        if (in_array($path, $protectedPaths, true)) {
            return;
        }

        Storage::disk('public')->delete($path);
    }

    private function uploadValidationMessages(string $field, int $maxMb): array
    {
        return [
            "{$field}.required" => 'Please choose a file before submitting.',
            "{$field}.uploaded" => 'The file could not be uploaded. Restart your Laravel server after changing php.ini, then try again.',
            "{$field}.extensions" => 'The selected file type is not allowed for this upload.',
            "{$field}.max" => "The selected file is too large. Maximum allowed size is {$maxMb} MB.",
        ];
    }

    private function resolveDxParentId(string $category, ?int $parentId): ?int
    {
        if ($category === 'domain') {
            return null;
        }

        return $parentId;
    }

    private function makeUniqueDxSlug(string $title): string
    {
        $base = Str::slug($title);
        $slug = $base !== '' ? $base : 'dx-item';
        $counter = 2;

        while (DxItem::query()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$counter;
            $counter += 1;
        }

        return $slug;
    }

    private function defaultDxDomainIcon(string $domainKey): string
    {
        return match ($domainKey) {
            'people' => 'bi-person',
            'process' => 'bi-activity',
            default => 'bi-pc-display',
        };
    }

    private function defaultDxDomainImage(string $domainKey): string
    {
        return match ($domainKey) {
            'people' => 'images/people.png',
            'process' => 'images/process.png',
            default => 'images/technology.png',
        };
    }
}
