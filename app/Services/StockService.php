<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockEntry;
use Illuminate\Support\Facades\DB;

class StockService
{
    public function registerEntry(array $data, ?int $receivedBy): StockEntry
    {
        return DB::transaction(function () use ($data, $receivedBy) {
            $entry = StockEntry::query()->create([
                'supplier_id' => $data['supplier_id'],
                'received_by' => $receivedBy,
                'reference' => $data['reference'] ?? null,
                'received_at' => $data['received_at'],
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($data['items'] as $item) {
                $entry->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'] ?? null,
                ]);

                Product::query()->whereKey($item['product_id'])->increment('stock', $item['quantity']);
            }

            return $entry->load(['items.product', 'supplier']);
        });
    }
}
