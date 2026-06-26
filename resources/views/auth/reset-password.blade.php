<x-guest-layout>
    <h1 class="auth-title">Choose new password</h1>
    <p class="auth-subtitle">Enter your email and new password.</p>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="field">
            <label for="email">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username">
            @error('email') <div class="input-error">{{ $message }}</div> @enderror
        </div>

        <div class="field">
            <label for="password">Password</label>
            <input id="password" name="password" type="password" required autocomplete="new-password">
            @error('password') <div class="input-error">{{ $message }}</div> @enderror
        </div>

        <div class="field">
            <label for="password_confirmation">Confirm password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password">
            @error('password_confirmation') <div class="input-error">{{ $message }}</div> @enderror
        </div>

        <button type="submit" class="btn btn-primary" style="width:100%;">Reset password</button>
    </form>
</x-guest-layout>
