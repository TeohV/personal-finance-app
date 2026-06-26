@extends('layouts.app')

@section('title', 'Profile')

@section('content')
    <div class="page-header">
        <div>
            <h2 class="page-title">Profile</h2>
            <p class="page-subtitle">Update your account details and password.</p>
        </div>
    </div>

    @if (session('status') === 'profile-updated')
        <div class="alert alert-success">Profile updated.</div>
    @endif

    @if (session('status') === 'password-updated')
        <div class="alert alert-success">Password updated.</div>
    @endif

    <div class="grid-2">
        <section class="form-card">
            <h3 class="card-title">Account information</h3>
            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('PATCH')

                <div class="field">
                    <label for="name">Name</label>
                    <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required>
                    @error('name') <div class="input-error">{{ $message }}</div> @enderror
                </div>

                <div class="field">
                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required>
                    @error('email') <div class="input-error">{{ $message }}</div> @enderror
                </div>

                <button type="submit" class="btn btn-primary">Save profile</button>
            </form>
        </section>

        <section class="form-card">
            <h3 class="card-title">Change password</h3>
            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                @method('PUT')

                <div class="field">
                    <label for="current_password">Current password</label>
                    <input id="current_password" name="current_password" type="password" autocomplete="current-password">
                    @foreach($errors->updatePassword->get('current_password') as $message)
                        <div class="input-error">{{ $message }}</div>
                    @endforeach
                </div>

                <div class="field">
                    <label for="password">New password</label>
                    <input id="password" name="password" type="password" autocomplete="new-password">
                    @foreach($errors->updatePassword->get('password') as $message)
                        <div class="input-error">{{ $message }}</div>
                    @endforeach
                </div>

                <div class="field">
                    <label for="password_confirmation">Confirm new password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password">
                    @foreach($errors->updatePassword->get('password_confirmation') as $message)
                        <div class="input-error">{{ $message }}</div>
                    @endforeach
                </div>

                <button type="submit" class="btn btn-primary">Update password</button>
            </form>
        </section>
    </div>

    <section class="form-card" style="margin-top: 18px;">
        <h3 class="card-title">Delete account</h3>
        <p class="page-subtitle" style="margin-bottom: 16px;">
            This permanently removes your account and related finance records.
        </p>

        <form method="POST" action="{{ route('profile.destroy') }}" onsubmit="return confirm('Delete your account? This cannot be undone.')">
            @csrf
            @method('DELETE')

            <div class="field" style="max-width: 420px;">
                <label for="delete_password">Password</label>
                <input id="delete_password" name="password" type="password" autocomplete="current-password">
                @foreach($errors->userDeletion->get('password') as $message)
                    <div class="input-error">{{ $message }}</div>
                @endforeach
            </div>

            <button type="submit" class="btn btn-danger">Delete account</button>
        </form>
    </section>
@endsection
