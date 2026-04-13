@foreach ($filteredIssuances as $issuance)
    @php
        $extension = strtolower(pathinfo(parse_url($issuance->url ?? '', PHP_URL_PATH) ?? '', PATHINFO_EXTENSION));
        $isPdfPreview = $extension === 'pdf';
        $isImagePreview = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true);
        $displayTitle = trim(($issuance->erm_number ? ($issuance->erm_number.' - ') : '').($issuance->title ?? ''));
    @endphp
    <div class="modal fade" id="issuanceModal{{ $issuance->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content issuance-modal">
                <div class="modal-body p-4 p-lg-5">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <span class="badge-soft">{{ $issuance->category }}</span>
                            <h3 class="display-6 fw-bold mt-3 mb-2">{{ $displayTitle }}</h3>
                            <div class="text-secondary-soft">{{ $issuance->division }} &bull; {{ optional($issuance->date)->format('F d, Y') }}</div>
                        </div>
                        <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="issuance-modal-card">
                        @if ($isPdfPreview)
                            <iframe class="issuance-preview-frame" src="{{ $issuance->url }}" title="{{ $displayTitle }}"></iframe>
                        @elseif ($isImagePreview)
                            <img class="issuance-preview-image" src="{{ $issuance->url }}" alt="{{ $displayTitle }}">
                        @else
                            <div class="issuance-preview-fallback">
                                <div class="issuance-preview-fallback-icon">
                                    <i class="bi bi-file-earmark-text"></i>
                                </div>
                                <h4>Preview is not available for this file type.</h4>
                                <p class="mb-0">This browser can preview PDFs and images in the modal. Use the download button below to open this document in its proper app.</p>
                            </div>
                        @endif
                    </div>
                    <div class="d-flex flex-column flex-sm-row gap-3 mt-4">
                        <a class="btn btn-accent rounded-pill px-4" href="{{ $issuance->url ?: '#' }}" target="_blank" rel="noreferrer">Download File</a>
                        <button class="btn btn-outline-secondary rounded-pill px-4" type="button" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach
