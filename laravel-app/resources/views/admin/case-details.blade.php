<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User details</title>
    <style>
        * { box-sizing: border-box; }
        body { min-height: 100vh; margin: 0; padding: 40px 24px; background: #050505; color: #f5f5f5; font-family: Arial, sans-serif; }
        .shell { width: min(900px, 100%); margin: 0 auto; }
        .top { display: flex; justify-content: space-between; align-items: center; gap: 20px; margin-bottom: 24px; }
        h1, h2, p { margin-top: 0; }
        h1 { margin-bottom: 8px; font-size: 30px; }
        h2 { margin-bottom: 18px; font-size: 18px; }
        p { color: #888; line-height: 1.5; }
        .back { color: #bbb; text-decoration: none; }
        .back:hover { color: #fff; }
        .panel { margin-bottom: 20px; padding: 24px; border: 1px solid #292929; border-radius: 12px; background: #0d0d0d; }
        .details { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px; }
        .detail label { display: block; margin-bottom: 6px; color: #777; font-size: 12px; text-transform: uppercase; letter-spacing: .05em; }
        .detail strong { color: #eee; font-size: 15px; font-weight: 500; overflow-wrap: anywhere; }
        .status { display: inline-block; padding: 5px 9px; border: 1px solid #365740; border-radius: 12px; color: #b9efc5; font-size: 12px; }
        .thread { display: flex; flex-direction: column; gap: 10px; max-height: 440px; min-height: 120px; padding: 4px 0; overflow-y: auto; }
        .bubble { align-self: flex-start; max-width: 78%; padding: 11px 13px; border-radius: 10px 10px 10px 3px; background: #191919; color: #ddd; line-height: 1.45; overflow-wrap: anywhere; }
        .bubble.admin { align-self: flex-end; border-radius: 10px 10px 3px 10px; background: #18351f; }
        .bubble small { display: block; margin-top: 5px; color: #777; font-size: 10px; }
        .typing-indicator { min-height: 16px; margin: -10px 0 10px; color: #777; font-size: 11px; }
        .typing-indicator.is-visible { display: flex; align-items: center; gap: 3px; }
        .typing-indicator span { width: 4px; height: 4px; border-radius: 50%; background: #777; animation: typing-dot 1.2s infinite ease-in-out; }
        .typing-indicator span:nth-child(2) { animation-delay: .15s; }
        .typing-indicator span:nth-child(3) { animation-delay: .3s; }
        @keyframes typing-dot { 0%, 60%, 100% { opacity: .3; transform: translateY(0); } 30% { opacity: 1; transform: translateY(-2px); } }
        .empty { color: #888; }
        .reply { display: flex; gap: 10px; margin-top: 20px; }
        .reply input { flex: 1; min-width: 0; height: 44px; padding: 0 13px; border: 1px solid #333; border-radius: 22px; outline: 0; background: #050505; color: #fff; }
        .end-session { height: 36px; margin-top: 12px; padding: 0 14px; border: 1px solid #743d3d; border-radius: 18px; background: transparent; color: #ffaaaa; font-size: 12px; }
        button { height: 44px; padding: 0 20px; border: 0; border-radius: 22px; background: #f2f2f2; color: #111; font-weight: 700; cursor: pointer; }
        .error { color: #ff8d8d; font-size: 13px; }
        @media (max-width: 560px) { body { padding: 24px 16px; } .top { align-items: flex-start; flex-direction: column; } .details { grid-template-columns: 1fr; } .reply { flex-direction: column; } button { width: 100%; } }
    </style>
</head>
<body>
    <main class="shell">
        <header class="top">
            <div>
                <h1>User details</h1>
                <p>Full support case and conversation history.</p>
            </div>
            <a class="back" href="{{ route('admin.dashboard') }}">Back to records</a>
        </header>

        <section class="panel">
            <h2>Case information</h2>
            <div class="details">
                <div class="detail"><label>Email</label><strong>{{ $supportCase->email }}</strong></div>
                <div class="detail"><label>Username</label><strong>{{ $supportCase->username }}</strong></div>
                <div class="detail"><label>New email</label><strong>{{ $supportCase->new_email ?? 'Not provided' }}</strong></div>
                <div class="detail"><label>Status</label><strong><span class="status">{{ str_replace('_', ' ', ucfirst($supportCase->status)) }}</span></strong></div>
                <div class="detail"><label>Received</label><strong>{{ $supportCase->created_at->format('Y-m-d H:i') }}</strong></div>
                <div class="detail"><label>Last updated</label><strong>{{ $supportCase->updated_at->format('Y-m-d H:i') }}</strong></div>
            </div>
            <div class="detail" style="margin-top: 18px"><label>Original message</label><strong>{{ $supportCase->message }}</strong></div>
        </section>

        <section class="panel">
            <h2>Conversation</h2>
            <div class="typing-indicator" data-typing-indicator aria-live="polite"></div>
            <div class="thread" data-thread>
                @forelse ($supportCase->messages as $message)
                    <div class="bubble {{ $message->sender === 'admin' ? 'admin' : '' }}">{{ $message->body }}<small>{{ ucfirst($message->sender) }} · {{ $message->created_at->format('Y-m-d H:i') }}</small></div>
                @empty
                    <p class="empty">No messages yet.</p>
                @endforelse
            </div>
            <form class="reply" method="POST" action="{{ route('admin.messages.store', $supportCase) }}">
                @csrf
                <input name="body" type="text" maxlength="4000" placeholder="Reply to user" required>
                <button type="submit">Send reply</button>
            </form>
            @error('body')<p class="error">{{ $message }}</p>@enderror
            @if ($supportCase->access_enabled)
                <form method="POST" action="{{ route('admin.cases.end-session', $supportCase) }}" onsubmit="return confirm('End this user session? Their access will be disabled immediately.');">
                    @csrf
                    <button class="end-session" type="submit">End user session</button>
                </form>
            @else
                <p class="error">This user session has ended. Their access is disabled.</p>
            @endif
        </section>
    </main>
    <script>
        const messageThread = document.querySelector('[data-thread]');
        const messagePollUrl = @json(route('admin.messages.index', $supportCase));
        const typingStatusUrl = @json(route('admin.typing.show', $supportCase));
        const typingStoreUrl = @json(route('admin.typing.store', $supportCase));
        const typingToken = document.querySelector('input[name="_token"]').value;

        function escapeMessage(value) {
            return String(value).replace(/[&<>'"]/g, character => ({
                '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;'
            }[character]));
        }

        function renderMessages(messages) {
            if (!messages.length) {
                messageThread.innerHTML = '<p class="empty">No messages yet.</p>';
                return;
            }

            messageThread.innerHTML = messages.map(message => `<div class="bubble ${message.sender === 'admin' ? 'admin' : ''}">${escapeMessage(message.body)}<small>${escapeMessage(message.sender.charAt(0).toUpperCase() + message.sender.slice(1))} · ${escapeMessage(message.created_at)}</small></div>`).join('');
            messageThread.scrollTop = messageThread.scrollHeight;
        }

        async function pollMessages() {
            try {
                const response = await fetch(messagePollUrl, { headers: { Accept: 'application/json' }, credentials: 'same-origin' });
                if (response.ok) renderMessages(await response.json());
            } catch (error) {
                // Keep the current conversation visible if polling is temporarily unavailable.
            }
        }

        async function pollTyping() {
            try {
                const response = await fetch(typingStatusUrl, { headers: { Accept: 'application/json' }, credentials: 'same-origin' });
                if (response.ok) {
                    const indicator = document.querySelector('[data-typing-indicator]');
                    indicator.innerHTML = (await response.json()).typing ? '<span></span><span></span><span></span>' : '';
                    indicator.classList.toggle('is-visible', indicator.innerHTML !== '');
                }
            } catch (error) {
                // Keep the current indicator state if polling is temporarily unavailable.
            }
        }

        let lastTypingSignal = 0;
        document.querySelector('.reply input').addEventListener('input', () => {
            const now = Date.now();
            if (now - lastTypingSignal < 1500) return;
            lastTypingSignal = now;
            fetch(typingStoreUrl, { method: 'POST', headers: { 'X-CSRF-TOKEN': typingToken, Accept: 'application/json' }, credentials: 'same-origin' });
        });

        pollMessages();
        pollTyping();
        window.setInterval(pollMessages, 3000);
        window.setInterval(pollTyping, 1000);
    </script>
</body>
</html>
