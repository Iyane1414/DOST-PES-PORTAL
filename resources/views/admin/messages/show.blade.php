@extends('layouts.app', ['title' => 'Message Detail - DOST Admin'])

@section('body_class', 'admin-dashboard-page')

@section('content')
    <div class="admin-shell admin-shell-enhanced">
        @include('admin.partials.sidebar', ['activeSection' => 'workspace', 'activeTab' => 'messages'])

        <main class="admin-main admin-main-enhanced">
            <div class="admin-dashboard-top admin-workspace-top">
                <div class="admin-workspace-hero-copy">
                    <div class="admin-kicker">Publishing Workspace</div>
                    <h1 class="admin-dashboard-title admin-workspace-title">Messages</h1>
                    <p class="text-secondary-soft mb-0">Review sender details, open full message records, and send responses directly from the admin workspace.</p>
                </div>
            </div>

            @if (session('status'))
                <div class="alert alert-success admin-status-alert">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger admin-status-alert">
                    <strong>Reply failed.</strong>
                    <div class="mt-2">{{ $errors->first() }}</div>
                </div>
            @endif

            <div class="admin-message-detail-back">
                <a href="{{ route('admin.workspace', ['tab' => 'messages']) }}" class="btn btn-outline-secondary rounded-pill px-4">
                    <i class="bi bi-arrow-left me-2"></i>Back to Messages
                </a>
            </div>

            <div class="admin-message-detail-grid">
                <section class="admin-card admin-issuance-panel admin-issuance-form-panel admin-message-detail-panel">
                    <div class="admin-section-head admin-section-head-sm admin-issuance-panel-head">
                        <div>
                            <div class="admin-kicker mb-2">Message Record</div>
                            <h2 class="h4 fw-bold mb-1">{{ $messageItem->subject }}</h2>
                            <p class="text-secondary-soft mb-0">Sender details and full message content.</p>
                        </div>
                    </div>

                    <div class="admin-message-detail-meta-grid">
                        <div class="admin-message-detail-meta-card">
                            <div class="admin-message-detail-label">Sender</div>
                            <strong>{{ $messageItem->name }}</strong>
                            <span>{{ $messageItem->email }}</span>
                        </div>
                        <div class="admin-message-detail-meta-card">
                            <div class="admin-message-detail-label">Received</div>
                            <strong>{{ optional($messageItem->created_at)->format('M d, Y') }}</strong>
                            <span>{{ optional($messageItem->created_at)->format('h:i A') }}</span>
                        </div>
                        <div class="admin-message-detail-meta-card">
                            <div class="admin-message-detail-label">Status</div>
                            <strong>{{ $messageItem->replied_at ? 'Replied' : 'Awaiting Reply' }}</strong>
                            <span>
                                @if ($messageItem->replied_at)
                                    Replied {{ $messageItem->replied_at->diffForHumans() }}
                                @else
                                    No admin reply sent yet
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="admin-message-detail-body-card">
                        <div class="admin-message-detail-label">Full Message</div>
                        <div class="admin-message-detail-body">{{ $messageItem->message }}</div>
                    </div>

                    @if ($messageItem->replied_at && $messageItem->admin_reply_body)
                        <div class="admin-message-detail-history">
                            <div class="admin-message-detail-label">Latest Reply Sent</div>
                            <div class="admin-message-detail-history-subject">{{ $messageItem->admin_reply_subject }}</div>
                            <div class="admin-message-detail-history-body">{{ $messageItem->admin_reply_body }}</div>
                        </div>
                    @endif
                </section>

                <aside class="admin-card admin-workspace-card admin-issuance-panel admin-issuance-library-panel admin-message-reply-panel">
                    <div class="admin-section-head admin-section-head-sm admin-issuance-panel-head">
                        <div>
                            <div class="admin-kicker mb-2">Reply Desk</div>
                            <h2 class="h4 fw-bold mb-1">Send Email Reply</h2>
                            <p class="text-secondary-soft mb-0">Your response will be sent directly to the sender's email address.</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('admin.messages.reply', $messageItem) }}" class="row g-3">
                        @csrf
                        <div class="col-12">
                            <div class="admin-issuance-field">
                                <label class="form-label">To</label>
                                <input class="form-control" type="text" value="{{ $messageItem->name }} - {{ $messageItem->email }}" readonly>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="admin-issuance-field">
                                <label class="form-label">Reply Subject</label>
                                <input class="form-control" type="text" name="reply_subject" value="{{ old('reply_subject', 'Re: '.$messageItem->subject) }}" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="admin-issuance-field">
                                <label class="form-label">Reply Message</label>
                                <textarea class="form-control" name="reply_body" rows="12" placeholder="Write your response here..." required>{{ old('reply_body') }}</textarea>
                            </div>
                        </div>
                        <div class="col-12 admin-issuance-form-actions">
                            <button class="btn btn-accent rounded-pill px-4" type="submit">Send Reply</button>
                        </div>
                    </form>

                    <div class="admin-message-related-list">
                        <div class="admin-message-detail-label">Latest Messages</div>
                        @foreach ($messageList as $listMessage)
                            <a href="{{ route('admin.messages.show', $listMessage) }}" class="admin-message-related-item {{ $listMessage->is($messageItem) ? 'is-current' : '' }}">
                                <strong>{{ $listMessage->subject }}</strong>
                                <span>{{ $listMessage->name }} - {{ optional($listMessage->created_at)->diffForHumans() }}</span>
                            </a>
                        @endforeach
                    </div>
                </aside>
            </div>
        </main>
    </div>
@endsection

