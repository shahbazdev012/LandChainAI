<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\PropertyStatus;
use App\Models\Property;
use App\Services\HashChain\HashChainService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

/**
 * Officer/Admin review of Data Entry submissions. Approving seals the record
 * into the blockchain-inspired hash chain (it becomes live ground truth).
 */
class PropertyApprovalController extends Controller
{
    public function approve(Property $property, HashChainService $chain): RedirectResponse
    {
        $this->authorize('approve', $property);

        if ($property->status !== PropertyStatus::PendingApproval) {
            return back()->with('error', 'Only properties pending approval can be approved.');
        }

        DB::transaction(function () use ($property, $chain): void {
            $property->update([
                'status' => PropertyStatus::Approved,
                'approved_by' => request()->user()->id,
                'approved_at' => now(),
            ]);

            $chain->append($property->refresh());
        });

        return back()->with('success', "Property {$property->property_number} approved and sealed into the chain.");
    }

    public function reject(Property $property): RedirectResponse
    {
        $this->authorize('approve', $property);

        if ($property->status !== PropertyStatus::PendingApproval) {
            return back()->with('error', 'Only properties pending approval can be rejected.');
        }

        $property->update(['status' => PropertyStatus::Rejected]);

        return back()->with('warning', "Property {$property->property_number} was rejected — an Officer can correct and re-approve it.");
    }
}
