<div class="table-responsive admin-issuance-table-wrap">
    <table class="table admin-table admin-issuance-table align-middle">
        <thead>
            <tr>
                <th>Headline</th>
                <th>Type</th>
                <th>Date</th>
                <th class="text-end">Action</th>
            </tr>
        </thead>
        <tbody>
            @php
                $newsRows = ($workspaceNews ?? $news)->values();
            @endphp
            @foreach ($newsRows as $item)
                <tr
                    data-news-library-row
                    data-news-library-search="{{ strtolower(trim(($item->title ?? '').' '.($item->eyebrow ?? '').' '.($item->summary ?? ''))) }}"
                >
                    <td>
                        <div class="admin-issuance-record">
                            <div class="admin-issuance-record-title">{{ $item->title }}</div>
                            <div class="admin-issuance-record-meta">{{ \Illuminate\Support\Str::limit($item->summary, 90) }}</div>
                        </div>
                    </td>
                    <td><span class="admin-issuance-chip">{{ $item->eyebrow }}</span></td>
                    <td>{{ optional($item->date)->format('M d, Y') }}</td>
                    <td class="text-end">
                        <div class="admin-action-stack justify-content-end">
                            <a class="btn btn-sm btn-outline-primary rounded-pill admin-issuance-edit-btn" href="{{ route('admin.workspace', ['tab' => 'news', 'edit_news' => $item->id, 'news_search' => $newsSearch ?? null]) }}">Edit</a>
                            <form method="POST" action="{{ route('admin.news.destroy', $item) }}">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger rounded-pill admin-issuance-delete-btn" type="submit">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
                <tr data-news-library-empty-row @if ($newsRows->isNotEmpty()) hidden @endif>
                    <td colspan="4">
                        <div class="admin-issuance-empty-state">
                            <strong>No news stories found</strong>
                            <span>Publish your first PES in Action story to populate the homepage section.</span>
                        </div>
                    </td>
                </tr>
        </tbody>
    </table>
</div>
