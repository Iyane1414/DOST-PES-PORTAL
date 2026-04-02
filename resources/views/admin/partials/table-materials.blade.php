<div class="table-responsive admin-issuance-table-wrap">
    <table class="table admin-table admin-issuance-table align-middle">
        <thead>
            <tr>
                <th>Title</th>
                <th>Type</th>
                <th>Date</th>
                <th class="text-end">Action</th>
            </tr>
        </thead>
        <tbody>
            @php
                $materialRows = ($workspaceMaterials ?? $materials)->values();
            @endphp
            @foreach ($materialRows as $item)
                <tr
                    data-material-library-row
                    data-material-library-search="{{ strtolower(trim(($item->title ?? '').' '.($item->type ?? '').' '.($item->division ?? ''))) }}"
                >
                    <td>
                        <div class="admin-issuance-record">
                            <div class="admin-issuance-record-title">{{ $item->title }}</div>
                            <div class="admin-issuance-record-meta">{{ $item->division }}</div>
                        </div>
                    </td>
                    <td><span class="admin-issuance-chip">{{ $item->type }}</span></td>
                    <td>{{ optional($item->date)->format('M d, Y') }}</td>
                    <td class="text-end">
                        <div class="admin-action-stack justify-content-end">
                            <a class="btn btn-sm btn-outline-primary rounded-pill admin-issuance-edit-btn" href="{{ route('admin.workspace', ['tab' => 'materials', 'edit_material' => $item->id]) }}">Edit</a>
                            <form method="POST" action="{{ route('admin.materials.destroy', $item) }}">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger rounded-pill admin-issuance-delete-btn" type="submit">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
                <tr data-material-library-empty-row @if ($materialRows->isNotEmpty()) hidden @endif>
                    <td colspan="4">
                        <div class="admin-issuance-empty-state">
                            <strong>No materials yet</strong>
                            <span>Saved materials will appear here once you add your first record.</span>
                        </div>
                    </td>
                </tr>
        </tbody>
    </table>
</div>
