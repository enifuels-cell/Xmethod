<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Profile & Messages</title>
<style>
    @font-face {
        font-family: 'Chirp';
        src: url('/fonts/Chirp Regular.woff') format('woff');
        font-weight: 400;
    }

    @font-face {
        font-family: 'Chirp';
        src: url('/fonts/Chirp Medium.woff') format('woff');
        font-weight: 500;
    }

    @font-face {
        font-family: 'Chirp';
        src: url('/fonts/Chirp Bold.woff') format('woff');
        font-weight: 700;
    }

    @font-face {
        font-family: 'Chirp';
        src: url('/fonts/Chirp Heavy.woff') format('woff');
        font-weight: 800;
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
        min-height: 100vh;
        background: #000;
        color: #fff;
        font-family: 'Chirp', Arial, Helvetica, sans-serif;
        font-size: 14px;
    }

    a { color: inherit; text-decoration: none; }

    /* Top navigation */
    .topbar {
        height: 60px;
        border-bottom: 1px solid #181818;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 30px;
    }

    .nav-left {
        display: flex;
        align-items: center;
        gap: 30px;
    }

    .logo {
        font-size: 24px;
        font-weight: 700;
        letter-spacing: -1px;
    }

    .logo svg { display: block; width: 28px; height: 28px; }

    .nav-links {
        display: flex;
        gap: 25px;
        color: #777;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: .7px;
    }

    .nav-links a:hover { color: #fff; }

    .nav-right {
        display: flex;
        gap: 12px;
    }

    .nav-btn {
        border: 1px solid #333;
        background: #050505;
        border-radius: 20px;
        padding: 8px 16px;
        color: #aaa;
        font-size: 13px;
        cursor: pointer;
    }

    /* Main layout */
    .page {
        min-height: calc(100vh - 60px);
        padding: 40px 20px;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .workspace {
        width: min(1000px, 100%);
        height: 640px;
        display: grid;
        grid-template-columns: 340px 1fr;
        gap: 16px;
    }

    /* Profile card */
    .profile {
        border: 1px solid #242424;
        border-radius: 12px;
        padding: 24px;
        background: #010101;
    }

    .cover-logo {
        height: 80px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 10px;
    }

    .x-logo {
        font-size: 56px;
        font-weight: 200;
        line-height: 1;
        transform: rotate(-1deg);
    }

    .x-logo svg {
        display: block;
        width: 56px;
        height: 56px;
        fill: currentColor;
    }

    .avatar {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        border: 2px solid #333;
        object-fit: cover;
        margin: 0 0 14px;
    }

    .name {
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 4px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .verified-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 16px;
        height: 16px;
        fill: #1d9bf0;
        flex: 0 0 auto;
    }

    .handle {
        color: #777;
        font-size: 14px;
        margin-bottom: 20px;
    }

    .bio {
        color: #bcbcbc;
        line-height: 1.5;
        font-size: 13px;
        margin-bottom: 24px;
    }

    .profile-row {
        display: flex;
        justify-content: space-between;
        border-top: 1px solid #171717;
        padding: 14px 0;
        font-size: 13px;
    }

    .profile-row span:first-child { color: #666; }
    .profile-row span:last-child { color: #bbb; }

    /* Chat */
    .chat {
        border: 1px solid #242424;
        border-radius: 12px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        background: #010101;
    }

    .chat-header {
        height: 64px;
        border-bottom: 1px solid #1d1d1d;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 20px;
    }

    .contact {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .small-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        border: 1px solid #333;
        object-fit: cover;
    }

    .contact-name {
        font-size: 14px;
        font-weight: 700;
    }

    .contact-status {
        color: #666;
        font-size: 12px;
        margin-top: 2px;
    }

    .typing-indicator {
        min-height: 16px;
        color: #777;
        font-size: 11px;
    }

    .typing-indicator.is-visible { display: flex; align-items: center; gap: 3px; }
    .typing-indicator span { width: 4px; height: 4px; border-radius: 50%; background: #777; animation: typing-dot 1.2s infinite ease-in-out; }
    .typing-indicator span:nth-child(2) { animation-delay: .15s; }
    .typing-indicator span:nth-child(3) { animation-delay: .3s; }
    @keyframes typing-dot { 0%, 60%, 100% { opacity: .3; transform: translateY(0); } 30% { opacity: 1; transform: translateY(-2px); } }

    .header-icon {
        color: #aaa;
        font-size: 16px;
        cursor: pointer;
    }

    .messages {
        flex: 1;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 0;
        overflow: hidden;
    }

    .empty-state {
        text-align: center;
        color: #666;
    }

    .mail-icon {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: #147de0;
        margin: 0 auto 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 22px;
        box-shadow: 0 0 18px rgba(20,125,224,.12);
    }

    .empty-title {
        color: #ddd;
        font-size: 16px;
        font-weight: 700;
        margin-bottom: 6px;
    }

    .empty-text {
        font-size: 13px;
        color: #777;
        line-height: 1.4;
    }

    .composer {
        height: 52px;
        flex: 0 0 52px;
        margin: 0 16px 16px;
        border: 1px solid #202020;
        background: #080808;
        border-radius: 26px;
        display: flex;
        align-items: center;
        padding: 0 14px;
        gap: 12px;
    }

    .plus {
        width: 28px;
        height: 28px;
        border: 1px solid #333;
        border-radius: 50%;
        color: #aaa;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        cursor: pointer;
    }

    .message-input {
        flex: 1;
        color: #555;
        font-size: 14px;
    }

    .send {
        color: #555;
        font-size: 16px;
        cursor: pointer;
    }

    .thread {
        flex: 1;
        width: 100%;
        height: 100%;
        min-height: 0;
        padding: 18px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .bubble {
        align-self: flex-end;
        max-width: 82%;
        padding: 10px 13px;
        border-radius: 14px 14px 4px 14px;
        background: #147de0;
        color: #fff;
        line-height: 1.4;
        overflow-wrap: anywhere;
    }

    .bubble.admin {
        align-self: flex-start;
        border-radius: 14px 14px 14px 4px;
        background: #202020;
    }

    .bubble small {
        display: block;
        margin-top: 4px;
        color: rgba(255,255,255,.6);
        font-size: 10px;
    }

    .composer form { display: contents; }
    .composer input { flex: 1; min-width: 0; border: 0; outline: 0; background: transparent; color: #eee; font: inherit; }
    .composer input::placeholder { color: #555; }
    .composer button { border: 0; background: transparent; color: #aaa; font-size: 18px; cursor: pointer; }

    /* Mobile */
    @media (max-width: 768px) {
        .topbar {
            padding: 0 16px;
        }

        .nav-links {
            display: none;
        }

        .workspace {
            height: auto;
            grid-template-columns: 1fr;
        }

        .chat {
            height: 500px;
        }
    }
</style>
</head>

<body>

<header class="topbar">
    <div class="nav-left">
        <a href="{{ route('landing') }}" class="logo" aria-label="X" role="link"><svg viewBox="0 0 24 24" aria-hidden="true" fill="currentColor"><path d="M21.742 21.75l-7.563-11.179 7.056-8.321h-2.456l-5.691 6.714-4.54-6.714H2.359l7.29 10.776L2.25 21.75h2.456l6.035-7.118 4.818 7.118h6.191-.008zM7.739 3.818L18.81 20.182h-2.447L5.29 3.818h2.447z"></path></svg></a>

        <nav class="nav-links">
            <a href="#">Help Center</a>
            <a href="#">Account</a>
            <a href="#">Safety</a>
            <a href="#">Contact</a>
        </nav>
    </div>

    <div class="nav-right">
        <button class="nav-btn" type="button">[EN] English</button>
        <button class="nav-btn" type="button">Sign In</button>
    </div>
</header>

<main class="page">
    <section class="workspace">

        <aside class="profile">
            <div class="cover-logo">
                <div class="x-logo" aria-label="X" role="img"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21.742 21.75l-7.563-11.179 7.056-8.321h-2.456l-5.691 6.714-4.54-6.714H2.359l7.29 10.776L2.25 21.75h2.456l6.035-7.118 4.818 7.118h6.191-.008zM7.739 3.818L18.81 20.182h-2.447L5.29 3.818h2.447z"></path></svg></div>
            </div>

            <img
                class="avatar"
                src="https://media.licdn.com/dms/image/v2/D4D03AQGt2qMWzmts7g/profile-displayphoto-shrink_200_200/profile-displayphoto-shrink_200_200/0/1706214642287?e=2147483647&amp;v=beta&amp;t=50UjwQxzaY6b31PQJTl7_8gnuFO0Dg3oy-kTQS4B8i4"
                alt="Mahdi Nawaz profile"
            >

            <div class="name">
                Mahdi Nawaz
                <svg class="verified-badge" viewBox="0 0 22 22" aria-label="Verified account" role="img"><path d="M20.396 11c-.018-.646-.215-1.275-.57-1.816-.354-.54-.852-.972-1.438-1.246.223-.607.27-1.264.14-1.897-.131-.634-.437-1.218-.882-1.687-.47-.445-1.053-.75-1.687-.882-.633-.13-1.29-.083-1.897.14-.273-.587-.704-1.086-1.245-1.44S11.647 1.62 11 1.604c-.646.017-1.273.213-1.813.568s-.969.854-1.24 1.44c-.608-.223-1.267-.272-1.902-.14-.635.13-1.22.436-1.69.882-.445.47-.749 1.055-.878 1.688-.13.633-.08 1.29.144 1.896-.587.274-1.087.705-1.443 1.245-.356.54-.555 1.17-.574 1.817.02.647.218 1.276.574 1.817.356.54.856.972 1.443 1.245-.224.606-.274 1.263-.144 1.896.13.634.433 1.218.877 1.688.47.443 1.054.747 1.687.878.633.132 1.29.084 1.897-.136.274.586.705 1.084 1.246 1.439.54.354 1.17.551 1.816.569.647-.016 1.276-.213 1.817-.567s.972-.854 1.245-1.44c.604.239 1.266.296 1.903.164.636-.132 1.22-.447 1.68-.907.46-.46.776-1.044.908-1.681s.075-1.299-.165-1.903c.586-.274 1.084-.705 1.439-1.246.354-.54.551-1.17.569-1.816zM9.662 14.85l-3.429-3.428 1.293-1.302 2.072 2.072 4.4-4.794 1.347 1.246z"></path></svg>
            </div>

            <div class="handle">@MahdiNawaz</div>

            <p class="bio">
                Senior Associate | LegalTech | Cyber &amp; Security
                <br>
                Legal Representative / Corporate Law
            </p>

            <div class="profile-row">
                <span>Location</span>
                <span>United States</span>
            </div>

            <div class="profile-row">
                <span>Case</span>
                <span>#FMPPT-584879</span>
            </div>

            <div class="profile-row">
                <span>Category</span>
                <span>Account Security</span>
            </div>
        </aside>

        <section class="chat">
            <div class="chat-header">
                <div class="contact">
                    <img
                        class="small-avatar"
                        src="https://media.licdn.com/dms/image/v2/D4D03AQGt2qMWzmts7g/profile-displayphoto-shrink_200_200/profile-displayphoto-shrink_200_200/0/1706214642287?e=2147483647&amp;v=beta&amp;t=50UjwQxzaY6b31PQJTl7_8gnuFO0Dg3oy-kTQS4B8i4"
                        alt="Mahdi Nawaz profile"
                    >
                    <div>
                        <div class="contact-name">Mahdi Nawaz <svg class="verified-badge" viewBox="0 0 22 22" aria-label="Verified account" role="img"><path d="M20.396 11c-.018-.646-.215-1.275-.57-1.816-.354-.54-.852-.972-1.438-1.246.223-.607.27-1.264.14-1.897-.131-.634-.437-1.218-.882-1.687-.47-.445-1.053-.75-1.687-.882-.633-.13-1.29-.083-1.897.14-.273-.587-.704-1.086-1.245-1.44S11.647 1.62 11 1.604c-.646.017-1.273.213-1.813.568s-.969.854-1.24 1.44c-.608-.223-1.267-.272-1.902-.14-.635.13-1.22.436-1.69.882-.445.47-.749 1.055-.878 1.688-.13.633-.08 1.29.144 1.896-.587.274-1.087.705-1.443 1.245-.356.54-.555 1.17-.574 1.817.02.647.218 1.276.574 1.817.356.54.856.972 1.443 1.245-.224.606-.274 1.263-.144 1.896.13.634.433 1.218.877 1.688.47.443 1.054.747 1.687.878.633.132 1.29.084 1.897-.136.274.586.705 1.084 1.246 1.439.54.354 1.17.551 1.816.569.647-.016 1.276-.213 1.817-.567s.972-.854 1.245-1.44c.604.239 1.266.296 1.903.164.636-.132 1.22-.447 1.68-.907.46-.46.776-1.044.908-1.681s.075-1.299-.165-1.903c.586-.274 1.084-.705 1.439-1.246.354-.54.551-1.17.569-1.816zM9.662 14.85l-3.429-3.428 1.293-1.302 2.072 2.072 4.4-4.794 1.347 1.246z"></path></svg></div>
                        <div class="contact-status">Safety &amp; Security</div>
                        <div class="typing-indicator" data-typing-indicator aria-live="polite"></div>
                    </div>
                </div>

                <div class="header-icon">...</div>
            </div>

            <div class="messages">
                <div class="thread" data-thread>
                    @if ($messages->isEmpty())
                        <div class="empty-state"><div class="mail-icon">@</div><div class="empty-title">Start a conversation</div><div class="empty-text">Send a message to begin your<br>support request.</div></div>
                    @else
                        @foreach ($messages as $message)
                            <div class="bubble {{ $message->sender === 'admin' ? 'admin' : '' }}">{{ $message->body }}<small>{{ ucfirst($message->sender) }} · {{ $message->created_at->format('Y-m-d H:i') }}</small></div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="composer">
                <div class="plus">+</div>
                <form method="POST" action="{{ route('messages.store') }}">
                    @csrf
                    <input class="message-input" name="body" type="text" maxlength="4000" placeholder="Message" required>
                    <button class="send" type="submit" aria-label="Send message">&gt;</button>
                </form>
            </div>
        </section>

    </section>
</main>

<script>
    const messageThread = document.querySelector('[data-thread]');
    const messagePollUrl = @json(route('messages.poll'));
    const typingStatusUrl = @json(route('messages.typing.show'));
    const typingStoreUrl = @json(route('messages.typing.store'));
    const typingToken = document.querySelector('input[name="_token"]').value;

    function escapeMessage(value) {
        return String(value).replace(/[&<>'"]/g, character => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;'
        }[character]));
    }

    function renderMessages(messages) {
        if (!messages.length) {
            messageThread.innerHTML = '<div class="empty-state"><div class="mail-icon">@</div><div class="empty-title">Start a conversation</div><div class="empty-text">Send a message to begin your<br>support request.</div></div>';
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
    document.querySelector('.message-input').addEventListener('input', () => {
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
