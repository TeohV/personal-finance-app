@extends('layouts.app')

@section('title', 'New Expense')

@section('content')
    <div class="page-header">
        <div>
            <h2 class="page-title">New expense</h2>
            <p class="page-subtitle">Record money spent from one of your expense categories.</p>
        </div>
        <a href="{{ route('expenses.index') }}" class="btn">Back</a>
    </div>

    <section class="form-card" style="max-width: 720px;">
        @if($categories->isEmpty())
            <div class="alert alert-error">Create an expense category first before adding expenses.</div>
        @endif
        @if($accounts->isEmpty())
            <div class="alert alert-error">Create an account first so the expense has a source.</div>
        @endif

        <form action="{{ route('expenses.store') }}" method="POST">
            @csrf

            <div class="field">
                <label for="description">Description</label>
                <input id="description" name="description" type="text" value="{{ old('description') }}" placeholder="Example: Grocery shopping" autofocus>
                @error('description') <div class="input-error">{{ $message }}</div> @enderror
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
                    <label for="date">Date</label>
                    <input id="date" name="date" type="date" value="{{ old('date', now()->toDateString()) }}">
                    @error('date') <div class="input-error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="field">
                <label for="notes">Notes</label>
                <textarea id="notes" name="notes" placeholder="Optional">{{ old('notes') }}</textarea>
                @error('notes') <div class="input-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-expense" @disabled($categories->isEmpty() || $accounts->isEmpty())>Save expense</button>
                <a href="{{ route('expenses.index') }}" class="btn">Cancel</a>
            </div>
        </form>
    </section>
@endsection
