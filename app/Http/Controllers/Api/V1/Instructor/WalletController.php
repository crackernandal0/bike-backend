<?php

namespace App\Http\Controllers\Api\V1\Instructor;


use App\Http\Controllers\Controller;
use App\Models\Driver\InstructorWallet;
use App\Models\DrivingSchool\Enrollment;
use App\Models\Ride\Ride;
use App\Models\Wallet;
use Illuminate\Support\Str;

class WalletController extends Controller
{
    public function fetchInstructorWalletStats()
    {
        // Get the authenticated instructor
        $instructor = auth('driver')->user();

        // Fetch the instructor's wallet, create if it does not exist
        $wallet = InstructorWallet::firstOrCreate(
            ['instructor_id' => $instructor->id],
            ['balance' => 0.00, 'is_active' => true]
        );

        // Total earnings from completed transactions (rounded to 2 decimal places)
        $totalEarnings = round($wallet->transactions()
            ->where('status', 'completed')
            ->sum('amount'), 2);

        // Count of enrolled students
        $enrolledStudentsCount = Enrollment::whereHas('course', function ($query) use ($instructor) {
            $query->where('instructor_id', $instructor->id);
        })
            ->where('status', 'enrolled') // Only count enrolled students
            ->count();

        // Total earnings for today (rounded)
        $todayEarnings = round($wallet->transactions()
            ->where('status', 'completed')
            ->whereDate('created_at', now())
            ->sum('amount'), 2);

        // Prepare a collection for all days in the current week (Monday to Sunday)
        $allDaysInWeek = collect();
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();

        for ($date = $startOfWeek->copy(); $date->lte($endOfWeek); $date->addDay()) {
            $allDaysInWeek->put($date->format('D'), 0);
        }

        // Fetch actual earnings for the week by each day and merge them with the full week's collection
        $weeklyEarnings = $wallet->transactions()
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->get()
            ->groupBy(function ($date) {
                return \Carbon\Carbon::parse($date->created_at)->format('D');
            })
            ->map(function ($day) {
                return round($day->sum('amount'), 2);
            });

        // Merge weekly earnings with the complete week (fill missing days with 0)
        $allDaysInWeek = $allDaysInWeek->merge($weeklyEarnings);

        // Generate a collection for all days in the current month
        $allDaysInMonth = collect();
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        for ($date = $startOfMonth->copy(); $date->lte($endOfMonth); $date->addDay()) {
            $allDaysInMonth->put($date->format('d-m-Y'), 0);
        }

        // Fetch monthly earnings and merge with all days of the month
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

        // Merge monthly earnings with all days in the month
        $allDaysInMonth = $allDaysInMonth->merge($monthlyEarnings);

        // Fetch the 5 most recent transactions
        $recentTransactions = $wallet->transactions()
            ->latest()
            ->take(5)
            ->get();

        // Prepare the response data
        $data = [
            'total_earnings' => $totalEarnings,
            'total_enrolled_students' => $enrolledStudentsCount,
            'today_earnings' => $todayEarnings,
            'weekly_earnings' => $allDaysInWeek,
            'monthly_earnings' => $allDaysInMonth,
            'wallet_balance' => round($wallet->balance, 2),
            'recent_transactions' => $recentTransactions,
        ];

        return jsonResponseWithData(true, 'Instructor wallet stats fetched successfully', $data);
    }


    public function fetchAllInstructorTransactions()
    {
        // Get the authenticated instructor
        $instructor = auth('driver')->user();

        // Fetch the instructor's wallet
        $wallet = InstructorWallet::where('instructor_id', $instructor->id)->first();

        if ($wallet) {
            // Fetch all transactions ordered by the latest
            $transactions = $wallet->transactions()->latest()->get();
        } else {
            $transactions = [];
        }
        return jsonResponseWithData(true, 'instructor transactions fetched successfully', ['transactions' => $transactions]);
    }

    public function withdrawInstructorFullBalance()
    {
        // Get the authenticated instructor
        $instructor = auth('driver')->user();

        // Fetch the instructor's wallet, ensuring it exists
        $wallet = InstructorWallet::where('instructor_id', $instructor->id)->first();


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
