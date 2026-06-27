<?php

namespace Tests\Unit;

use App\Models\Property;
use App\Models\PropertyBlock;
use App\Services\HashChain\HashChainService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HashChainServiceTest extends TestCase
{
    use RefreshDatabase;

    private function chain(): HashChainService
    {
        return app(HashChainService::class);
    }

    public function test_first_block_links_to_genesis_and_subsequent_blocks_link_together(): void
    {
        $chain = $this->chain();

        $first = $chain->append(Property::factory()->create());
        $second = $chain->append(Property::factory()->create());

        $this->assertSame(1, $first->sequence);
        $this->assertSame(PropertyBlock::GENESIS_HASH, $first->previous_hash);

        $this->assertSame(2, $second->sequence);
        $this->assertSame($first->hash, $second->previous_hash);
    }

    public function test_appending_is_idempotent_per_property(): void
    {
        $chain = $this->chain();
        $property = Property::factory()->create();

        $a = $chain->append($property);
        $b = $chain->append($property);

        $this->assertSame($a->id, $b->id);
        $this->assertSame(1, PropertyBlock::count());
    }

    public function test_an_intact_chain_verifies_successfully(): void
    {
        $chain = $this->chain();
        Property::factory()->count(5)->create()->each(fn (Property $p) => $chain->append($p));

        $report = $chain->verify();

        $this->assertTrue($report->intact);
        $this->assertSame(5, $report->blocks);
        $this->assertNull($report->brokenAtSequence);
    }

    public function test_tampering_with_block_data_is_detected(): void
    {
        $chain = $this->chain();
        Property::factory()->count(4)->create()->each(fn (Property $p) => $chain->append($p));

        $block = PropertyBlock::where('sequence', 2)->firstOrFail();
        $data = $block->data;
        $data['owner_name'] = 'Tampered Name';
        $block->update(['data' => $data]);

        $report = $chain->verify();

        $this->assertFalse($report->intact);
        $this->assertSame(2, $report->brokenAtSequence);
    }
}
