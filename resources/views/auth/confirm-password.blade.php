<x-guest-layout>
    <h1 class="auth-title">Confirm password</h1>
    <p class="auth-subtitle">Please confirm your password before continuing.</p>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <div class="field">
            <label for="password">Password</label>
            <input id="password" name="password" type="password" required autocomplete="current-password">
            @error('password') <div class="input-error">{{ $message }}</div> @enderror
        </div>

        <button type="submit" class="btn btn-primary" style="width:100%;">Confirm</button>
    </form>
</x-guest-layout>
