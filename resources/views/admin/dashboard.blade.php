<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invitation dashboard</title>
    <style>
        * { box-sizing: border-box; }
        body { min-height: 100vh; margin: 0; padding: 40px 24px; background: #050505; color: #f5f5f5; font-family: Arial, sans-serif; }
        .shell { width: min(760px, 100%); margin: 0 auto; }
        header { display: flex; justify-content: space-between; align-items: center; gap: 20px; margin-bottom: 32px; }
        h1 { margin: 0 0 8px; font-size: 30px; }
        p { color: #888; line-height: 1.5; }
        .logout { border: 1px solid #333; border-radius: 20px; padding: 9px 16px; background: transparent; color: #ddd; cursor: pointer; }
        .panel { padding: 28px; border: 1px solid #292929; border-radius: 12px; background: #0d0d0d; }
        label { display: block; margin-bottom: 8px; color: #aaa; font-size: 14px; }
        .controls { display: flex; gap: 12px; }
        input { width: 140px; height: 46px; padding: 0 14px; border: 1px solid #333; border-radius: 6px; background: #050505; color: #fff; font-size: 15px; }
        button { height: 46px; padding: 0 20px; border: 0; border-radius: 23px; background: #f2f2f2; color: #111; font-weight: 700; cursor: pointer; }
        .invite { margin-top: 28px; padding: 18px; border: 1px solid #274a31; border-radius: 8px; background: #0d1b11; }
        .invite code { display: block; margin-top: 10px; overflow-wrap: anywhere; color: #b9efc5; line-height: 1.5; }
        .records { margin-top: 28px; overflow-x: auto; border: 1px solid #292929; border-radius: 12px; background: #0d0d0d; }
        .records h2 { margin: 0; padding: 22px 24px; border-bottom: 1px solid #292929; font-size: 18px; }
        table { width: 100%; min-width: 680px; border-collapse: collapse; text-align: left; }
        th, td { padding: 14px 16px; border-bottom: 1px solid #202020; vertical-align: top; font-size: 13px; }
        th { color: #888; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: .05em; }
        td { color: #ddd; }
        td small { display: block; max-width: 240px; margin-top: 5px; color: #888; line-height: 1.4; }
        .status { display: inline-block; padding: 5px 9px; border: 1px solid #365740; border-radius: 12px; color: #b9efc5; font-size: 12px; }
        .empty { padding: 24px; color: #888; }
        .thread { min-width: 260px; max-width: 360px; max-height: 180px; overflow-y: auto; }
        .bubble { margin: 6px 0; padding: 8px 10px; border-radius: 8px; background: #171717; color: #ddd; line-height: 1.4; }
        .bubble.admin { background: #18351f; }
        .bubble small { display: block; margin-top: 4px; color: #777; font-size: 10px; }
        .typing-indicator { min-height: 15px; color: #777; font-size: 11px; }
        .typing-indicator.is-visible { display: flex; align-items: center; gap: 3px; }
        .typing-indicator span { width: 4px; height: 4px; border-radius: 50%; background: #777; animation: typing-dot 1.2s infinite ease-in-out; }
        .typing-indicator span:nth-child(2) { animation-delay: .15s; }
        .typing-indicator span:nth-child(3) { animation-delay: .3s; }
        @keyframes typing-dot { 0%, 60%, 100% { opacity: .3; transform: translateY(0); } 30% { opacity: 1; transform: translateY(-2px); } }
        .reply-form { display: flex; gap: 6px; margin-top: 10px; }
        .reply-form input { width: 180px; height: 36px; }
        .reply-form button { height: 36px; padding: 0 12px; font-size: 12px; }
        .end-session { margin-top: 8px; height: 32px; padding: 0 12px; border: 1px solid #743d3d; border-radius: 16px; background: transparent; color: #ffaaaa; font-size: 11px; }
        .case-link { color: #fff; text-decoration: none; }
        .case-link:hover { text-decoration: underline; }
        .error { color: #ff8d8d; font-size: 13px; }
        @media (max-width: 520px) { header { align-items: flex-start; flex-direction: column; } .controls { flex-direction: column; } input, button { width: 100%; } }
    </style>
</head>
<body>
    <main class="shell">
        <header>
            <div><h1>Invitation dashboard</h1><p>Generate a signed link for a person you want to allow into the app.</p></div>
            <form method="POST" action="{{ route('admin.logout') }}">@csrf<button class="logout" type="submit">Sign out</button></form>
        </header>
        <section class="panel">
            <form method="POST" action="{{ route('admin.invites.create') }}">
                @csrf
                <label for="hours">Link validity in hours</label>
                <div class="controls"><input id="hours" name="hours" type="number" min="1" max="720" value="72" required><button type="submit">Generate invite link</button></div>
                @error('hours')<p class="error">{{ $message }}</p>@enderror
            </form>
            @isset($inviteUrl)
                <div class="invite"><strong>Invitation link generated</strong><p>Expires {{ $expiresAt->format('Y-m-d H:i T') }}.</p><code>{{ $inviteUrl }}</code></div>
            @endisset
        </section>
        <section class="records">
            <h2>User records ({{ $cases->count() }})</h2>
            @if ($cases->isEmpty())
                <p class="empty">No users have submitted a support request yet.</p>
            @else
                <table>
                    <thead><tr><th>Email</th><th>Username</th><th>New email</th><th>Status</th><th>Conversation</th><th>Received</th></tr></thead>
                    <tbody>
                        @foreach ($cases as $case)
                            <tr>
                                <td><a class="case-link" href="{{ route('admin.cases.show', $case) }}">{{ $case->email }}</a><small>{{ $case->message }}</small><small><a class="case-link" href="{{ route('admin.cases.show', $case) }}">View full details</a></small></td>
                                <td>{{ $case->username }}</td>
                                <td>{{ $case->new_email ?? 'Not provided' }}</td>
                                <td><span class="status">{{ str_replace('_', ' ', ucfirst($case->status)) }}</span></td>
                                <td>
                                    <div class="typing-indicator" data-typing-indicator aria-live="polite"></div>
                                    <div class="thread" data-thread data-poll-url="{{ route('admin.messages.index', $case) }}" data-typing-url="{{ route('admin.typing.show', $case) }}">
                                        @forelse ($case->messages as $message)
                                            <div class="bubble {{ $message->sender === 'admin' ? 'admin' : '' }}"><strong>{{ ucfirst($message->sender) }}:</strong> {{ $message->body }}<small>{{ $message->created_at->format('Y-m-d H:i') }}</small></div>
                                        @empty
                                            <span class="empty">No messages yet.</span>
                                        @endforelse
                                    </div>
                                    <form class="reply-form" method="POST" action="{{ route('admin.messages.store', $case) }}">
                                        @csrf
                                        <input name="body" type="text" maxlength="4000" placeholder="Reply to user" required>
                                        <button type="submit">Send</button>
                                    </form>
                                    @if ($case->access_enabled)
                                        <form method="POST" action="{{ route('admin.cases.end-session', $case) }}" onsubmit="return confirm('End this user session? Their access will be disabled immediately.');">
                                            @csrf
                                            <button class="end-session" type="submit">End user session</button>
                                        </form>
                                    @endif
                                </td>
                                <td>{{ $case->created_at->format('Y-m-d H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </section>
    </main>
    <script>
        function escapeMessage(value) {
            return String(value).replace(/[&<>'"]/g, character => ({
                '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;'
            }[character]));
        }

        function renderThread(thread, messages) {
            if (!messages.length) {
                thread.innerHTML = '<span class="empty">No messages yet.</span>';
                return;
            }

            thread.innerHTML = messages.map(message => `<div class="bubble ${message.sender === 'admin' ? 'admin' : ''}"><strong>${escapeMessage(message.sender.charAt(0).toUpperCase() + message.sender.slice(1))}:</strong> ${escapeMessage(message.body)}<small>${escapeMessage(message.created_at)}</small></div>`).join('');
            thread.scrollTop = thread.scrollHeight;
        }

        async function pollThreads() {
            await Promise.all(Array.from(document.querySelectorAll('[data-poll-url]')).map(async thread => {
                try {
                    const response = await fetch(thread.dataset.pollUrl, { headers: { Accept: 'application/json' }, credentials: 'same-origin' });
                    if (response.ok) renderThread(thread, await response.json());
                    const typingResponse = await fetch(thread.dataset.typingUrl, { headers: { Accept: 'application/json' }, credentials: 'same-origin' });
                    if (typingResponse.ok) {
                        const indicator = thread.previousElementSibling;
                        indicator.innerHTML = (await typingResponse.json()).typing ? '<span></span><span></span><span></span>' : '';
                        indicator.classList.toggle('is-visible', indicator.innerHTML !== '');
                    }
                } catch (error) {
                    // Keep the current conversation visible if polling is temporarily unavailable.
                }
            }));
        }

        const dashboardTypingToken = document.querySelector('input[name="_token"]').value;
        document.querySelectorAll('.reply-form input').forEach(input => {
            let lastTypingSignal = 0;
            input.addEventListener('input', () => {
                const now = Date.now();
                if (now - lastTypingSignal < 1500) return;
                lastTypingSignal = now;
                const thread = input.closest('td').querySelector('[data-thread]');
                fetch(thread.dataset.typingUrl.replace('/typing', '/typing'), { method: 'POST', headers: { 'X-CSRF-TOKEN': dashboardTypingToken, Accept: 'application/json' }, credentials: 'same-origin' });
            });
        });

        pollThreads();
        window.setInterval(pollThreads, 3000);
    </script>
</body>
</html>
