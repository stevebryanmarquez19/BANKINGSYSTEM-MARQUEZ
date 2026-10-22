@extends('layouts.app')
@section('title', 'New Transaction')
@section('content')
<div class="card">
    <h2>New Transaction</h2>
    <form method="POST" action="{{ route('transactions.store') }}">
        @csrf
        <label>Account</label>
        <select name="account_id" required>
            @foreach ($accounts as $a)
                <option value="{{ $a->id }}" @selected(old('account_id') == $a->id)>
                    {{ $a->account_number }} — balance ₱{{ number_format((float) $a->balance, 2) }}
                    (min ₱{{ number_format((float) $a->minimum_balance, 2) }})
                </option>
            @endforeach
        </select>

        <label>Type</label>
        <select name="type" required>
            <option value="deposit" @selected(old('type') === 'deposit')>Deposit</option>
            <option value="withdrawal" @selected(old('type') === 'withdrawal')>Withdrawal</option>
        </select>

        <label>Amount</label>
        <input type="number" step="0.01" min="1" name="amount" value="{{ old('amount') }}" required>

        <label>Reference Number (optional)</label>
        <input type="text" name="reference_number" value="{{ old('reference_number') }}">

        <label>Notes (optional)</label>
        <textarea name="notes" rows="3">{{ old('notes') }}</textarea>

        <button class="btn" type="submit">Submit</button>
    </form>
</div>
@endsection
