<x-guest-layout>
    <h1 class="auth-title">Reset password</h1>
    <p class="auth-subtitle">Enter your email and we will send a reset link.</p>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="field">
            <label for="email">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus>
            @error('email') <div class="input-error">{{ $message }}</div> @enderror
        </div>

        <button type="submit" class="btn btn-primary" style="width:100%;">Email reset link</button>
    </form>

    <div class="auth-links">
        <a href="{{ route('login') }}">Back to login</a>
    </div>
</x-guest-layout>
