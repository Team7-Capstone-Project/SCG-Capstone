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
    public function index()
    {
        // Total Shipments
        $totalShipments = Shipment::count();

        // Delivered Shipments
        $deliveredShipments = Shipment::delivered()->count();

        // Late Shipments (Delivered but ata_customer > customer_receiving_schedule)
        $lateShipments = Shipment::late()->count();

        // On-Time Shipments (Delivered and ata_customer <= customer_receiving_schedule)
        $onTimeShipments = Shipment::onTime()->count();

        // Calculate On-Time Delivery Rate
        // OTD Rate = (On-Time Shipments / Total Delivered Shipments) * 100
        $otdRate = $deliveredShipments > 0 
            ? round(($onTimeShipments / $deliveredShipments) * 100, 1) 
            : 0;

        // Recent Shipments (last 10)
        $recentShipments = Shipment::with(['customer', 'supplier', 'createdBy'])
            ->latest()
            ->limit(10)
            ->get();

        // Monthly Trend Data (last 6 months)
        $monthlyData = $this->getMonthlyTrendData();

        // Shipments by Status
        $shipmentsByStatus = Shipment::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return view('dashboard', compact(
            'totalShipments',
            'deliveredShipments',
            'lateShipments',
            'onTimeShipments',
            'otdRate',
            'recentShipments',
            'monthlyData',
            'shipmentsByStatus'
        ));
    }

    /**
     * Get monthly trend data for charts
     */
    private function getMonthlyTrendData()
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

            // Total shipments in this month
            $totalCounts[] = Shipment::whereBetween('created_at', [$monthStart, $monthEnd])->count();

            // Delivered shipments in this month
            $deliveredCounts[] = Shipment::delivered()
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->count();

            // On-time shipments in this month
            $onTimeCounts[] = Shipment::onTime()
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->count();
        }

        return [
            'months' => $months,
            'total' => $totalCounts,
            'delivered' => $deliveredCounts,
            'onTime' => $onTimeCounts,
        ];
    }
}
