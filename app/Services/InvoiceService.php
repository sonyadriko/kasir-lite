<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Outlet;
use App\Models\InvoiceSequence;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    /**
     * Generate next invoice number for outlet
     * Format: {OUTLET_CODE}/{YYYYMM}/{4-digit}
     */
    public function nextNumber(Outlet $outlet, Carbon $soldAt): string
    {
        $period = $soldAt->format('Ym'); // YYYYMM format

        // Lock the sequence row and get/increment the number
        $sequence = DB::transaction(function () use ($outlet, $period) {
            $sequence = InvoiceSequence::where('outlet_id', $outlet->id)
                ->where('period', $period)
                ->lockForUpdate()
                ->first();

            if (!$sequence) {
                $sequence = InvoiceSequence::create([
                    'outlet_id' => $outlet->id,
                    'period' => $period,
                    'last_number' => 1,
                ]);
                $nextNumber = 1;
            } else {
                $nextNumber = $sequence->last_number + 1;
                $sequence->update(['last_number' => $nextNumber]);
            }

            return ['sequence' => $sequence, 'number' => $nextNumber];
        });

        // Format: CBB/202509/0042
        return sprintf(
            '%s/%s/%04d',
            $outlet->code,
            substr($period, 0, 4) . substr($period, 4, 2), // YYYYMM to YYYY MM then concat
            $sequence['number']
        );
    }
}