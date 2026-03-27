<div class="table-responsive">
    <table class="table admin-table align-middle">
        <thead><tr><th>Title</th><th>Category</th><th class="text-end">Action</th></tr></thead>
        <tbody>@foreach ($dxItems as $item)<tr><td>{{ $item->title }}</td><td class="text-uppercase small fw-semibold">{{ $item->category }}</td><td class="text-end"><form method="POST" action="{{ route('admin.dx-items.destroy', $item) }}">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger rounded-pill" type="submit">Delete</button></form></td></tr>@endforeach</tbody>
    </table>
</div>
