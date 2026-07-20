<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display SCM Dashboard with OTD Metrics (FR-R-01)
     */
    public function index(Request $request)
    {
        $dateExpression = DB::getDriverName() === 'sqlite'
            ? "strftime('%Y-%m', etd_port) as month_val"
            : "DATE_FORMAT(etd_port, '%Y-%m') as month_val";

        $availableMonths = Shipment::select(DB::raw($dateExpression))
            ->groupBy('month_val')
            ->orderBy('month_val', 'desc')
            ->pluck('month_val')
            ->map(function ($value) {
                $carbonDate = \Carbon\Carbon::createFromFormat('Y-m', $value);
                return [
                    'value' => $value,
                    'label' => $carbonDate->translatedFormat('F Y'),
                ];
            })
            ->toArray();

        $selectedMonth = $request->query('month');
        $selectedType = $request->query('type');
        $shipmentsQuery = Shipment::query();

        if ($selectedMonth && preg_match('/^\d{4}-\d{2}$/', $selectedMonth)) {
            list($year, $monthNum) = explode('-', $selectedMonth);
            $shipmentsQuery->whereYear('etd_port', $year)->whereMonth('etd_port', $monthNum);
        }

        if ($selectedType && in_array($selectedType, ['Import', 'Export'])) {
            $shipmentsQuery->where('type', $selectedType);
        }

        // Total Shipments
        $totalShipments = (clone $shipmentsQuery)->count();

        // Delivered Shipments (needed for OTD Rate)
        $deliveredShipments = (clone $shipmentsQuery)->delivered()->count();

        // In Transit Shipments (Replaces Delivered card on the UI)
        $inTransitShipments = (clone $shipmentsQuery)->inTransit()->count();

        // Late Shipments (Delivered but ata_customer > customer_receiving_schedule)
        $lateShipments = (clone $shipmentsQuery)->late()->count();

        // On-Time Shipments (Delivered and ata_customer == customer_receiving_schedule)
        $onTimeShipments = (clone $shipmentsQuery)->onTime()->count();

        // Early Shipments (Delivered and ata_customer < customer_receiving_schedule)
        $earlyShipments = (clone $shipmentsQuery)->early()->count();

        // Import & Export specific counts for summary metrics
        $importShipmentsCount = (clone $shipmentsQuery)->where('type', 'Import')->count();
        $exportShipmentsCount = (clone $shipmentsQuery)->where('type', 'Export')->count();

        // Calculate On-Time Delivery Rate
        $otdRate = $deliveredShipments > 0 
            ? round((($onTimeShipments + $earlyShipments) / $deliveredShipments) * 100, 1) 
            : 0;

        // Recent Shipments (last 10)
        $recentShipments = (clone $shipmentsQuery)->with(['customer', 'supplier', 'createdBy'])
            ->latest()
            ->limit(10)
            ->get();

        // Monthly Trend Data (last 6 months)
        $monthlyData = $this->getMonthlyTrendData($selectedType);

        // Shipments by Status
        $shipmentsByStatus = (clone $shipmentsQuery)->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return view('dashboard', compact(
            'totalShipments',
            'deliveredShipments',
            'inTransitShipments',
            'lateShipments',
            'earlyShipments',
            'onTimeShipments',
            'importShipmentsCount',
            'exportShipmentsCount',
            'otdRate',
            'recentShipments',
            'monthlyData',
            'shipmentsByStatus',
            'availableMonths'
        ));
    }

    /**
     * Get monthly trend data for charts
     */
    private function getMonthlyTrendData($type = null)
    {
        $months = [];
        $totalCounts = [];
        $deliveredCounts = [];
        $onTimeCounts = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthStart = $date->copy()->startOfMonth();
            $monthEnd = $date->copy()->endOfMonth();

            $months[] = $date->format('M Y');

            $baseQuery = Shipment::whereBetween('created_at', [$monthStart, $monthEnd]);
            if ($type && in_array($type, ['Import', 'Export'])) {
                $baseQuery->where('type', $type);
            }

            // Total shipments in this month
            $totalCounts[] = (clone $baseQuery)->count();

            // Delivered shipments in this month
            $deliveredCounts[] = (clone $baseQuery)->delivered()->count();

            // On-time shipments in this month
            $onTimeCounts[] = (clone $baseQuery)->onTime()->count();
        }

        return [
            'months' => $months,
            'total' => $totalCounts,
            'delivered' => $deliveredCounts,
            'onTime' => $onTimeCounts,
        ];
    }
}
