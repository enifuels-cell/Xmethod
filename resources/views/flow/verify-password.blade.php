<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify your password</title>
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

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html,
        body {
            min-height: 100%;
            background: #000;
            color: #f5f5f5;
            font-family: 'Chirp', Arial, Helvetica, sans-serif;
        }

        body {
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 24px;
        }

        .verification-modal {
            width: min(420px, 90vw);
            min-height: 280px;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 30px;
            background: #000;
            border: 1px solid #222;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.8);
        }

        .logo {
            margin-bottom: 20px;
            color: #aaa;
            font-size: 28px;
            font-weight: 700;
        }

        .logo svg { display: block; width: 28px; height: 28px; }

        h1 {
            align-self: flex-start;
            margin: 0 0 8px;
            font-size: 24px;
            line-height: 1.2;
            font-weight: 700;
        }

        .description {
            align-self: flex-start;
            margin: 0 0 24px;
            color: #777;
            font-size: 15px;
            line-height: 1.5;
        }

        .password {
            width: 100%;
            height: 48px;
            margin-bottom: auto;
            padding: 0 16px;
            border: 1px solid #292929;
            border-radius: 8px;
            outline: none;
            background: #050505;
            color: #f5f5f5;
            font-size: 15px;
            transition: border-color 0.2s ease;
        }

        .password:focus {
            border-color: #666;
        }

        .password::placeholder {
            color: #555;
        }

        .actions {
            width: 100%;
            display: flex;
            gap: 12px;
            margin-top: 30px;
        }

        button {
            flex: 1;
            height: 48px;
            border-radius: 24px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s ease, border-color 0.2s ease;
        }

        .cancel {
            border: 1px solid #292929;
            background: transparent;
            color: #eee;
        }

        .continue {
            border: 0;
            background: #f1f2f3;
            color: #111;
        }

        .cancel:hover {
            border-color: #666;
        }

        .continue:hover {
            background: #fff;
        }

        .continue.is-loading {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: wait;
        }

        .spinner {
            width: 14px;
            height: 14px;
            border: 2px solid #aaa;
            border-top-color: #111;
            border-radius: 50%;
            animation: spin .8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        @media (max-width: 500px) {
            .verification-modal {
                width: min(420px, 92vw);
                padding: 24px 20px;
            }
        }
    </style>
</head>
<body>
    <main class="verification-modal" aria-labelledby="title">
        <a href="{{ route('landing') }}" class="logo" aria-label="X" role="link"><svg viewBox="0 0 24 24" aria-hidden="true" fill="currentColor"><path d="M21.742 21.75l-7.563-11.179 7.056-8.321h-2.456l-5.691 6.714-4.54-6.714H2.359l7.29 10.776L2.25 21.75h2.456l6.035-7.118 4.818 7.118h6.191-.008zM7.739 3.818L18.81 20.182h-2.447L5.29 3.818h2.447z"></path></svg></a>
        <h1 id="title">Confirm your session</h1>
        <p class="description">Confirm this support session to continue. We will never ask you to enter or share your account password here.</p>

        <form method="POST" action="{{ route('password.verify') }}">
            @csrf
        <div class="actions">
            <button class="cancel" type="button" onclick="history.back()">Cancel</button>
            <button class="continue" type="submit">Confirm session</button>
        </div>
        </form>
    </main>

</body>
</html>
