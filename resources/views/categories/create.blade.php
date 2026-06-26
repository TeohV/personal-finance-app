@extends('layouts.app')

@section('title', 'New Category')

@section('content')
    <div class="page-header">
        <div>
            <h2 class="page-title">New category</h2>
            <p class="page-subtitle">Create a label for income or expenses.</p>
        </div>
        <a href="{{ route('categories.index') }}" class="btn">Back</a>
    </div>

    <section class="form-card" style="max-width: 620px;">
        <form action="{{ route('categories.store') }}" method="POST">
            @csrf

            <div class="field">
                <label for="name">Category name</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" placeholder="Example: Groceries" autofocus>
                @error('name') <div class="input-error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="type">Type</label>
                <select id="type" name="type">
                    <option value="expense" @selected(old('type', 'expense') === 'expense')>Expense</option>
                    <option value="income" @selected(old('type') === 'income')>Income</option>
                </select>
                @error('type') <div class="input-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Create category</button>
                <a href="{{ route('categories.index') }}" class="btn">Cancel</a>
            </div>
        </form>
    </section>
@endsection
