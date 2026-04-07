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
            @endphp
            @foreach ($gateRows as $item)
                <tr
                    data-gates-library-row
                    data-gates-library-search="{{ strtolower(trim(($item->title ?? '').' '.($item->code ?? '').' '.($item->type ?? '').' '.($item->description ?? ''))) }}"
                >
                    <td>
                        <div class="admin-issuance-record">
                            <div class="admin-issuance-record-title">{{ $item->title }}</div>
                            <div class="admin-issuance-record-meta">{{ \Illuminate\Support\Str::limit($item->description, 90) }}</div>
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
                            <a class="btn btn-sm btn-outline-primary rounded-pill admin-issuance-edit-btn" href="{{ route('admin.workspace', ['tab' => 'gates', 'edit_gate' => $item->id, 'material_search' => $materialSearch ?? null]) }}">Edit</a>
                            <form method="POST" action="{{ route('admin.gates.destroy', $item) }}">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger rounded-pill admin-issuance-delete-btn" type="submit">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
            <tr data-gates-library-empty-row @if ($gateRows->isNotEmpty()) hidden @endif>
                <td colspan="4">
                    <div class="admin-issuance-empty-state">
                        <strong>No GATES projects yet</strong>
                        <span>Your uploaded GATES project files will appear here.</span>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>
