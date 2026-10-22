<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;

class DashboardController extends Controller
{
    /** Web page */
    public function index()
    {
        $user          = auth()->user();
        $accounts      = $user->accounts()->get();
        $notifications = $user->notifications()->latest()->take(5)->get();

        return view('dashboard', compact('accounts', 'notifications'));
    }

    /** GET /api/dashboard/stats */
    public function stats()
    {
        return response()->json([
            'total_accounts'           => Account::count(),
            'total_money'              => (float) Account::sum('balance'),
            'total_transactions_today' => Transaction::whereDate('created_at', today())->count(),
        ]);
    }
}
