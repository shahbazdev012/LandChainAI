<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\PropertyStatus;
use App\Models\Property;
use App\Models\User;
use App\Services\HashChain\HashChainService;
use Illuminate\Database\Seeder;

class PropertySeeder extends Seeder
{
    /**
     * Curated, realistic demo records (Pakistani housing societies / colonies).
     * Approved records have a matching deed image under public/demo/ so the
     * public verification flow can be demonstrated end-to-end.
     *
     * @return list<array<string, mixed>>
     */
    private function records(): array
    {
        return [
            // ----- Approved (live ground truth, sealed into the chain) -----
            [
                'property_number' => 'DHA-5C-1207', 'title' => '1 Kanal Residential — DHA Phase 5',
                'type' => 'residential', 'owner_name' => 'Imran Yousaf', 'owner_cnic' => '35201-1234567-1',
                'owner_contact' => '0300-1234567', 'address' => 'House 1207, Street 12, Block C, DHA Phase 5',
                'city' => 'Lahore', 'province' => 'Punjab', 'area_value' => 1, 'area_unit' => 'kanal',
                'status' => PropertyStatus::Approved,
            ],
            [
                'property_number' => 'BTK-P4-0889', 'title' => '250 sq.yd Residential — Bahria Town Karachi',
                'type' => 'residential', 'owner_name' => 'Sana Riaz', 'owner_cnic' => '42101-7654321-2',
                'owner_contact' => '0321-7654321', 'address' => 'Plot 889, Precinct 4, Bahria Town',
                'city' => 'Karachi', 'province' => 'Sindh', 'area_value' => 250, 'area_unit' => 'sqyd',
                'status' => PropertyStatus::Approved,
            ],
            [
                'property_number' => 'GLB3-MB-045', 'title' => 'Commercial Shop — Gulberg III',
                'type' => 'commercial', 'owner_name' => 'Tariq Mehmood', 'owner_cnic' => '35202-2233445-6',
                'owner_contact' => '0333-2233445', 'address' => 'Shop 45, Main Boulevard, Gulberg III',
                'city' => 'Lahore', 'province' => 'Punjab', 'area_value' => 8, 'area_unit' => 'marla',
                'status' => PropertyStatus::Approved,
            ],
            [
                'property_number' => 'F11-3-220', 'title' => '10 Marla House — F-11/3 Islamabad',
                'type' => 'residential', 'owner_name' => 'Ayesha Khan', 'owner_cnic' => '61101-9988776-5',
                'owner_contact' => '0345-9988776', 'address' => 'House 220, Street 9, F-11/3',
                'city' => 'Islamabad', 'province' => 'ICT', 'area_value' => 10, 'area_unit' => 'marla',
                'status' => PropertyStatus::Approved,
            ],
            [
                'property_number' => 'JT-G4-512', 'title' => '5 Marla House — Johar Town',
                'type' => 'residential', 'owner_name' => 'Bilal Ahmed', 'owner_cnic' => '35202-5566778-9',
                'owner_contact' => '0301-5566778', 'address' => 'House 512, Block G4, Johar Town',
                'city' => 'Lahore', 'province' => 'Punjab', 'area_value' => 5, 'area_unit' => 'marla',
                'status' => PropertyStatus::Approved,
            ],

            // ----- Pending approval (entered by Data Entry, awaiting Officer) -----
            [
                'property_number' => 'BEI-A-145', 'title' => '7 Marla Plot — Bahria Enclave',
                'type' => 'plot', 'owner_name' => 'Hamza Sattar', 'owner_cnic' => '61101-1122334-7',
                'owner_contact' => '0346-1122334', 'address' => 'Plot 145, Sector A, Bahria Enclave',
                'city' => 'Islamabad', 'province' => 'ICT', 'area_value' => 7, 'area_unit' => 'marla',
                'status' => PropertyStatus::PendingApproval,
            ],
            [
                'property_number' => 'DHA8-Z-023', 'title' => '500 sq.yd Plot — DHA Phase 8',
                'type' => 'plot', 'owner_name' => 'Mariam Shah', 'owner_cnic' => '42201-3344556-8',
                'owner_contact' => '0322-3344556', 'address' => 'Plot 23, Zone Z, DHA Phase 8',
                'city' => 'Karachi', 'province' => 'Sindh', 'area_value' => 500, 'area_unit' => 'sqyd',
                'status' => PropertyStatus::PendingApproval,
            ],
            [
                'property_number' => 'WT-J1-077', 'title' => '10 Marla House — Wapda Town',
                'type' => 'residential', 'owner_name' => 'Usman Ghani', 'owner_cnic' => '35202-7788990-1',
                'owner_contact' => '0302-7788990', 'address' => 'House 77, Block J1, Wapda Town',
                'city' => 'Lahore', 'province' => 'Punjab', 'area_value' => 10, 'area_unit' => 'marla',
                'status' => PropertyStatus::PendingApproval,
            ],

            // ----- Rejected (needs Officer correction) -----
            [
                'property_number' => 'SDR-C-012', 'title' => 'Commercial Unit — Saddar',
                'type' => 'commercial', 'owner_name' => 'Kashif Noor', 'owner_cnic' => '42301-4455667-2',
                'owner_contact' => '0323-4455667', 'address' => 'Shop 12, Saddar Bazaar',
                'city' => 'Karachi', 'province' => 'Sindh', 'area_value' => 6, 'area_unit' => 'marla',
                'status' => PropertyStatus::Rejected,
            ],
            [
                'property_number' => 'HYT-5-301', 'title' => '10 Marla House — Hayatabad Phase 5',
                'type' => 'residential', 'owner_name' => 'Noman Iqbal', 'owner_cnic' => '17301-6677889-3',
                'owner_contact' => '0341-6677889', 'address' => 'House 301, Phase 5, Hayatabad',
                'city' => 'Peshawar', 'province' => 'KPK', 'area_value' => 10, 'area_unit' => 'marla',
                'status' => PropertyStatus::Rejected,
            ],
        ];
    }

    public function run(HashChainService $chain): void
    {
        if (Property::query()->exists()) {
            return;
        }

        $clerk = User::query()->where('email', 'dataentry@landchain.test')->firstOrFail();
        $officer = User::query()->where('email', 'officer@landchain.test')->firstOrFail();

        foreach ($this->records() as $record) {
            $approved = $record['status'] === PropertyStatus::Approved;

            $property = Property::query()->create([
                ...$record,
                'created_by' => $clerk->id,
                'approved_by' => $approved ? $officer->id : null,
                'approved_at' => $approved ? now() : null,
            ]);

            // Approved records become live ground truth → sealed into the chain.
            if ($approved) {
                $chain->append($property);
            }
        }
    }
}
