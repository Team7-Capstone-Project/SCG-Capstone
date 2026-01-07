<?php

namespace App\Exports;

use App\Models\Shipment;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ShipmentsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function query()
    {
        // Eager load relationships to prevent N+1 queries and null reference errors
        return $this->query->with(['customer', 'supplier']);
    }

    public function headings(): array
    {
        return [
            'ID',
            'Customer PO',
            'Customer',
            'Supplier',
            'Status',
            'Type',
            'SCG PO',
            'SCG SO',
            'Booking Number',
            'Delivery Note Number',
            'Supplier Invoice',
            'ETD Port',
            'ETA Port',
            'ATA Port',
            'Delivery Schedule',
            'ATA Customer',
            'OTD Status',
            'Shipping Cost',
            'Customs Cost',
            'Other Costs',
            'Total Cost',
            'Notes',
        ];
    }

    public function map($shipment): array
    {
        $otdStatus = 'Pending';
        if ($shipment->isDelivered()) {
            $baseStatus = $shipment->isOnTime() ? 'On-Time' : 'Late';
            $daysText = $shipment->getDaysDifferenceText();

            if ($daysText && $daysText !== 'On schedule') {
                $otdStatus = "{$baseStatus} ({$daysText})";
            } else {
                $otdStatus = $baseStatus;
            }
        }

        return [
            $shipment->id,
            $shipment->customer_po ?? '',
            $shipment->customer ? $shipment->customer->name : 'N/A',
            $shipment->supplier ? $shipment->supplier->name : 'N/A',
            $shipment->status ?? '',
            $shipment->type ?? '',
            $shipment->scg_po ?? '',
            $shipment->scg_so ?? '',
            $shipment->booking_number ?? '',
            $shipment->delivery_note_number ?? '',
            $shipment->supplier_invoice ?? '',
            $shipment->etd_port?->format('Y-m-d') ?? '',
            $shipment->eta_port?->format('Y-m-d') ?? '',
            $shipment->ata_port?->format('Y-m-d') ?? '',
            $shipment->customer_receiving_schedule?->format('Y-m-d') ?? '',
            $shipment->ata_customer?->format('Y-m-d') ?? '',
            $otdStatus,
            $shipment->shipping_cost ?? 0,
            $shipment->customs_cost ?? 0,
            $shipment->other_costs ?? 0,
            $shipment->total_cost ?? 0,
            $shipment->notes ?? '',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true]],
        ];
    }
}
