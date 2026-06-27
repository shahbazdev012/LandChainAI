<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\PropertyStatus;
use App\Enums\VerificationStatus;
use App\Models\Property;
use App\Models\PropertyVerification;
use App\Models\User;
use App\Services\HashChain\HashChainService;
use Illuminate\Database\Seeder;

class PropertySeeder extends Seeder
{
    public function run(HashChainService $chain): void
    {
        if (Property::query()->exists()) {
            return;
        }

        $registrar = User::query()->where('email', 'admin@admin.com')->firstOrFail();

        // A realistic spread of lifecycle states for the dashboard & search demo.
        $distribution = [
            PropertyStatus::Verified->value => 5,
            PropertyStatus::AwaitingApproval->value => 2,
            PropertyStatus::Pending->value => 3,
            PropertyStatus::Suspicious->value => 2,
            PropertyStatus::Rejected->value => 2,
        ];

        foreach ($distribution as $status => $count) {
            $status = PropertyStatus::from($status);

            Property::factory()
                ->count($count)
                ->status($status)
                ->state([
                    'registered_by' => $registrar->id,
                    'verified_at' => $status === PropertyStatus::Verified ? now() : null,
                ])
                ->create()
                ->each(function (Property $property) use ($chain, $status): void {
                    // Seal every property into the hash chain in creation order.
                    $chain->append($property);

                    // Properties that have been through the automated check get a
                    // verification record. "Awaiting approval" passed the check
                    // (Verified outcome) but has no human sign-off yet.
                    $outcome = $this->verificationOutcome($status);

                    if ($outcome !== null) {
                        // bind to the property via for() so the factory does not
                        // resolve its default property_id (which would create a
                        // stray Property for every verification record).
                        PropertyVerification::factory()
                            ->for($property)
                            ->status($outcome)
                            ->create(['run_by' => $property->registered_by]);
                    }
                });
        }
    }

    private function verificationOutcome(PropertyStatus $status): ?VerificationStatus
    {
        return match ($status) {
            PropertyStatus::Verified, PropertyStatus::AwaitingApproval => VerificationStatus::Verified,
            PropertyStatus::Suspicious => VerificationStatus::Suspicious,
            PropertyStatus::Rejected => VerificationStatus::Rejected,
            default => null,
        };
    }
}
