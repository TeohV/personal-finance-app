@extends('layouts.app')

@section('title', 'New Account')

@section('content')
    <div class="page-header">
        <div>
            <h2 class="page-title">New account</h2>
            <p class="page-subtitle">Create a place where money can be held, such as cash, bank, e-wallet, or savings.</p>
        </div>
        <a href="{{ route('accounts.index') }}" class="btn">Back</a>
    </div>

    <section class="form-card" style="max-width: 720px;">
        <form method="POST" action="{{ route('accounts.store') }}">
            @csrf

            <div class="field">
                <label for="name">Account name</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" placeholder="Example: Maybank savings" autofocus>
                @error('name') <div class="input-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-grid">
                <div class="field">
                    <label for="type">Type</label>
                    <select id="type" name="type">
                        @foreach($types as $value => $label)
                            <option value="{{ $value }}" @selected(old('type', 'bank') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('type') <div class="input-error">{{ $message }}</div> @enderror
                </div>

                <div class="field">
                    <label for="opening_balance">Opening balance (RM)</label>
                    <input id="opening_balance" name="opening_balance" type="number" min="0" step="0.01" value="{{ old('opening_balance', '0.00') }}">
                    @error('opening_balance') <div class="input-error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save account</button>
                <a href="{{ route('accounts.index') }}" class="btn">Cancel</a>
            </div>
        </form>
    </section>
@endsection
