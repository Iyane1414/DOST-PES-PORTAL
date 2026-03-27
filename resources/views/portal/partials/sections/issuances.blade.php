<section class="section-space section-soft" id="issuances">
    <div class="container">
        <div class="issuance-shell">
            <div class="section-header d-flex flex-column flex-xl-row justify-content-between align-items-xl-end gap-3 position-relative">
                <div>
                    <h2 class="section-title issuance-title split-title">PES<br><span class="split-title-accent">Issuances</span></h2>
                    <p class="section-copy issuance-copy">Official memo, letters, and orders.</p>
                </div>
                <form class="row g-2 align-items-center issuance-filters" method="GET" action="{{ route('portal.home') }}">
                    <div class="col-12 col-md-auto">
                        <div class="issuance-search-wrap">
                            <i class="bi bi-search"></i>
                            <input class="form-control rounded-pill px-4 issuance-search" type="search" name="search" value="{{ $search }}" placeholder="Search issuances...">
                        </div>
                    </div>
                    <div class="col-6 col-md-auto">
                        <select class="form-select rounded-pill px-4 issuance-select issuance-select-accent" name="category">
                            <option value="All">All</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->name }}" @selected($categoryFilter === $category->name)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-auto">
                        <button class="btn issuance-filter-btn rounded-pill px-4 w-100" type="submit">Filter by <i class="bi bi-chevron-down ms-2"></i></button>
                    </div>
                </form>
            </div>
            <div class="table-card issuance-table-card">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr><th>Title</th><th>Category</th><th>Division</th><th class="issuance-date-col">Date</th><th class="issuance-action-col">Action</th></tr></thead>
                        <tbody>
                            @forelse ($filteredIssuances as $issuance)
                                <tr>
                                    <td class="fw-semibold">{{ $issuance->title }}</td>
                                    <td>{{ $issuance->category }}</td>
                                    <td>{{ $issuance->division }}</td>
                                    <td class="issuance-date-col">{{ optional($issuance->date)->format('Y-m-d') }}</td>
                                    <td class="issuance-action-col">
                                        <div class="issuance-action-wrap">
                                            <button class="btn issuance-view-btn" type="button" data-bs-toggle="modal" data-bs-target="#issuanceModal{{ $issuance->id }}">View</button>
                                            <a class="link-accent" href="{{ $issuance->url ?: '#' }}" target="_blank" rel="noreferrer" download>Download</a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center py-5 text-secondary-soft">No issuances found matching your criteria.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
