@extends('layouts.app')

@section('title', 'Edit Expense')

@section('content')
    <div class="page-header">
        <div>
            <h2 class="page-title">Edit expense</h2>
            <p class="page-subtitle">Update this spending record.</p>
        </div>
        <a href="{{ route('expenses.index') }}" class="btn">Back</a>
    </div>

    <section class="form-card" style="max-width: 720px;">
        <form action="{{ route('expenses.update', $expense) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="field">
                <label for="description">Description</label>
                <input id="description" name="description" type="text" value="{{ old('description', $expense->description) }}" autofocus>
                @error('description') <div class="input-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-grid">
                <div class="field">
                    <label for="amount">Amount (RM)</label>
                    <input id="amount" name="amount" type="number" min="0.01" step="0.01" value="{{ old('amount', $expense->amount) }}">
                    @error('amount') <div class="input-error">{{ $message }}</div> @enderror
                </div>

                <div class="field">
                    <label for="category_id">Category</label>
                    <select id="category_id" name="category_id">
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id', $expense->category_id) == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <div class="input-error">{{ $message }}</div> @enderror
                </div>

                <div class="field">
                    <label for="account_id">Account</label>
                    <select id="account_id" name="account_id">
                        @foreach ($accounts as $account)
                            <option value="{{ $account->id }}" @selected(old('account_id', $expense->account_id) == $account->id)>
                                {{ $account->name }}{{ $account->is_active ? '' : ' (archived)' }}
                            </option>
                        @endforeach
                    </select>
                    @error('account_id') <div class="input-error">{{ $message }}</div> @enderror
                </div>

                <div class="field">
                    <label for="date">Date</label>
                    <input id="date" name="date" type="date" value="{{ old('date', $expense->date->format('Y-m-d')) }}">
                    @error('date') <div class="input-error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="field">
                <label for="notes">Notes</label>
                <textarea id="notes" name="notes">{{ old('notes', $expense->notes) }}</textarea>
                @error('notes') <div class="input-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-expense">Save changes</button>
                <a href="{{ route('expenses.index') }}" class="btn">Cancel</a>
            </div>
        </form>

        <div class="danger-zone">
            <p>Remove this expense permanently.</p>
            <form action="{{ route('expenses.destroy', $expense) }}" method="POST" onsubmit="return confirm('Delete this expense?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Delete</button>
            </form>
        </div>
    </section>
@endsection
