<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use RuntimeException;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with('account')
            ->whereHas('account', fn ($q) => $q->where('user_id', auth()->id()))
            ->latest()
            ->simplePaginate(15);

        return view('transactions.index', compact('transactions'));
    }

    public function create()
    {
        $accounts = Account::where('user_id', auth()->id())->get();

        return view('transactions.create', compact('accounts'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_id'       => ['required', 'exists:accounts,id'],
            'type'             => ['required', 'in:deposit,withdrawal'],
            'amount'           => ['required', 'numeric', 'min:1'],
            'reference_number' => ['nullable', 'string', 'max:255'],
            'notes'            => ['nullable', 'string'],
        ]);

        // Custom validation (after hook): ownership + sufficient funds
        $validator->after(function ($v) use ($request) {
            if ($v->errors()->isNotEmpty()) {
                return;
            }

            $account = Account::find($request->input('account_id'));

            if ($account->user_id !== auth()->id()) {
                $v->errors()->add('account_id', 'That account does not belong to you.');
                return;
            }

            if ($request->input('type') === 'withdrawal') {
                $remaining = (float) $account->balance - (float) $request->input('amount');

                if ($remaining < (float) $account->minimum_balance) {
                    $v->errors()->add(
                        'amount',
                        'Insufficient funds. Your balance must stay at or above '
                        . number_format((float) $account->minimum_balance, 2)
                        . ' (current balance: ' . number_format((float) $account->balance, 2) . ').'
                    );
                }
            }
        });

        $validated = $validator->validate();

        try {
            // If the Observer throws, the whole DB transaction is rolled back
            DB::transaction(function () use ($validated) {
                Transaction::create($validated);
            });
        } catch (RuntimeException $e) {
            return back()->withErrors(['amount' => $e->getMessage()])->withInput();
        }

        $redirect = redirect()->route('transactions.index')
            ->with('success', 'Transaction completed successfully.');

        // Immediate on-screen alert if a withdrawal brought the account to its minimum
        if ($validated['type'] === 'withdrawal') {
            $account = Account::find($validated['account_id']);

            if ((float) $account->balance <= (float) $account->minimum_balance) {
                $redirect->with('warning', 'Low balance alert for ' . $account->account_number
                    . ': balance is ' . number_format((float) $account->balance, 2)
                    . ' (minimum ' . number_format((float) $account->minimum_balance, 2) . ').');
            }
        }

        return $redirect;
    }
}
