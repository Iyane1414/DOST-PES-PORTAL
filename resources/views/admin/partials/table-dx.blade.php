<div class="table-responsive">
    <table class="table admin-table align-middle">
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
                        <strong>{{ $item->title }}</strong>
                        @if ($item->code)
                            <div class="small text-secondary-soft mt-1">{{ $item->code }}</div>
                        @endif
                    </td>
                    <td class="text-uppercase small fw-semibold">{{ $item->category }}</td>
                    <td class="text-capitalize">{{ $item->domain_key ?: 'N/A' }}</td>
                    <td>{{ $item->parent?->title ?? 'None' }}</td>
                    <td>
                        @if ($item->file_url)
                            <a href="{{ $item->file_url }}" target="_blank" rel="noreferrer">Open File</a>
                        @else
                            <span class="text-secondary-soft">No file</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <form method="POST" action="{{ route('admin.dx-items.destroy', $item) }}">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger rounded-pill" type="submit">Delete</button>
                        </form>
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
