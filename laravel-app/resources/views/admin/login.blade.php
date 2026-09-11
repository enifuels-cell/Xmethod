<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin sign in</title>
    <style>
        * { box-sizing: border-box; }
        body { min-height: 100vh; margin: 0; display: grid; place-items: center; padding: 24px; background: #050505; color: #f5f5f5; font-family: Arial, sans-serif; }
        .panel { width: min(420px, 100%); padding: 32px; border: 1px solid #292929; border-radius: 12px; background: #0d0d0d; }
        h1 { margin: 0 0 8px; font-size: 26px; }
        p { color: #888; line-height: 1.5; }
        label { display: block; margin: 24px 0 8px; color: #aaa; font-size: 14px; }
        input { width: 100%; height: 46px; padding: 0 14px; border: 1px solid #333; border-radius: 6px; background: #050505; color: #fff; font-size: 15px; }
        button { width: 100%; height: 46px; margin-top: 18px; border: 0; border-radius: 23px; background: #f2f2f2; color: #111; font-weight: 700; cursor: pointer; }
        .error { color: #ff8d8d; font-size: 13px; }
    </style>
</head>
<body>
    <main class="panel">
        <h1>Admin access</h1>
        <p>Sign in to generate invitation links.</p>
        <form method="POST" action="{{ route('admin.authenticate') }}">
            @csrf
            <label for="access_key">Admin access key</label>
            <input id="access_key" name="access_key" type="password" autocomplete="current-password" required autofocus>
            @error('access_key')<p class="error">{{ $message }}</p>@enderror
            <button type="submit">Open dashboard</button>
        </form>
    </main>
</body>
</html>
