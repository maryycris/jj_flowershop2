@extends('layouts.clerk_app')

@section('content')
<div class="container py-3">
    <h4 class="mb-3">Loyalty Cards</h4>
    <div class="table-responsive bg-white p-3 rounded">
        <table class="table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Stamps</th>
                    <th>Updated</th>
                    <th style="width:260px">Adjust</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cards as $card)
                <tr>
                    <td>
                        <div class="d-flex align-items-center justify-content-between">
                            <span>{{ $card->user->first_name ?? $card->user->name }} (ID: {{ $card->user_id }})</span>
                            <a href="{{ route('clerk.loyalty.history', $card) }}" class="btn btn-link btn-sm">History</a>
                        </div>
                    </td>
                    <td>{{ $card->stamps_count }}/5</td>
                    <td>{{ $card->updated_at->diffForHumans() }}</td>
                    <td>
                        <form method="POST" action="{{ route('clerk.loyalty.adjust', $card) }}" class="d-flex gap-2">
                            @csrf
                            @method('PUT')
                            <input type="number" name="delta" class="form-control" value="1" min="-5" max="5" style="width:80px">
                            <input type="text" name="reason" class="form-control" placeholder="Reason (optional)">
                            <button class="btn btn-success">Apply</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center">No loyalty cards yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection


