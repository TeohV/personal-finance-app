<x-guest-layout>
    <h1 class="auth-title">Log in</h1>
    <p class="auth-subtitle">Welcome back. Continue managing your finances.</p>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="field">
            <label for="email">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username">
            @error('email') <div class="input-error">{{ $message }}</div> @enderror
        </div>

        <div class="field">
            <label for="password">Password</label>
            <input id="password" name="password" type="password" required autocomplete="current-password">
            @error('password') <div class="input-error">{{ $message }}</div> @enderror
        </div>

        <div class="field">
            <label style="display:flex; align-items:center; gap:8px; font-weight:400;">
                <input type="checkbox" name="remember" style="width:auto;">
                Remember me
            </label>
        </div>

        <button type="submit" class="btn btn-primary" style="width:100%;">Log in</button>
    </form>

    <div class="auth-links">
        @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}">Forgot password?</a>
        @endif
        @if (Route::has('register'))
            <br>New here? <a href="{{ route('register') }}">Create an account</a>
        @endif
    </div>
</x-guest-layout>
