<x-guest-layout>
    <h1 class="auth-title">Verify email</h1>
    <p class="auth-subtitle">Check your email for a verification link before continuing.</p>

    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success">A new verification link has been sent.</div>
    @endif

    <div class="form-actions">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn btn-primary">Resend email</button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn">Log out</button>
        </form>
    </div>
</x-guest-layout>
