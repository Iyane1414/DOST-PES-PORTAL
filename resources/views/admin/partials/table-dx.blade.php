<div class="table-responsive admin-issuance-table-wrap admin-dx-library-wrap">
    <table class="table admin-table admin-issuance-table admin-dx-table align-middle">
        <thead>
            <tr>
                <th>Title</th>
                <th>Domain</th>
                <th>Sub-program</th>
                <th>File</th>
                <th class="text-end">Action</th>
            </tr>
        </thead>
        <tbody>
            @php
                $dxProjectRows = ($workspaceDxItems ?? $dxItems)->where('category', 'project')->values();
            @endphp
            @foreach ($dxProjectRows as $item)
                <tr
                    data-dx-library-row
                    data-dx-library-search="{{ strtolower(trim(($item->title ?? '').' '.($item->parent?->title ?? '').' '.($item->domain_key ?? ''))) }}"
                    data-dx-library-title="{{ strtolower(trim($item->title ?? '')) }}"
                    data-dx-library-program="{{ strtolower(trim($item->parent?->title ?? '')) }}"
                >
                    <td>
                        <div class="admin-dx-record">
                            <div class="admin-dx-record-title">{{ $item->title }}</div>
                            <div class="admin-dx-record-meta">{{ ucfirst($item->domain_key ?: 'General') }}</div>
                        </div>
                    </td>
                    <td><span class="admin-dx-domain-label">{{ ucfirst($item->domain_key ?: 'N/A') }}</span></td>
                    <td class="admin-dx-parent-cell">{{ $item->parent?->title ?? 'None' }}</td>
                    <td>
                        @if ($item->file_url)
                            <a class="admin-dx-file-link" href="{{ $item->file_url }}" target="_blank" rel="noreferrer">Open File</a>
                        @else
                            <span class="admin-dx-file-empty">No file</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <div class="admin-action-stack justify-content-end">
                            <a class="btn btn-sm btn-outline-primary rounded-pill admin-issuance-edit-btn" href="{{ route('admin.workspace', ['tab' => 'dx', 'edit_dx' => $item->id, 'dx_search' => $dxSearch ?? null]) }}">Edit</a>
                            <form method="POST" action="{{ route('admin.dx-items.destroy', $item) }}">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger rounded-pill admin-issuance-delete-btn" type="submit">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
            <tr data-dx-library-empty-row @if ($dxProjectRows->isNotEmpty()) hidden @endif>
                <td colspan="5" class="text-center text-secondary-soft py-4">No DOST DX project records found.</td>
            </tr>
        </tbody>
    </table>
</div>
