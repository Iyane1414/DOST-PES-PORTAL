<div class="table-responsive">
    <table class="table admin-table align-middle">
        <thead><tr><th>Title</th><th>Type</th><th>Date</th><th class="text-end">Action</th></tr></thead>
        <tbody>@foreach ($materials as $item)<tr><td>{{ $item->title }}</td><td>{{ $item->type }}</td><td>{{ optional($item->date)->format('M d, Y') }}</td><td class="text-end"><form method="POST" action="{{ route('admin.materials.destroy', $item) }}">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger rounded-pill" type="submit">Delete</button></form></td></tr>@endforeach</tbody>
    </table>
</div>
