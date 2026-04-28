<div class="table-responsive admin-issuance-table-wrap">
    <table class="table admin-table admin-issuance-table align-middle">
        <thead>
            <tr>
                <th>Title</th>
                <th>Code</th>
                <th>Type</th>
                <th>Date</th>
                <th class="text-end">Action</th>
            </tr>
        </thead>
        <tbody>
            @php
                $gateRows = ($workspaceGatesProjects ?? $gatesProjects)->values();
                $emptyStateLabel = match ($activeTab ?? '') {
                    'gates-issuances' => 'GATES Project 1 issuances',
                    'gates-news' => 'GATES Project 1 news',
                    default => 'GATES Project 1 projects and videos',
                };
            @endphp
            @foreach ($gateRows as $item)
                @php
                    $isNewsRow = str_contains(strtolower((string) $item->type), 'news');
                    $rowSummary = $isNewsRow ? ($item->news_summary ?: $item->description) : $item->description;
                    $rowSearchText = strtolower(trim(implode(' ', array_filter([
                        $item->title ?? '',
                        $item->code ?? '',
                        $item->type ?? '',
                        $item->news_eyebrow ?? '',
                        $rowSummary ?? '',
                    ]))));
                @endphp
                <tr
                    data-gates-library-row
                    data-gates-library-search="{{ $rowSearchText }}"
                >
                    <td>
                        <div class="admin-issuance-record">
                            <div class="admin-issuance-record-title">{{ $item->title }}</div>
                            <div class="admin-issuance-record-meta">{{ \Illuminate\Support\Str::limit((string) $rowSummary, 90) }}</div>
                        </div>
                    </td>
                    <td>
                        @if ($item->code)
                            <span class="admin-issuance-chip">{{ $item->code }}</span>
                        @else
                            <span class="admin-dx-file-empty">No code</span>
                        @endif
                    </td>
                    <td>{{ $item->type ?? 'Project' }}</td>
                    <td>{{ optional($item->date)->format('M d, Y') ?: 'No date' }}</td>
                    <td class="text-end">
                        <div class="admin-action-stack justify-content-end">
                            <a class="btn btn-sm btn-outline-primary rounded-pill admin-issuance-edit-btn" href="{{ route('admin.workspace', ['tab' => $activeTab ?? 'gates-projects', 'edit_gate' => $item->id, 'material_search' => $materialSearch ?? null]) }}">Edit</a>
                            <form method="POST" action="{{ route('admin.gates.destroy', $item) }}">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="workspace_tab" value="{{ $activeTab ?? 'gates-projects' }}">
                                <button class="btn btn-sm btn-outline-danger rounded-pill admin-issuance-delete-btn" type="submit">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
            <tr data-gates-library-empty-row @if ($gateRows->isNotEmpty()) hidden @endif>
                <td colspan="5">
                    <div class="admin-issuance-empty-state">
                        <strong>No {{ $emptyStateLabel }} yet</strong>
                        <span>Your uploaded {{ strtolower($emptyStateLabel) }} files will appear here.</span>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>
