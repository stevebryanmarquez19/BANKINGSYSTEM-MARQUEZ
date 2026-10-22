@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')

@foreach ($accounts as $account)
    @if ((float) $account->balance <= (float) $account->minimum_balance)
        <div class="alert alert-warn">
            &#9888; Low balance on {{ $account->account_number }}:
            ₱{{ number_format((float) $account->balance, 2) }}
            (minimum ₱{{ number_format((float) $account->minimum_balance, 2) }})
        </div>
    @endif
@endforeach

<div class="card">
    <h2>Bank Statistics <small style="font-weight:400;color:#6b7280">(auto-refresh every 30s)</small></h2>
    <div class="stats">
        <div class="stat"><div class="num" id="stat-accounts">-</div>Total Accounts</div>
        <div class="stat"><div class="num">₱<span id="stat-money">-</span></div>Total Money in Bank</div>
        <div class="stat"><div class="num" id="stat-transactions">-</div>Transactions Today</div>
    </div>
    <small id="last-updated" style="color:#6b7280"></small>
</div>

<div class="card">
    <h2>My Accounts</h2>
    <table>
        <tr><th>Account #</th><th>Balance</th><th>Minimum</th></tr>
        @foreach ($accounts as $account)
            <tr>
                <td>{{ $account->account_number }}</td>
                <td>₱{{ number_format((float) $account->balance, 2) }}</td>
                <td>₱{{ number_format((float) $account->minimum_balance, 2) }}</td>
            </tr>
        @endforeach
    </table>
</div>

<div class="card">
    <h2>Notifications</h2>
    @forelse ($notifications as $n)
        <div class="alert alert-warn">
            <strong>{{ $n->data['account_number'] ?? '' }}</strong>:
            {{ $n->data['message'] ?? '' }}
            (balance ₱{{ number_format((float) ($n->data['balance'] ?? 0), 2) }})
            <small>— {{ $n->created_at->diffForHumans() }}</small>
        </div>
    @empty
        <p style="color:#6b7280">No notifications.</p>
    @endforelse
</div>

<script>
async function fetchStats() {
    try {
        const res = await fetch('/api/dashboard/stats', { headers: { 'Accept': 'application/json' } });
        if (!res.ok) throw new Error('Request failed: ' + res.status);
        const data = await res.json();

        document.getElementById('stat-accounts').textContent = data.total_accounts;
        document.getElementById('stat-money').textContent =
            Number(data.total_money).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('stat-transactions').textContent = data.total_transactions_today;
        document.getElementById('last-updated').textContent =
            'Last updated: ' + new Date().toLocaleTimeString();
    } catch (err) {
        console.error('Polling error:', err);
    }
}

fetchStats();                    // load immediately
setInterval(fetchStats, 30000);  // then every 30 seconds
</script>
@endsection
