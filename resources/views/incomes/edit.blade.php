@extends('layouts.app')

@section('title', 'Edit Income')

@section('content')
    <div class="page-header">
        <div>
            <h2 class="page-title">Edit income</h2>
            <p class="page-subtitle">Update this income record.</p>
        </div>
        <a href="{{ route('incomes.index') }}" class="btn">Back</a>
    </div>

    <section class="form-card" style="max-width: 720px;">
        <form action="{{ route('incomes.update', $income) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="field">
                <label for="source">Source</label>
                <input id="source" name="source" type="text" value="{{ old('source', $income->source) }}" autofocus>
                @error('source') <div class="input-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-grid">
                <div class="field">
                    <label for="amount">Amount (RM)</label>
                    <input id="amount" name="amount" type="number" min="0.01" step="0.01" value="{{ old('amount', $income->amount) }}">
                    @error('amount') <div class="input-error">{{ $message }}</div> @enderror
                </div>

                <div class="field">
                    <label for="category_id">Category</label>
                    <select id="category_id" name="category_id">
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id', $income->category_id) == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <div class="input-error">{{ $message }}</div> @enderror
                </div>

                <div class="field">
                    <label for="account_id">Account</label>
                    <select id="account_id" name="account_id">
                        @foreach ($accounts as $account)
                            <option value="{{ $account->id }}" @selected(old('account_id', $income->account_id) == $account->id)>
                                {{ $account->name }}{{ $account->is_active ? '' : ' (archived)' }}
                            </option>
                        @endforeach
                    </select>
                    @error('account_id') <div class="input-error">{{ $message }}</div> @enderror
                </div>

                <div class="field">
                    <label for="income_date">Date</label>
                    <input id="income_date" name="income_date" type="date" value="{{ old('income_date', \Carbon\Carbon::parse($income->income_date)->format('Y-m-d')) }}">
                    @error('income_date') <div class="input-error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-income">Save changes</button>
                <a href="{{ route('incomes.index') }}" class="btn">Cancel</a>
            </div>
        </form>

        <div class="danger-zone">
            <p>Remove this income record permanently.</p>
            <form action="{{ route('incomes.destroy', $income) }}" method="POST" onsubmit="return confirm('Delete this income entry?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Delete</button>
            </form>
        </div>
    </section>
@endsection
