<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AdminMessageReplyMail;
use App\Models\AiSetting;
use App\Models\ContactMessage;
use App\Models\Division;
use App\Models\DxItem;
use App\Models\DxRoadmapItem;
use App\Models\GatesProject;
use App\Models\Issuance;
use App\Models\IssuanceCategory;
use App\Models\Material;
use App\Models\News;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Throwable;

class ResourceController extends Controller
{
    public function storeIssuance(Request $request): RedirectResponse
    {
        $data = $this->validateIssuance($request);
        $path = $request->file('document')->store('issuances', 'public');

        /** @var \Illuminate\Filesystem\FilesystemAdapter $publicDisk */
        $publicDisk = Storage::disk('public');

        Issuance::query()->create($this->issuancePayload($data, $publicDisk->url($path)));

        return $this->redirectWithTab('issuances', 'Issuance published.');
    }

    public function updateIssuance(Request $request, Issuance $issuance): RedirectResponse
    {
        $data = $this->validateIssuance($request, false);
        $url = $issuance->url;

        if ($request->hasFile('document')) {
            $this->deletePublicFile($issuance->url);

            /** @var \Illuminate\Filesystem\FilesystemAdapter $publicDisk */
            $publicDisk = Storage::disk('public');
            $url = $publicDisk->url($request->file('document')->store('issuances', 'public'));
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

        /** @var \Illuminate\Filesystem\FilesystemAdapter $publicDisk */
        $publicDisk = Storage::disk('public');

        Material::query()->create($this->materialPayload($data, $publicDisk->url($path)));

        return $this->redirectWithTab('materials', 'Material saved.');
    }

    public function updateMaterial(Request $request, Material $material): RedirectResponse
    {
        $data = $this->validateMaterial($request, false);
        $url = $material->url;

        if ($request->hasFile('document')) {
            $this->deletePublicFile($material->url);

            /** @var \Illuminate\Filesystem\FilesystemAdapter $publicDisk */
            $publicDisk = Storage::disk('public');
            $url = $publicDisk->url($request->file('document')->store('materials', 'public'));
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

    public function storeGatesProject(Request $request): RedirectResponse
    {
        $data = $this->validateGatesProject($request);
        $isNews = $this->isGatesNewsType($data['type'] ?? '');
        /** @var \Illuminate\Filesystem\FilesystemAdapter $publicDisk */
        $publicDisk = Storage::disk('public');
        $fileUrl = null;
        $thumbnailPath = null;

        if ($isNews) {
            if ($request->hasFile('thumbnail')) {
                $thumbnailPath = $publicDisk->url($request->file('thumbnail')->store('gates/news', 'public'));
            }
        } elseif ($request->hasFile('document')) {
            $fileUrl = $publicDisk->url($request->file('document')->store('gates', 'public'));
        }

        GatesProject::query()->create($this->gatesProjectPayload($data, $fileUrl, $thumbnailPath));

        return $this->redirectWithTab($this->resolveGatesWorkspaceTab($request), 'GATES Project 1 item saved.');
    }

    public function updateGatesProject(Request $request, GatesProject $gatesProject): RedirectResponse
    {
        $data = $this->validateGatesProject($request, false);
        $isNews = $this->isGatesNewsType($data['type'] ?? '');
        /** @var \Illuminate\Filesystem\FilesystemAdapter $publicDisk */
        $publicDisk = Storage::disk('public');
        $url = $gatesProject->url;
        $thumbnailPath = $gatesProject->thumbnail_path;

        if ($isNews) {
            if ($request->hasFile('thumbnail')) {
                $this->deletePublicFile($gatesProject->thumbnail_path);
                $thumbnailPath = $publicDisk->url($request->file('thumbnail')->store('gates/news', 'public'));
            }
            $url = $data['link_url'] ?: null;
        } elseif ($request->hasFile('document')) {
            $this->deletePublicFile($gatesProject->url);
            $url = $publicDisk->url($request->file('document')->store('gates', 'public'));
            $thumbnailPath = null;
        }

        $gatesProject->update($this->gatesProjectPayload($data, $url, $thumbnailPath));

        return $this->redirectWithTab($this->resolveGatesWorkspaceTab($request, $gatesProject), 'GATES Project 1 item updated.');
    }

    public function destroyGatesProject(Request $request, GatesProject $gatesProject): RedirectResponse
    {
        $this->deletePublicFile($gatesProject->url);
        $this->deletePublicFile($gatesProject->thumbnail_path);
        $workspaceTab = $this->resolveGatesWorkspaceTab($request, $gatesProject);
        $gatesProject->delete();

        return $this->redirectWithTab($workspaceTab, 'GATES Project 1 item deleted.');
    }

    public function storeNews(Request $request): RedirectResponse
    {
        $data = $this->validateNews($request);

        News::query()->create($this->newsPayload($request, $data));

        return $this->redirectWithTab('news', 'News item published.');
    }

    public function updateNews(Request $request, News $news): RedirectResponse
    {
        $data = $this->validateNews($request);
        $news->update($this->newsPayload($request, $data, $news));

        return $this->redirectWithTab('news', 'News item updated.');
    }

    public function destroyNews(News $news): RedirectResponse
    {
        $this->deletePublicFile($news->thumbnail_path);
        $news->delete();

        return $this->redirectWithTab('news', 'News item deleted.');
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

    public function storeDxRoadmapItem(Request $request): RedirectResponse
    {
        $data = $this->validateDxRoadmapItem($request);

        DxRoadmapItem::query()->create($this->dxRoadmapPayload($data));

        return $this->redirectWithTab('roadmap', 'DX roadmap item saved.');
    }

    public function updateDxRoadmapItem(Request $request, DxRoadmapItem $dxRoadmapItem): RedirectResponse
    {
        $data = $this->validateDxRoadmapItem($request);
        $dxRoadmapItem->update($this->dxRoadmapPayload($data));

        return $this->redirectWithTab('roadmap', 'DX roadmap item updated.');
    }

    public function destroyDxRoadmapItem(DxRoadmapItem $dxRoadmapItem): RedirectResponse
    {
        $dxRoadmapItem->delete();

        return $this->redirectWithTab('roadmap', 'DX roadmap item deleted.');
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

    public function replyToMessage(Request $request, ContactMessage $contactMessage): RedirectResponse
    {
        $data = $request->validate([
            'reply_subject' => ['required', 'string', 'max:255'],
            'reply_body' => ['required', 'string', 'max:5000'],
        ]);

        try {
            Mail::to($contactMessage->email)->send(
                new AdminMessageReplyMail(
                    $contactMessage,
                    $data['reply_subject'],
                    $data['reply_body'],
                )
            );
        } catch (Throwable $exception) {
            report($exception);

            $errorMessage = config('app.debug')
                ? $exception->getMessage()
                : 'The reply could not be sent. Please check your mail configuration and try again.';

            return back()
                ->withInput()
                ->withErrors([
                    'reply_body' => $errorMessage,
                ]);
        }

        $contactMessage->update([
            'opened_at' => $contactMessage->opened_at ?: now(),
            'replied_at' => now(),
            'admin_reply_subject' => $data['reply_subject'],
            'admin_reply_body' => $data['reply_body'],
        ]);

        return redirect()
            ->route('admin.messages.show', $contactMessage)
            ->with('status', 'Reply sent successfully to '.$contactMessage->email.'.');
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
                'erm_number' => ['nullable', 'string', 'max:255'],
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
            'erm_number' => $data['erm_number'] ?? null,
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

    private function validateGatesProject(Request $request, bool $documentRequired = true): array
    {
        $type = (string) $request->input('type', '');
        $isNews = $this->isGatesNewsType($type);

        $baseRules = [
            'title' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50'],
            'type' => ['required', 'string', 'in:project,issuance,gates_p1_news,video_presentation'],
            'date' => ['nullable', 'date'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];

        if ($isNews) {
            return $request->validate(
                $baseRules + [
                    'eyebrow' => ['required', 'string', 'max:100'],
                    'summary' => ['required', 'string', 'max:500'],
                    'content' => ['required', 'string', 'max:10000'],
                    'date' => ['required', 'date'],
                    'link_url' => ['nullable', 'url', 'max:255'],
                    'accent' => ['required', Rule::in(['cyan', 'blue', 'gold', 'mint', 'violet', 'slate'])],
                    'image_alt' => ['nullable', 'string', 'max:255'],
                    'thumbnail' => [
                        'nullable',
                        'image',
                        'mimes:jpg,jpeg,png,webp',
                        'max:5120',
                    ],
                    'description' => ['nullable', 'string', 'max:2000'],
                    'document' => ['nullable', 'file', 'extensions:pdf,doc,docx,xls,xlsx,ppt,pptx,mp4,mov,jpg,jpeg,png', 'max:512000'],
                ],
                $this->uploadValidationMessages('thumbnail', 5)
            );
        }

        return $request->validate(
            $baseRules + [
                'description' => ['required', 'string', 'max:2000'],
                'document' => array_filter([
                    $documentRequired ? 'required' : 'nullable',
                    'file',
                    'extensions:pdf,doc,docx,xls,xlsx,ppt,pptx,mp4,mov,jpg,jpeg,png',
                    'max:512000',
                ]),
            ],
            $this->uploadValidationMessages('document', 500)
        );
    }

    private function validateDxRoadmapItem(Request $request): array
    {
        return $request->validate([
            'year_label' => ['required', 'string', 'max:50'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:2000'],
            'milestones' => ['nullable', 'string', 'max:4000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }

    private function dxRoadmapPayload(array $data): array
    {
        $milestones = collect(preg_split('/\r\n|\r|\n/', (string) ($data['milestones'] ?? '')) ?: [])
            ->map(fn (string $item) => trim($item))
            ->filter()
            ->values()
            ->all();

        return [
            'year_label' => $data['year_label'],
            'title' => $data['title'],
            'description' => $data['description'],
            'milestones' => $milestones,
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'is_active' => (bool) ($data['is_active'] ?? false),
        ];
    }

    private function gatesProjectPayload(array $data, ?string $url, ?string $thumbnailPath = null): array
    {
        $isNews = $this->isGatesNewsType($data['type'] ?? '');

        return [
            'title' => $data['title'],
            'code' => $data['code'] ?: null,
            'type' => $this->normalizeGatesProjectType($data['type']),
            'news_eyebrow' => $isNews ? $data['eyebrow'] : null,
            'description' => $isNews ? ($data['summary'] ?? $data['description'] ?? '') : $data['description'],
            'news_summary' => $isNews ? $data['summary'] : null,
            'news_content' => $isNews ? $data['content'] : null,
            'date' => $data['date'] ?: null,
            'url' => $isNews ? ($data['link_url'] ?: $url) : $url,
            'news_link_url' => $isNews ? ($data['link_url'] ?: null) : null,
            'news_accent' => $isNews ? ($data['accent'] ?? 'cyan') : null,
            'news_image_alt' => $isNews ? ($data['image_alt'] ?: $data['title']) : null,
            'thumbnail_path' => $isNews ? $thumbnailPath : null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => true,
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

        if (str_contains($normalized, 'project')) {
            return 'Projects';
        }

        return 'Presentation';
    }

    private function normalizeGatesProjectType(string $type): string
    {
        $normalized = strtolower(trim($type));

        if ($normalized === 'project' || str_contains($normalized, 'project')) {
            return 'Project';
        }

        if ($normalized === 'issuance' || str_contains($normalized, 'issuance')) {
            return 'Issuance';
        }

        if (
            $normalized === 'gates_p1_news'
            || str_contains($normalized, 'p1 news')
            || str_contains($normalized, 'project 1 news')
            || str_contains($normalized, 'gates news')
        ) {
            return 'GATES Project 1 News';
        }

        if ($normalized === 'video_presentation' || $normalized === 'video presentation' || str_contains($normalized, 'video')) {
            return 'Video Presentation';
        }

        return 'Project';
    }

    private function isGatesNewsType(string $type): bool
    {
        $normalized = Str::lower(trim($type));

        return $normalized === 'gates_p1_news'
            || str_contains($normalized, 'p1 news')
            || str_contains($normalized, 'project 1 news')
            || str_contains($normalized, 'gates p1 news');
    }

    private function resolveGatesWorkspaceTab(Request $request, ?GatesProject $gatesProject = null): string
    {
        $requestedTab = $request->string('workspace_tab')->toString();
        $allowedTabs = ['gates-projects', 'gates-issuances', 'gates-news'];

        if (in_array($requestedTab, $allowedTabs, true)) {
            return $requestedTab;
        }

        if ($gatesProject) {
            return $this->workspaceTabForGatesType($gatesProject->type ?? '');
        }

        return 'gates-projects';
    }

    private function workspaceTabForGatesType(string $type): string
    {
        $normalized = Str::lower(trim($type));

        if (str_contains($normalized, 'issuance')) {
            return 'gates-issuances';
        }

        if (
            str_contains($normalized, 'p1 news')
            || str_contains($normalized, 'project 1 news')
            || str_contains($normalized, 'gates p1 news')
            || str_contains($normalized, 'news')
        ) {
            return 'gates-news';
        }

        return 'gates-projects';
    }

    private function validateDxItem(Request $request, ?DxItem $dxItem = null): array
    {
        return $request->validate([
            'category' => ['required', Rule::in(['domain', 'program', 'project'])],
            'domain_key' => ['required', Rule::in(['people', 'process', 'technology', 'other'])],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:2000'],
            'parent_id' => ['nullable', 'integer', 'exists:dx_items,id'],
            'icon' => ['nullable', 'string', 'max:100'],
            'image' => ['nullable', 'image', 'max:5120'],
            'document' => ['nullable', 'file', 'extensions:pdf,doc,docx,xls,xlsx,ppt,pptx,mp4,mov,jpg,jpeg,png', 'max:102400'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
    }

    private function validateNews(Request $request): array
    {
        return $request->validate([
            'eyebrow' => ['required', 'string', 'max:100'],
            'title' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'summary' => ['required', 'string', 'max:500'],
            'content' => ['required', 'string', 'max:10000'],
            'link_url' => ['nullable', 'url', 'max:255'],
            'accent' => ['required', Rule::in(['cyan', 'blue', 'gold', 'mint', 'violet', 'slate'])],
            'image_alt' => ['nullable', 'string', 'max:255'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);
    }

    private function newsPayload(Request $request, array $data, ?News $news = null): array
    {
        $thumbnailPath = $news?->thumbnail_path;

        if ($request->hasFile('thumbnail')) {
            $this->deletePublicFile($news?->thumbnail_path);
            /** @var \Illuminate\Filesystem\FilesystemAdapter $publicDisk */
            $publicDisk = Storage::disk('public');
            $thumbnailPath = $publicDisk->url($request->file('thumbnail')->store('news', 'public'));
        }

        return [
            'eyebrow' => $data['eyebrow'],
            'title' => $data['title'],
            'date' => $data['date'],
            'summary' => $data['summary'],
            'content' => $data['content'],
            'link_url' => $data['link_url'] ?: null,
            'thumbnail_path' => $thumbnailPath,
            'accent' => $data['accent'],
            'image_alt' => $data['image_alt'] ?: $data['title'],
        ];
    }

    private function dxItemPayload(Request $request, array $data, ?DxItem $dxItem = null): array
    {
        $parentId = $this->resolveDxParentId($data['category'], $data['parent_id'] ?? null);
        $parentItem = $parentId ? DxItem::query()->find($parentId) : null;
        $resolvedDomainKey = $data['category'] === 'project'
            ? ($parentItem?->domain_key ?: $data['domain_key'])
            : $data['domain_key'];
        $imagePath = $dxItem?->image_path;
        $fileUrl = $dxItem?->file_url;

        if ($request->hasFile('image')) {
            $this->deletePublicFile($dxItem?->image_path, ['images/people.png', 'images/process.png', 'images/technology.png']);
            /** @var \Illuminate\Filesystem\FilesystemAdapter $publicDisk */
            $publicDisk = Storage::disk('public');
            $imagePath = $publicDisk->url($request->file('image')->store('dx/images', 'public'));
        }

        if ($request->hasFile('document')) {
            $this->deletePublicFile($dxItem?->file_url);
            /** @var \Illuminate\Filesystem\FilesystemAdapter $publicDisk */
            $publicDisk = Storage::disk('public');
            $fileUrl = $publicDisk->url($request->file('document')->store('dx/files', 'public'));
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
            'domain_key' => $resolvedDomainKey,
            'code' => null,
            'icon' => $data['category'] === 'domain' ? ($data['icon'] ?: ($dxItem?->icon ?: $this->defaultDxDomainIcon($resolvedDomainKey))) : null,
            'image_path' => $data['category'] === 'domain' ? ($imagePath ?: $this->defaultDxDomainImage($resolvedDomainKey)) : null,
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
            'other' => 'bi-grid-3x3-gap',
            default => 'bi-pc-display',
        };
    }

    private function defaultDxDomainImage(string $domainKey): string
    {
        return match ($domainKey) {
            'people' => 'images/people.png',
            'process' => 'images/process.png',
            'other' => 'images/technology.png',
            default => 'images/technology.png',
        };
    }
}
