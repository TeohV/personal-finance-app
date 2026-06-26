@extends('layouts.app')

@section('title', 'New Financial Goal')

@section('content')
    <div class="page-header">
        <div>
            <h2 class="page-title">New financial goal</h2>
            <p class="page-subtitle">Set a saving target and track your reserved progress.</p>
        </div>
        <a href="{{ route('financial-goals.index') }}" class="btn">Back</a>
    </div>

    <section class="form-card" style="max-width: 760px;">
        <form action="{{ route('financial-goals.store') }}" method="POST">
            @csrf

            <div class="field">
                <label for="name">Goal name</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" placeholder="Example: Emergency fund" autofocus>
                @error('name') <div class="input-error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="description">Description</label>
                <textarea id="description" name="description" placeholder="Optional">{{ old('description') }}</textarea>
                @error('description') <div class="input-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-grid">
                <div class="field">
                    <label for="target_amount">Target amount (RM)</label>
                    <input id="target_amount" name="target_amount" type="number" min="0.01" step="0.01" value="{{ old('target_amount') }}">
                    @error('target_amount') <div class="input-error">{{ $message }}</div> @enderror
                </div>

                <div class="field">
                    <label for="current_amount">Current progress (RM)</label>
                    <input id="current_amount" name="current_amount" type="number" min="0" step="0.01" value="{{ old('current_amount', 0) }}">
                    @error('current_amount') <div class="input-error">{{ $message }}</div> @enderror
                </div>

                <div class="field">
                    <label for="target_date">Target date</label>
                    <input id="target_date" name="target_date" type="date" value="{{ old('target_date') }}">
                    @error('target_date') <div class="input-error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="field">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="in_progress" @selected(old('status', 'in_progress') === 'in_progress')>In progress</option>
                    <option value="completed" @selected(old('status') === 'completed')>Completed</option>
                    <option value="cancelled" @selected(old('status') === 'cancelled')>Cancelled</option>
                </select>
                @error('status') <div class="input-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-income">Save goal</button>
                <a href="{{ route('financial-goals.index') }}" class="btn">Cancel</a>
            </div>
        </form>
    </section>
@endsection
