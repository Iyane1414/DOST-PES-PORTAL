<div class="admin-message-inbox-list">
    @forelse ($workspaceMessages as $message)
        <a href="{{ route('admin.messages.show', $message) }}" class="admin-message-row">
            <div class="admin-message-row-avatar">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($message->name, 0, 1)) }}</div>
            <div class="admin-message-row-main">
                <div class="admin-message-row-top">
                    <div>
                        <div class="admin-message-row-name">{{ $message->name }}</div>
                        <div class="admin-message-row-subject">{{ $message->subject }}</div>
                    </div>
                    <div class="admin-message-row-time">{{ optional($message->created_at)->diffForHumans() }}</div>
                </div>
                <div class="admin-message-row-snippet">{{ \Illuminate\Support\Str::limit($message->message, 120) }}</div>
                <div class="admin-message-row-meta">
                    <span>{{ $message->email }}</span>
                    <span class="admin-message-row-status {{ $message->replied_at ? 'is-replied' : 'is-new' }}">
                        {{ $message->replied_at ? 'Replied' : 'Awaiting Reply' }}
                    </span>
                </div>
            </div>
        </a>
    @empty
        <div class="admin-message-empty">
            <div class="admin-message-empty-icon"><i class="bi bi-chat-left-text"></i></div>
            <strong>No messages found</strong>
            <span>No messages matched your current search or sorting view.</span>
        </div>
    @endforelse
</div>
