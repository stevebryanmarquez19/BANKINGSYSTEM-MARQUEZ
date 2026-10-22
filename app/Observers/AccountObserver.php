<?php

namespace App\Observers;

use App\Models\Transaction;
use App\Notifications\LowBalanceNotification;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class AccountObserver
{
    /**
     * "creating" fires BEFORE the INSERT. Throwing here prevents the
     * transaction row from being saved.
     */
    public function creating(Transaction $transaction): void
    {
        DB::transaction(function () use ($transaction) {
            // Lock the account row to avoid race conditions
            $account = $transaction->account()->lockForUpdate()->first();

            $amount  = (float) $transaction->amount;
            $balance = (float) $account->balance;
            $minimum = (float) $account->minimum_balance;

            if ($transaction->type === 'deposit') {
                $newBalance = $balance + $amount;
            } else { // withdrawal or transfer
                $newBalance = $balance - $amount;

                if ($newBalance < 0 || $newBalance < $minimum) {
                    throw new RuntimeException(
                        'Insufficient funds: balance cannot fall below the minimum balance of '
                        . number_format($minimum, 2) . '.'
                    );
                }
            }

            $account->balance = round($newBalance, 2);

            // Balance recovered above the minimum -> allow future alerts again
            if ($newBalance > $minimum) {
                $account->low_balance_notified = false;
            }

            $account->save();
        });
    }

    /**
     * "created" fires AFTER the INSERT: send the low-balance alert once.
     */
    public function created(Transaction $transaction): void
    {
        if (! in_array($transaction->type, ['withdrawal', 'transfer'])) {
            return;
        }

        $account = $transaction->account()->first();

        if ((float) $account->balance <= (float) $account->minimum_balance
            && ! $account->low_balance_notified) {
            $account->user->notify(new LowBalanceNotification($account));
            $account->update(['low_balance_notified' => true]);
        }
    }
}
