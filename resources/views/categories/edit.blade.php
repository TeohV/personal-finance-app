@extends('layouts.app')

@section('title', 'Edit Category')

@section('content')
    <div class="page-header">
        <div>
            <h2 class="page-title">Edit category</h2>
            <p class="page-subtitle">Update how this category is shown in your records.</p>
        </div>
        <a href="{{ route('categories.index') }}" class="btn">Back</a>
    </div>

    <section class="form-card" style="max-width: 620px;">
        <form action="{{ route('categories.update', $category) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="field">
                <label for="name">Category name</label>
                <input id="name" name="name" type="text" value="{{ old('name', $category->name) }}" autofocus>
                @error('name') <div class="input-error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="type">Type</label>
                <select id="type" name="type">
                    <option value="expense" @selected(old('type', $category->type) === 'expense')>Expense</option>
                    <option value="income" @selected(old('type', $category->type) === 'income')>Income</option>
                </select>
                @error('type') <div class="input-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save changes</button>
                <a href="{{ route('categories.index') }}" class="btn">Cancel</a>
            </div>
        </form>

        <div class="danger-zone">
            <p>Delete this category only if it is not used by transactions.</p>
            <form action="{{ route('categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Delete this category?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Delete</button>
            </form>
        </div>
    </section>
@endsection
