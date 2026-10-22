@extends('layouts.app')
@section('title', 'Transactions')
@section('content')
<div class="card">
    <h2>Transaction History</h2>
    <table>
        <tr><th>Date</th><th>Account</th><th>Type</th><th>Amount</th><th>Reference</th><th>Notes</th></tr>
        @forelse ($transactions as $t)
            <tr>
                <td>{{ $t->created_at->format('Y-m-d H:i') }}</td>
                <td>{{ $t->account->account_number }}</td>
                <td><span class="badge {{ $t->type }}">{{ ucfirst($t->type) }}</span></td>
                <td>₱{{ number_format((float) $t->amount, 2) }}</td>
                <td>{{ $t->reference_number }}</td>
                <td>{{ $t->notes }}</td>
            </tr>
        @empty
            <tr><td colspan="6" style="color:#6b7280">No transactions yet.</td></tr>
        @endforelse
    </table>
    <div style="margin-top:12px">
        @if ($transactions->previousPageUrl())
            <a href="{{ $transactions->previousPageUrl() }}">&laquo; Previous</a>
        @endif
        @if ($transactions->nextPageUrl())
            <a href="{{ $transactions->nextPageUrl() }}" style="margin-left:12px">Next &raquo;</a>
        @endif
    </div>
    <a class="btn" href="{{ route('transactions.create') }}">New Transaction</a>
</div>
@endsection
