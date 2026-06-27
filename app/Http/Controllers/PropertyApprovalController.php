<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\PropertyStatus;
use App\Models\Property;
use Illuminate\Http\RedirectResponse;

/**
 * Human approval gate. After the automated check passes a property moves to
 * "awaiting approval"; a reviewer then approves (→ verified) or rejects it.
 */
class PropertyApprovalController extends Controller
{
    public function approve(Property $property): RedirectResponse
    {
        $this->authorize('approve', $property);

        if ($property->status !== PropertyStatus::AwaitingApproval) {
            return back()->with('error', 'Only properties awaiting approval can be approved.');
        }

        $property->update([
            'status' => PropertyStatus::Verified,
            'verified_at' => now(),
        ]);

        return back()->with('success', "Property {$property->property_number} approved and verified.");
    }

    public function reject(Property $property): RedirectResponse
    {
        $this->authorize('approve', $property);

        if ($property->status !== PropertyStatus::AwaitingApproval) {
            return back()->with('error', 'Only properties awaiting approval can be rejected.');
        }

        $property->update([
            'status' => PropertyStatus::Rejected,
            'verified_at' => null,
        ]);

        return back()->with('warning', "Property {$property->property_number} was rejected by reviewer.");
    }
}
