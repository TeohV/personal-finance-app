@extends('layouts.app')

@section('title', 'Edit Account')

@section('content')
    <div class="page-header">
        <div>
            <h2 class="page-title">Edit account</h2>
            <p class="page-subtitle">Update this account's name, type, opening balance, or active status.</p>
        </div>
        <a href="{{ route('accounts.index') }}" class="btn">Back</a>
    </div>

    <section class="form-card" style="max-width: 720px;">
        <form method="POST" action="{{ route('accounts.update', $account) }}">
            @csrf
            @method('PUT')

            <div class="field">
                <label for="name">Account name</label>
                <input id="name" name="name" type="text" value="{{ old('name', $account->name) }}" autofocus>
                @error('name') <div class="input-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-grid">
                <div class="field">
                    <label for="type">Type</label>
                    <select id="type" name="type">
                        @foreach($types as $value => $label)
                            <option value="{{ $value }}" @selected(old('type', $account->type) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('type') <div class="input-error">{{ $message }}</div> @enderror
                </div>

                <div class="field">
                    <label for="opening_balance">Opening balance (RM)</label>
                    <input id="opening_balance" name="opening_balance" type="number" min="0" step="0.01" value="{{ old('opening_balance', $account->opening_balance) }}">
                    @error('opening_balance') <div class="input-error">{{ $message }}</div> @enderror
                </div>

                <div class="field">
                    <label for="is_active">Status</label>
                    <select id="is_active" name="is_active">
                        <option value="1" @selected(old('is_active', $account->is_active) == 1)>Active</option>
                        <option value="0" @selected(old('is_active', $account->is_active) == 0)>Archived</option>
                    </select>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save changes</button>
                <a href="{{ route('accounts.index') }}" class="btn">Cancel</a>
            </div>
        </form>

        <div class="danger-zone">
            <p>{{ $account->hasActivity() ? 'Archive this account so past records stay intact.' : 'Remove this unused account.' }}</p>
            <form method="POST" action="{{ route('accounts.destroy', $account) }}" onsubmit="return confirm('Archive or delete this account?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">{{ $account->hasActivity() ? 'Archive' : 'Delete' }}</button>
            </form>
        </div>
    </section>
@endsection
