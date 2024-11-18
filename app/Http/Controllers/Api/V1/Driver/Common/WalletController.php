<?php

namespace App\Http\Controllers\Api\V1\Driver\Common;

use App\Http\Controllers\Controller;
use App\Models\Ride\Ride;
use App\Models\Wallet;
use Illuminate\Support\Str;

class WalletController extends Controller
{
    public function fetchDriverWalletStats()
    {
        // Get the authenticated driver
        $driver = auth('driver')->user();

        // Fetch the driver's wallet, create if it does not exist
        $wallet = Wallet::firstOrCreate(
            ['driver_id' => $driver->id],
            ['balance' => 0.00, 'is_active' => true]
        );

        // Total earnings from completed transactions
        $totalEarnings = round($wallet->transactions()
            ->where('status', 'completed')
            ->sum('amount'), 2);

        // Count of completed rides
        $totalCompletedRides = Ride::where('driver_id', $driver->id)
            ->where('ride_status', 'completed')
            ->count();

        // Total distance traveled in completed rides
        $totalDistanceTraveled = round(Ride::where('driver_id', $driver->id)
            ->where('ride_status', 'completed')
            ->sum('total_distance'), 2);

        // Total earnings for today
        $todayEarnings = round($wallet->transactions()
            ->where('status', 'completed')
            ->whereDate('created_at', now())
            ->sum('amount'), 2);

        // Initialize an array for all days of the current week
        $allDaysOfWeek = collect([
            'Mon' => 0,
            'Tue' => 0,
            'Wed' => 0,
            'Thu' => 0,
            'Fri' => 0,
            'Sat' => 0,
            'Sun' => 0
        ]);

        // Total earnings for the current week, grouped by day
        $weeklyEarnings = $wallet->transactions()
            ->where('status', 'completed')
            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->get()
            ->groupBy(function ($date) {
                return \Carbon\Carbon::parse($date->created_at)->format('D');
            })
            ->map(function ($day) {
                return round($day->sum('amount'), 2);
            });

        // Merge the weekly earnings into all days of the week
        $allDaysOfWeek = $allDaysOfWeek->merge($weeklyEarnings);

        // Handle monthly earnings (already working as expected)
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();
        $allDaysInMonth = collect();
        for ($date = $startOfMonth->copy(); $date->lte($endOfMonth); $date->addDay()) {
            $allDaysInMonth->put($date->format('d-m-Y'), 0);
        }
        $monthlyEarnings = $wallet->transactions()
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->get()
            ->groupBy(function ($transaction) {
                return \Carbon\Carbon::parse($transaction->created_at)->format('d-m-Y');
            })
            ->map(function ($dayTransactions) {
                return round($dayTransactions->sum('amount'), 2);
            });
        $allDaysInMonth = $allDaysInMonth->merge($monthlyEarnings);

        // Fetch the 5 most recent transactions
        $recentTransactions = $wallet->transactions()
            ->latest()
            ->take(5)
            ->get();

        // Prepare the response data
        $data = [
            'total_earnings' => $totalEarnings,
            'total_completed_rides' => $totalCompletedRides,
            'total_distance_traveled' => $totalDistanceTraveled,
            'today_earnings' => $todayEarnings,
            'weekly_earnings' => $allDaysOfWeek,
            'monthly_earnings' => $allDaysInMonth,
            'wallet_balance' => round($wallet->balance, 2),
            'recent_transactions' => $recentTransactions,
        ];

        return jsonResponseWithData(true, 'Driver wallet stats fetched successfully', $data);
    }


    public function fetchAllDriverTransactions()
    {
        // Get the authenticated driver
        $driver = auth('driver')->user();

        // Fetch the driver's wallet
        $wallet = Wallet::where('driver_id', $driver->id)->first();

        if ($wallet) {
            // Fetch all transactions ordered by the latest
            $transactions = $wallet->transactions()->latest()->get();
        } else {
            $transactions = [];
        }
        return jsonResponseWithData(true, 'Driver transactions fetched successfully', ['transactions' => $transactions]);
    }

    public function withdrawDriverFullBalance()
    {
        // Get the authenticated driver
        $driver = auth('driver')->user();

        // Fetch the driver's wallet, ensuring it exists
        $wallet = Wallet::where('driver_id', $driver->id)->first();


        // Check if the wallet has sufficient balance
        if (!$wallet || $wallet->balance <= 0) {
            return jsonResponse(false, 'No available balance to withdraw');
        }

        // Get the full available balance
        $amount = $wallet->balance;

        // Deduct the full balance from the wallet
        $wallet->balance = 0;
        $wallet->save();

        // Create a transaction record for the withdrawal
        $transaction = $wallet->transactions()->create([
            'type' => 'withdraw',
            'amount' => $amount,
            'transaction_id' => Str::uuid(), // Unique transaction ID
            'reference' => 'Full Withdrawal',
            'status' => 'completed',
        ]);

        // Return a success response with the transaction details
        return jsonResponseWithData(true, 'Full balance withdrawn successfully', $transaction);
    }
}
