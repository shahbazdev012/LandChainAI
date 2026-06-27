<?php

namespace Tests\Feature;

use App\Enums\VerificationStatus;
use App\Models\Property;
use App\Models\PropertyDocument;
use App\Models\PropertyVerification;
use App\Services\HashChain\HashChainService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PropertyShowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Guards against the nested-resource double-wrapping regression, where
     * single resources serialised as {"data": {...}} and collections as
     * {"data": [...]}, breaking the Show page (e.g. block.hash / status.color).
     */
    public function test_show_page_props_have_unwrapped_nested_resources(): void
    {
        $property = Property::factory()->verified()->create();
        app(HashChainService::class)->append($property);
        PropertyVerification::factory()->for($property)->status(VerificationStatus::Verified)->create();
        PropertyDocument::factory()->for($property)->create();

        $this->actingAs($this->officer())
            ->get(route('properties.show', $property))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('properties/Show')
                // block is a flat object, not { data: {...} }
                ->has('property.block.hash')
                ->has('property.block.previous_hash')
                // latest_verification status is reachable at the top level
                ->where('property.latest_verification.status.value', 'verified')
                ->has('property.latest_verification.score')
                // collections are plain arrays, not { data: [...] }
                ->has('property.documents', 1)
                ->has('property.documents.0.original_name')
                ->has('property.verifications', 1)
                ->where('blockValid', true)
            );
    }
}
