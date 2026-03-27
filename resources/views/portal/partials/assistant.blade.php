<button class="assistant-toggle" type="button" id="assistant-toggle"><i class="bi bi-chat-dots-fill"></i></button>
<div class="assistant-panel d-none" id="assistant-panel">
    <div class="assistant-header">
        <div>
            <div class="fw-bold">PES Assistant</div>
            <div class="small text-white-50">Mandates, divisions, issuances, and DX</div>
        </div>
        <button class="btn btn-sm btn-outline-light border-0" type="button" id="assistant-close"><i class="bi bi-x-lg"></i></button>
    </div>
    <div class="assistant-body" id="assistant-body">
        <div class="assistant-bubble assistant-bubble-model">Hello! I am your PES AI Assistant. How can I help you today?</div>
    </div>
    <div class="assistant-suggestions">
        @foreach (['What is the PES mandate?', 'Show me latest issuances', 'What are the PES divisions?', 'Tell me about DOST DX'] as $suggestion)
            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill assistant-suggestion">{{ $suggestion }}</button>
        @endforeach
    </div>
    <form class="assistant-form" id="assistant-form">
        <input class="form-control" type="text" id="assistant-input" placeholder="Ask me anything...">
        <button class="btn btn-accent" type="submit"><i class="bi bi-arrow-right"></i></button>
    </form>
</div>
