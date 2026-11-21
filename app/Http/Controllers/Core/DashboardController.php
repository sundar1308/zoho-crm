<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Team;
use App\Models\User;
use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService
    ) {}

    /**
     * Show CRM interactive dashboard.
     */
    public function index(Request $request): View
    {
        $dateRange = $request->query('range', 'month');
        $userId = $request->query('user_id') ? (int) $request->query('user_id') : null;
        $teamId = $request->query('team_id') ? (int) $request->query('team_id') : null;

        $metrics = $this->dashboardService->getMetrics($dateRange, $userId, $teamId);
        $chartData = $this->dashboardService->getChartData($dateRange, $userId);

        $users = User::where('is_active', true)->select('id', 'name')->orderBy('name')->get();
        $teams = Team::select('id', 'name')->orderBy('name')->get();

        return view('core.dashboard', compact(
            'metrics',
            'chartData',
            'dateRange',
            'userId',
            'teamId',
            'users',
            'teams'
        ));
    }

    /**
     * Return dynamic chart data via JSON for AJAX refreshes.
     */
    public function chartData(Request $request): JsonResponse
    {
        $dateRange = $request->query('range', 'month');
        $userId = $request->query('user_id') ? (int) $request->query('user_id') : null;

        $chartData = $this->dashboardService->getChartData($dateRange, $userId);
        $metrics = $this->dashboardService->getMetrics($dateRange, $userId);

        return response()->json([
            'metrics' => $metrics,
            'charts' => $chartData,
        ]);
    }

    /**
     * Export dashboard summary report as CSV.
     */
    public function exportReport(Request $request): StreamedResponse
    {
        $dateRange = $request->query('range', 'month');
        $metrics = $this->dashboardService->getMetrics($dateRange);

        AuditLog::log('export_dashboard_report');

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="crm_dashboard_summary_'.date('Ymd_His').'.csv"',
        ];

        $callback = function () use ($metrics, $dateRange) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Zoho CRM Platform - Executive Dashboard Summary']);
            fputcsv($handle, ['Report Filter', ucfirst($dateRange)]);
            fputcsv($handle, ['Generated At', now()->toDateTimeString()]);
            fputcsv($handle, []);
            fputcsv($handle, ['Metric', 'Value']);
            fputcsv($handle, ['Total Leads', $metrics['total_leads']]);
            fputcsv($handle, ['New Leads', $metrics['new_leads']]);
            fputcsv($handle, ['Qualified Leads', $metrics['qualified_leads']]);
            fputcsv($handle, ['Converted Leads', $metrics['converted_leads']]);
            fputcsv($handle, ['Lead Conversion Rate (%)', $metrics['lead_conversion_rate'].'%']);
            fputcsv($handle, ['Total Contacts', $metrics['total_contacts']]);
            fputcsv($handle, ['Total Accounts', $metrics['total_accounts']]);
            fputcsv($handle, ['Open Deals Count', $metrics['open_deals']]);
            fputcsv($handle, ['Won Deals Count', $metrics['won_deals']]);
            fputcsv($handle, ['Lost Deals Count', $metrics['lost_deals']]);
            fputcsv($handle, ['Total Pipeline Value ($)', number_format($metrics['pipeline_value'], 2)]);
            fputcsv($handle, ['Expected Revenue ($)', number_format($metrics['expected_revenue'], 2)]);
            fputcsv($handle, ['Monthly Won Revenue ($)', number_format($metrics['monthly_revenue'], 2)]);
            fputcsv($handle, ['Deal Win Rate (%)', $metrics['win_rate'].'%']);
            fputcsv($handle, ['Pending Tasks', $metrics['pending_tasks']]);
            fputcsv($handle, ['Overdue Tasks', $metrics['overdue_tasks']]);
            fputcsv($handle, ['Upcoming Meetings', $metrics['upcoming_meetings']]);
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
