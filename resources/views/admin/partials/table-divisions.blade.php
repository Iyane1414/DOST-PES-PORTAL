<div class="table-responsive">
    <table class="table admin-table align-middle">
        <thead><tr><th>Name</th><th>Head</th><th class="text-end">Action</th></tr></thead>
        <tbody>@foreach ($divisions as $item)<tr><td>{{ $item->name }}</td><td>{{ $item->head }}</td><td class="text-end"><form method="POST" action="{{ route('admin.divisions.destroy', $item) }}">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger rounded-pill" type="submit">Delete</button></form></td></tr>@endforeach</tbody>
    </table>
</div>
