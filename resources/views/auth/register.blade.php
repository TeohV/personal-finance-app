<x-guest-layout>
    <h1 class="auth-title">Create account</h1>
    <p class="auth-subtitle">Use a strong password to protect your financial records.</p>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="field">
            <label for="name">Name</label>
            <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus autocomplete="name">
            @error('name') <div class="input-error">{{ $message }}</div> @enderror
        </div>

        <div class="field">
            <label for="email">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="username">
            @error('email') <div class="input-error">{{ $message }}</div> @enderror
        </div>

        <div class="field">
            <label for="password">Password</label>
            <input id="password" name="password" type="password" required autocomplete="new-password">
            <div class="text-muted" style="font-size:13px; margin-top:6px;">
                Minimum 8 characters with uppercase, lowercase, number, and symbol.
            </div>
            @error('password') <div class="input-error">{{ $message }}</div> @enderror
        </div>

        <div class="field">
            <label for="password_confirmation">Confirm password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password">
            @error('password_confirmation') <div class="input-error">{{ $message }}</div> @enderror
        </div>

        <button type="submit" class="btn btn-primary" style="width:100%;">Register</button>
    </form>

    <div class="auth-links">
        Already registered? <a href="{{ route('login') }}">Log in</a>
    </div>
</x-guest-layout>
