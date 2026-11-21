<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Activity;
use App\Models\Contact;
use App\Models\Deal;
use App\Models\DealStage;
use App\Models\Lead;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * Calculate all dashboard statistics with optional date range, user, and team filters.
     */
    public function getMetrics(?string $dateRange = 'month', ?int $userId = null, ?int $teamId = null): array
    {
        $startDate = $this->resolveStartDate($dateRange);

        // Base queries
        $leadQuery = Lead::query();
        $dealQuery = Deal::query();
        $activityQuery = Activity::query();
        $contactQuery = Contact::query();
        $accountQuery = Account::query();

        if ($startDate) {
            $leadQuery->where('created_at', '>=', $startDate);
            $dealQuery->where('created_at', '>=', $startDate);
            $activityQuery->where('created_at', '>=', $startDate);
        }

        if ($userId) {
            $leadQuery->where('assigned_user_id', $userId);
            $dealQuery->where('owner_id', $userId);
            $activityQuery->where('assigned_user_id', $userId);
            $contactQuery->where('owner_id', $userId);
            $accountQuery->where('owner_id', $userId);
        }

        if ($teamId) {
            $leadQuery->where('team_id', $teamId);
        }

        // Leads Metrics
        $totalLeads = (clone $leadQuery)->count();
        $newLeads = (clone $leadQuery)->whereHas('status', fn ($q) => $q->where('name', 'New'))->count();
        $qualifiedLeads = (clone $leadQuery)->whereHas('status', fn ($q) => $q->where('name', 'Qualified'))->count();
        $convertedLeads = (clone $leadQuery)->where('is_converted', true)->count();
        $conversionRate = $totalLeads > 0 ? round(($convertedLeads / $totalLeads) * 100, 1) : 0.0;

        // Contacts & Accounts
        $totalContacts = $contactQuery->count();
        $totalAccounts = $accountQuery->count();

        // Deals Metrics
        $openDealsQuery = (clone $dealQuery)->whereHas('stage', fn ($q) => $q->where('category', 'open'));
        $wonDealsQuery = (clone $dealQuery)->whereHas('stage', fn ($q) => $q->where('category', 'won'));
        $lostDealsQuery = (clone $dealQuery)->whereHas('stage', fn ($q) => $q->where('category', 'lost'));

        $openDealsCount = (clone $openDealsQuery)->count();
        $wonDealsCount = (clone $wonDealsQuery)->count();
        $lostDealsCount = (clone $lostDealsQuery)->count();

        $pipelineValue = (float) (clone $openDealsQuery)->sum('amount');
        $expectedRevenue = (float) (clone $openDealsQuery)->sum('expected_revenue');
        $monthlyWonRevenue = (float) (clone $wonDealsQuery)
            ->whereMonth('closing_date', Carbon::now()->month)
            ->whereYear('closing_date', Carbon::now()->year)
            ->sum('amount');

        $totalClosedDeals = $wonDealsCount + $lostDealsCount;
        $winRate = $totalClosedDeals > 0 ? round(($wonDealsCount / $totalClosedDeals) * 100, 1) : 0.0;
        $monthlyTarget = 150000.00;
        $targetAchievement = $monthlyTarget > 0 ? round(($monthlyWonRevenue / $monthlyTarget) * 100, 1) : 0.0;

        // Activities Metrics
        $pendingTasks = (clone $activityQuery)
            ->where('activity_type', 'task')
            ->whereIn('status', ['not_started', 'in_progress'])
            ->count();

        $overdueTasks = (clone $activityQuery)
            ->where('activity_type', 'task')
            ->whereIn('status', ['not_started', 'in_progress'])
            ->where('due_date', '<', Carbon::now())
            ->count();

        $upcomingMeetings = (clone $activityQuery)
            ->where('activity_type', 'meeting')
            ->where('start_time', '>=', Carbon::now())
            ->count();

        // Recent Activities
        $recentActivities = (clone $activityQuery)
            ->with(['assignedUser', 'subjectable'])
            ->latest()
            ->limit(8)
            ->get();

        return [
            'total_leads' => $totalLeads,
            'new_leads' => $newLeads,
            'qualified_leads' => $qualifiedLeads,
            'converted_leads' => $convertedLeads,
            'lead_conversion_rate' => $conversionRate,
            'total_contacts' => $totalContacts,
            'total_accounts' => $totalAccounts,
            'open_deals' => $openDealsCount,
            'won_deals' => $wonDealsCount,
            'lost_deals' => $lostDealsCount,
            'pipeline_value' => $pipelineValue,
            'expected_revenue' => $expectedRevenue,
            'monthly_revenue' => $monthlyWonRevenue,
            'win_rate' => $winRate,
            'monthly_target' => $monthlyTarget,
            'target_achievement' => $targetAchievement,
            'pending_tasks' => $pendingTasks,
            'overdue_tasks' => $overdueTasks,
            'upcoming_meetings' => $upcomingMeetings,
            'recent_activities' => $recentActivities,
        ];
    }

    /**
     * Get real Chart.js datasets from database records.
     */
    public function getChartData(?string $dateRange = 'month', ?int $userId = null): array
    {
        $startDate = $this->resolveStartDate($dateRange);

        // 1. Leads by Source
        $leadSourceData = Lead::query()
            ->when($startDate, fn ($q) => $q->where('leads.created_at', '>=', $startDate))
            ->when($userId, fn ($q) => $q->where('leads.assigned_user_id', $userId))
            ->join('lead_sources', 'leads.lead_source_id', '=', 'lead_sources.id')
            ->select('lead_sources.name as label', DB::raw('COUNT(leads.id) as count'))
            ->groupBy('lead_sources.name')
            ->get();

        // 2. Leads by Status
        $leadStatusData = Lead::query()
            ->when($startDate, fn ($q) => $q->where('leads.created_at', '>=', $startDate))
            ->when($userId, fn ($q) => $q->where('leads.assigned_user_id', $userId))
            ->join('lead_statuses', 'leads.lead_status_id', '=', 'lead_statuses.id')
            ->select('lead_statuses.name as label', 'lead_statuses.color', DB::raw('COUNT(leads.id) as count'))
            ->groupBy('lead_statuses.name', 'lead_statuses.color')
            ->get();

        // 3. Deals by Stage
        $dealsByStage = DealStage::query()
            ->leftJoin('deals', function ($join) use ($startDate, $userId) {
                $join->on('deal_stages.id', '=', 'deals.stage_id')
                    ->whereNull('deals.deleted_at');
                if ($startDate) {
                    $join->where('deals.created_at', '>=', $startDate);
                }
                if ($userId) {
                    $join->where('deals.owner_id', $userId);
                }
            })
            ->select(
                'deal_stages.name as label',
                'deal_stages.color',
                DB::raw('COUNT(deals.id) as deal_count'),
                DB::raw('COALESCE(SUM(deals.amount), 0) as total_amount')
            )
            ->groupBy('deal_stages.id', 'deal_stages.name', 'deal_stages.color', 'deal_stages.sort_order')
            ->orderBy('deal_stages.sort_order')
            ->get();

        // 4. Monthly Revenue (Last 6 Months)
        $monthlyRevenueLabels = [];
        $monthlyRevenueValues = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthLabel = $month->format('M Y');
            $monthlyRevenueLabels[] = $monthLabel;

            $rev = Deal::query()
                ->whereHas('stage', fn ($q) => $q->where('category', 'won'))
                ->when($userId, fn ($q) => $q->where('owner_id', $userId))
                ->whereMonth('closing_date', $month->month)
                ->whereYear('closing_date', $month->year)
                ->sum('amount');

            $monthlyRevenueValues[] = (float) $rev;
        }

        // 5. Won vs Lost Deals comparison
        $wonCount = Deal::query()
            ->whereHas('stage', fn ($q) => $q->where('category', 'won'))
            ->when($startDate, fn ($q) => $q->where('created_at', '>=', $startDate))
            ->when($userId, fn ($q) => $q->where('owner_id', $userId))
            ->count();

        $lostCount = Deal::query()
            ->whereHas('stage', fn ($q) => $q->where('category', 'lost'))
            ->when($startDate, fn ($q) => $q->where('created_at', '>=', $startDate))
            ->when($userId, fn ($q) => $q->where('owner_id', $userId))
            ->count();

        return [
            'leads_by_source' => [
                'labels' => $leadSourceData->pluck('label')->all(),
                'values' => $leadSourceData->pluck('count')->all(),
            ],
            'leads_by_status' => [
                'labels' => $leadStatusData->pluck('label')->all(),
                'colors' => $leadStatusData->pluck('color')->all(),
                'values' => $leadStatusData->pluck('count')->all(),
            ],
            'deals_by_stage' => [
                'labels' => $dealsByStage->pluck('label')->all(),
                'colors' => $dealsByStage->pluck('color')->all(),
                'counts' => $dealsByStage->pluck('deal_count')->all(),
                'amounts' => $dealsByStage->pluck('total_amount')->all(),
            ],
            'monthly_revenue' => [
                'labels' => $monthlyRevenueLabels,
                'values' => $monthlyRevenueValues,
            ],
            'won_vs_lost' => [
                'won' => $wonCount,
                'lost' => $lostCount,
            ],
        ];
    }

    protected function resolveStartDate(?string $range): ?Carbon
    {
        return match ($range) {
            'today' => Carbon::today(),
            'week' => Carbon::now()->startOfWeek(),
            'month' => Carbon::now()->startOfMonth(),
            'quarter' => Carbon::now()->startOfQuarter(),
            'year' => Carbon::now()->startOfYear(),
            'all' => null,
            default => Carbon::now()->startOfMonth(),
        };
    }
}
