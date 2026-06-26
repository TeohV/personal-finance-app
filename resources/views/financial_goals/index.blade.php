@extends('layouts.app')

@section('title', 'Financial Goals')

@section('content')
    <div class="page-header">
        <div>
            <h2 class="page-title">Financial goals</h2>
            <p class="page-subtitle">Track savings targets and progress.</p>
        </div>
        <a href="{{ route('financial-goals.create') }}" class="btn btn-income">Add goal</a>
    </div>

    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Goal</th>
                    <th>Progress</th>
                    <th>Target date</th>
                    <th>Status</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($goals as $goal)
                    <tr>
                        <td>
                            <strong>{{ $goal->name }}</strong><br>
                            <span class="text-muted">Target: RM {{ number_format($goal->target_amount, 2) }}</span>
                        </td>
                        <td style="min-width: 220px;">
                            <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
                                <span>RM {{ number_format($goal->current_amount, 2) }}</span>
                                <span>{{ $goal->progress_percentage }}%</span>
                            </div>
                            <div class="progress">
                                <div class="progress-fill" style="width: {{ $goal->progress_percentage }}%;"></div>
                            </div>
                        </td>
                        <td>{{ $goal->target_date ? $goal->target_date->format('d M Y') : 'No deadline' }}</td>
                        <td><span class="badge badge-muted">{{ str_replace('_', ' ', ucfirst($goal->status)) }}</span></td>
                        <td>
                            <div class="table-actions">
                                <a href="{{ route('financial-goals.edit', $goal) }}" class="btn btn-sm">Edit</a>
                                <form action="{{ route('financial-goals.destroy', $goal) }}" method="POST" onsubmit="return confirm('Delete this financial goal?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-expense">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty-state">No financial goals set yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
