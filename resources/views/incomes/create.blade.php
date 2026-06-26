@extends('layouts.app')

@section('title', 'New Income')

@section('content')
    <div class="page-header">
        <div>
            <h2 class="page-title">New income</h2>
            <p class="page-subtitle">Record salary, freelance work, allowance, or other money received.</p>
        </div>
        <a href="{{ route('incomes.index') }}" class="btn">Back</a>
    </div>

    <section class="form-card" style="max-width: 720px;">
        @if($categories->isEmpty())
            <div class="alert alert-error">Create an income category first before adding income.</div>
        @endif
        @if($accounts->isEmpty())
            <div class="alert alert-error">Create an account first so the income has somewhere to go.</div>
        @endif

        <form action="{{ route('incomes.store') }}" method="POST">
            @csrf

            <div class="field">
                <label for="source">Source</label>
                <input id="source" name="source" type="text" value="{{ old('source') }}" placeholder="Example: Part-time job" autofocus>
                @error('source') <div class="input-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-grid">
                <div class="field">
                    <label for="amount">Amount (RM)</label>
                    <input id="amount" name="amount" type="number" min="0.01" step="0.01" value="{{ old('amount') }}">
                    @error('amount') <div class="input-error">{{ $message }}</div> @enderror
                </div>

                <div class="field">
                    <label for="category_id">Category</label>
                    <select id="category_id" name="category_id">
                        <option value="">Select category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <div class="input-error">{{ $message }}</div> @enderror
                </div>

                <div class="field">
                    <label for="account_id">Account</label>
                    <select id="account_id" name="account_id">
                        <option value="">Select account</option>
                        @foreach ($accounts as $account)
                            <option value="{{ $account->id }}" @selected(old('account_id') == $account->id)>{{ $account->name }}</option>
                        @endforeach
                    </select>
                    @error('account_id') <div class="input-error">{{ $message }}</div> @enderror
                </div>

                <div class="field">
                    <label for="income_date">Date</label>
                    <input id="income_date" name="income_date" type="date" value="{{ old('income_date', now()->toDateString()) }}">
                    @error('income_date') <div class="input-error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-income" @disabled($categories->isEmpty() || $accounts->isEmpty())>Save income</button>
                <a href="{{ route('incomes.index') }}" class="btn">Cancel</a>
            </div>
        </form>
    </section>
@endsection
