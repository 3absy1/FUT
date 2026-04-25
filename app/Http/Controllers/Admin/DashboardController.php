<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\GameMatch;
use App\Models\Payment;
use App\Models\Pitch;
use App\Models\Stadium;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $thisWeekStart = Carbon::now()->startOfWeek();
        $thisMonthStart = Carbon::now()->startOfMonth();

        // Core counts
        $stats = [
            'total_users'    => User::where('is_stadium_owner', false)->count(),
            'total_stadiums' => Stadium::count(),
            'total_pitches'  => Pitch::count(),
            'total_clubs'    => Club::count(),
            'total_matches'  => GameMatch::count(),
            'active_matches' => GameMatch::where('status', 'in_progress')->count(),
        ];

        // Revenue
        $revenue = [
            'daily'   => Payment::whereDate('paid_at', $today)->where('status', 'paid')->sum('amount'),
            'weekly'  => Payment::whereBetween('paid_at', [$thisWeekStart, now()])->where('status', 'paid')->sum('amount'),
            'monthly' => Payment::whereBetween('paid_at', [$thisMonthStart, now()])->where('status', 'paid')->sum('amount'),
            'total'   => Payment::where('status', 'paid')->sum('amount'),
        ];

        // Matches per period
        $matchCounts = [
            'daily'   => GameMatch::whereDate('scheduled_datetime', $today)->count(),
            'weekly'  => GameMatch::whereBetween('scheduled_datetime', [$thisWeekStart, now()])->count(),
            'monthly' => GameMatch::whereBetween('scheduled_datetime', [$thisMonthStart, now()])->count(),
        ];

        // Monthly matches chart (last 12 months)
        $monthlyMatchesChart = GameMatch::select(
            DB::raw('YEAR(scheduled_datetime) as year'),
            DB::raw('MONTH(scheduled_datetime) as month'),
            DB::raw('COUNT(*) as count')
        )
            ->where('scheduled_datetime', '>=', now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->map(fn($r) => [
                'label' => Carbon::createFromDate($r->year, $r->month, 1)->format('M Y'),
                'count' => $r->count,
            ]);

        // Monthly revenue chart (last 12 months)
        $monthlyRevenueChart = Payment::select(
            DB::raw('YEAR(paid_at) as year'),
            DB::raw('MONTH(paid_at) as month'),
            DB::raw('SUM(amount) as total')
        )
            ->where('status', 'paid')
            ->where('paid_at', '>=', now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->map(fn($r) => [
                'label' => Carbon::createFromDate($r->year, $r->month, 1)->format('M Y'),
                'total' => (float) $r->total,
            ]);

        // Top stadiums by match count
        $topStadiums = Stadium::withCount('matches')
            ->orderByDesc('matches_count')
            ->limit(5)
            ->get();

        // Recent matches
        $recentMatches = GameMatch::with(['clubA', 'clubB', 'stadium'])
            ->orderByDesc('scheduled_datetime')
            ->limit(8)
            ->get();

        // New users (last 30 days)
        $newUsers = User::where('created_at', '>=', now()->subDays(30))
            ->where('is_stadium_owner', false)
            ->count();

        // Daily matches for the last 7 days
        $dailyMatchesChart = collect(range(6, 0))->map(function ($daysAgo) {
            $date = Carbon::today()->subDays($daysAgo);
            return [
                'label' => $date->format('D'),
                'count' => GameMatch::whereDate('scheduled_datetime', $date)->count(),
            ];
        });

        return view('admin.pages.dashboard.index', compact(
            'stats', 'revenue', 'matchCounts', 'monthlyMatchesChart',
            'monthlyRevenueChart', 'topStadiums', 'recentMatches',
            'newUsers', 'dailyMatchesChart'
        ));
    }
}
