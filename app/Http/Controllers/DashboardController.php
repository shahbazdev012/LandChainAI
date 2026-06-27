<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\PropertyStatus;
use App\Http\Resources\PropertyResource;
use App\Models\Property;
use App\Services\HashChain\HashChainService;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(HashChainService $chain): Response
    {
        $counts = Property::query()
            ->selectRaw('status, count(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $recent = Property::query()
            ->with('latestVerification')
            ->latest()
            ->limit(6)
            ->get();

        return Inertia::render('Dashboard', [
            'stats' => [
                'total' => (int) $counts->sum(),
                'verified' => (int) $counts->get(PropertyStatus::Verified->value, 0),
                // "In review" = awaiting the automated check or a human approval.
                'pending' => (int) $counts->get(PropertyStatus::Pending->value, 0)
                    + (int) $counts->get(PropertyStatus::AwaitingApproval->value, 0),
                'flagged' => (int) $counts->get(PropertyStatus::Suspicious->value, 0)
                    + (int) $counts->get(PropertyStatus::Rejected->value, 0),
            ],
            'chain' => $chain->verify()->toArray(),
            'recent' => PropertyResource::collection($recent),
        ]);
    }
}
