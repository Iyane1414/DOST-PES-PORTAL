<div class="admin-message-list">
    @forelse ($workspaceMessages as $message)
        <article id="message-{{ $message->id }}" class="admin-message-card @if (($selectedMessageId ?? null) === $message->id) is-focused @endif">
            <div class="admin-message-card-top">
                <div>
                    <div class="admin-message-subject">{{ $message->subject }}</div>
                    <div class="admin-message-sender">{{ $message->name }} <span>•</span> {{ $message->email }}</div>
                </div>
                <div class="admin-message-date">{{ optional($message->created_at)->format('M d, Y • h:i A') }}</div>
            </div>

            <div class="admin-message-preview">
                {{ \Illuminate\Support\Str::limit($message->message, 170) }}
            </div>

            <div class="admin-message-full">
                <div class="admin-message-full-label">Full Message</div>
                <div class="admin-message-full-body">{{ $message->message }}</div>
            </div>
        </article>
    @empty
        <div class="admin-message-empty">
            <div class="admin-message-empty-icon"><i class="bi bi-chat-left-text"></i></div>
            <strong>No messages found</strong>
            <span>No messages matched your current search or sorting view.</span>
        </div>
    @endforelse
</div>
