<div class="table-responsive admin-issuance-table-wrap">
    <table class="table admin-table admin-issuance-table align-middle">
        <thead>
            <tr>
                <th>Title</th>
                <th>Category</th>
                <th>Date</th>
                <th class="text-end">Action</th>
            </tr>
        </thead>
        <tbody>
            @php
                $issuanceRows = ($workspaceIssuances ?? $issuances)->values();
            @endphp
            @foreach ($issuanceRows as $item)
                <tr
                    data-admin-issuance-library-row
                    data-admin-issuance-library-search="{{ strtolower(trim(($item->title ?? '').' '.($item->category ?? '').' '.($item->division ?? ''))) }}"
                >
                    <td>
                        <div class="admin-issuance-record">
                            <div class="admin-issuance-record-title">{{ $item->title }}</div>
                            <div class="admin-issuance-record-meta">{{ $item->division }}</div>
                        </div>
                    </td>
                    <td><span class="admin-issuance-chip">{{ $item->category }}</span></td>
                    <td>{{ optional($item->date)->format('M d, Y') }}</td>
                    <td class="text-end">
                        <div class="admin-action-stack justify-content-end">
                            <a class="btn btn-sm btn-outline-primary rounded-pill admin-issuance-edit-btn" href="{{ route('admin.workspace', ['tab' => 'issuances', 'edit_issuance' => $item->id, 'issuance_search' => $issuanceSearch ?? null]) }}">Edit</a>
                            <form method="POST" action="{{ route('admin.issuances.destroy', $item) }}">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger rounded-pill admin-issuance-delete-btn" type="submit">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
                <tr data-admin-issuance-library-empty-row @if ($issuanceRows->isNotEmpty()) hidden @endif>
                    <td colspan="4">
                        <div class="admin-issuance-empty-state">
                            <strong>No issuances found</strong>
                            <span>Try a different search term or publish a new issuance record.</span>
                        </div>
                    </td>
                </tr>
        </tbody>
    </table>
</div>
