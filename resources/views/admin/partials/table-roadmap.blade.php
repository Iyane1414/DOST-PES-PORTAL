<div class="table-responsive">
    <table class="table admin-table align-middle">
        <thead>
            <tr>
                <th>Year</th>
                <th>Title</th>
                <th>Milestones</th>
                <th>Status</th>
                <th class="text-end">Action</th>
            </tr>
        </thead>
        <tbody>
            @php
                $roadmapRows = ($dxRoadmapItems ?? collect())->values();
            @endphp
            @forelse ($roadmapRows as $item)
                <tr>
                    <td>{{ $item->year_label }}</td>
                    <td>
                        <div class="admin-issuance-record">
                            <div class="admin-issuance-record-title">{{ $item->title }}</div>
                            <div class="admin-issuance-record-meta">{{ \Illuminate\Support\Str::limit($item->description, 90) }}</div>
                        </div>
                    </td>
                    <td>{{ collect($item->milestones ?? [])->implode(', ') ?: 'No milestones' }}</td>
                    <td>
                        <span class="admin-issuance-chip">{{ $item->is_active ? 'Visible' : 'Hidden' }}</span>
                    </td>
                    <td class="text-end">
                        <div class="admin-action-stack justify-content-end">
                            <a class="btn btn-sm btn-outline-primary rounded-pill admin-issuance-edit-btn" href="{{ route('admin.workspace', ['tab' => 'roadmap', 'edit_roadmap' => $item->id]) }}">Edit</a>
                            <form method="POST" action="{{ route('admin.dx-roadmap.destroy', $item) }}">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger rounded-pill admin-issuance-delete-btn" type="submit">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-secondary-soft py-4">No DX roadmap items found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
