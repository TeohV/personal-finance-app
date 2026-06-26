@extends('layouts.app')

@section('title', 'Categories')

@section('content')
    <div class="page-header">
        <div>
            <h2 class="page-title">Categories</h2>
            <p class="page-subtitle">Keep income and expenses organised.</p>
        </div>
        <a href="{{ route('categories.create') }}" class="btn btn-primary">New category</a>
    </div>

    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Type</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                    <tr>
                        <td><strong>{{ $category->name }}</strong></td>
                        <td>
                            <span class="badge {{ $category->type === 'income' ? 'badge-income' : 'badge-expense' }}">
                                {{ ucfirst($category->type) }}
                            </span>
                        </td>
                        <td>
                            <div class="table-actions">
                                <a href="{{ route('categories.edit', $category) }}" class="btn btn-sm">Edit</a>
                                <form action="{{ route('categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Delete this category?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-expense">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="empty-state">No categories yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
