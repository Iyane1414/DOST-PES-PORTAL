<div class="table-responsive admin-dx-table-wrap">
    <table class="table admin-table admin-dx-table align-middle">
        <thead>
            <tr>
                <th>Title</th>
                <th>Category</th>
                <th>Domain</th>
                <th>Parent</th>
                <th>File</th>
                <th class="text-end">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($dxItems as $item)
                <tr>
                    <td>
                        <div class="admin-dx-record">
                            <div class="admin-dx-record-title">{{ $item->title }}</div>
                            <div class="admin-dx-record-meta">
                                {{ ucfirst($item->domain_key ?: 'General') }}
                                @if ($item->category === 'project' && $item->parent?->title)
                                    <span class="admin-dx-record-separator">/</span>
                                    {{ $item->parent->title }}
                                @endif
                            </div>
                        </div>
                        @if ($item->code)
                            <div class="admin-dx-record-code">{{ $item->code }}</div>
                        @endif
                    </td>
                    <td><span class="admin-dx-category-chip">{{ $item->category }}</span></td>
                    <td><span class="admin-dx-domain-label">{{ $item->domain_key ?: 'N/A' }}</span></td>
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
                            <a class="btn btn-sm btn-outline-primary rounded-pill admin-issuance-edit-btn" href="{{ route('admin.workspace', ['tab' => 'dx', 'edit_dx' => $item->id]) }}">Edit</a>
                            <form method="POST" action="{{ route('admin.dx-items.destroy', $item) }}">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger rounded-pill admin-issuance-delete-btn" type="submit">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-secondary-soft py-4">No DOST DX records yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
