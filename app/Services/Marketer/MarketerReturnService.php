<?php

namespace App\Services\Marketer;

use App\Models\MarketerReturnRequest;
use App\Models\MarketerReturnItem;
use Illuminate\Support\Facades\DB;

class MarketerReturnService
{
    public function createReturn($marketerId, array $items, $notes = null)
    {
        return DB::transaction(function () use ($marketerId, $items, $notes) {
            $return = MarketerReturnRequest::create([
                'invoice_number' => $this->generateInvoiceNumber(),
                'marketer_id' => $marketerId,
                'status' => 'pending',
                'notes' => $notes,
            ]);

            foreach ($items as $item) {
                MarketerReturnItem::create([
                    'return_request_id' => $return->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                ]);
            }

            return $return->load('items.product');
        });
    }

    public function cancelReturn($returnId, $marketerId, $notes = null)
    {
        return DB::transaction(function () use ($returnId, $marketerId, $notes) {
            $return = MarketerReturnRequest::where('id', $returnId)
                ->where('marketer_id', $marketerId)
                ->whereIn('status', ['pending', 'approved'])
                ->firstOrFail();

            $return->update([
                'status' => 'cancelled',
                'notes' => $notes
            ]);

            return $return;
        });
    }

    private function generateInvoiceNumber()
    {
        return 'MRR-' . date('Ymd') . '-' . str_pad(MarketerReturnRequest::count() + 1, 5, '0', STR_PAD_LEFT);
    }
}
