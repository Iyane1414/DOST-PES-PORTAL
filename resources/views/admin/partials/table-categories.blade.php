<div class="table-responsive">
    <table class="table admin-table align-middle">
        <thead><tr><th>Name</th><th class="text-end">Action</th></tr></thead>
        <tbody>@foreach ($categories as $item)<tr><td>{{ $item->name }}</td><td class="text-end"><form method="POST" action="{{ route('admin.categories.destroy', $item) }}">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger rounded-pill" type="submit">Delete</button></form></td></tr>@endforeach</tbody>
    </table>
</div>
