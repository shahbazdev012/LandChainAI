<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Services\HashChain\HashChainService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PublicVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_verify_page_is_publicly_accessible(): void
    {
        $this->get(route('verify'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('verify/Index')->where('result', null));
    }

    public function test_a_property_can_be_verified_by_its_number(): void
    {
        $property = Property::factory()->create(['property_number' => 'LHR-1234-56789']);
        app(HashChainService::class)->append($property);

        $this->get(route('verify', ['query' => 'LHR-1234-56789']))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('verify/Index')
                ->where('result.found', true)
                ->where('result.block_valid', true)
                ->where('result.property.property_number', 'LHR-1234-56789'));
    }

    public function test_a_property_can_be_verified_by_its_block_hash(): void
    {
        $property = Property::factory()->create();
        $block = app(HashChainService::class)->append($property);

        $this->get(route('verify', ['query' => $block->hash]))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->where('result.found', true));
    }

    public function test_owner_cnic_is_masked_in_public_results(): void
    {
        $property = Property::factory()->create([
            'property_number' => 'LHR-0001-00001',
            'owner_cnic' => '35201-1234567-8',
        ]);
        app(HashChainService::class)->append($property);

        $this->get(route('verify', ['query' => 'LHR-0001-00001']))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->where(
                'result.property.owner_cnic',
                fn (string $cnic) => str_contains($cnic, '•') && str_ends_with($cnic, '5678'),
            ));
    }

    public function test_unknown_query_returns_not_found(): void
    {
        $this->get(route('verify', ['query' => 'does-not-exist']))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->where('result.found', false));
    }
}
