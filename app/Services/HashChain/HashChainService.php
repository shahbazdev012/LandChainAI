<?php

declare(strict_types=1);

namespace App\Services\HashChain;

use App\Models\Property;
use App\Models\PropertyBlock;
use Illuminate\Support\Facades\DB;

/**
 * Blockchain-inspired hash chain.
 *
 * Each registered property is sealed into an append-only ledger as a "block".
 * A block stores an immutable snapshot of the property data plus a SHA-256 hash
 * computed over that snapshot AND the previous block's hash. Because every block
 * references its predecessor, tampering with any block (or its data) breaks the
 * chain from that point on — which {@see verify()} detects by recomputing hashes.
 *
 * This is a teaching/portfolio model of blockchain integrity, not a distributed
 * ledger or proof-of-work system.
 */
class HashChainService
{
    /**
     * Seal a property into the chain. Idempotent: a property already on the
     * chain returns its existing block.
     */
    public function append(Property $property): PropertyBlock
    {
        return DB::transaction(function () use ($property): PropertyBlock {
            if ($existing = $property->block()->first()) {
                return $existing;
            }

            $last = PropertyBlock::query()
                ->orderByDesc('sequence')
                ->lockForUpdate()
                ->first();

            $sequence = $last ? $last->sequence + 1 : 1;
            $previousHash = $last ? $last->hash : PropertyBlock::GENESIS_HASH;
            $createdAt = now();
            $data = $this->snapshot($property);

            $hash = $this->hash($sequence, $property->id, $data, $previousHash, $createdAt->toIso8601String());

            return PropertyBlock::query()->create([
                'sequence' => $sequence,
                'property_id' => $property->id,
                'hash' => $hash,
                'previous_hash' => $previousHash,
                'data' => $data,
                'created_at' => $createdAt,
            ]);
        });
    }

    /**
     * Walk the entire chain and confirm every block's hash and link are valid.
     */
    public function verify(): ChainReport
    {
        $blocks = PropertyBlock::query()->orderBy('sequence')->get();

        $issues = [];
        $brokenAt = null;
        $expectedPrevious = PropertyBlock::GENESIS_HASH;
        $expectedSequence = 1;

        foreach ($blocks as $block) {
            if ($block->sequence !== $expectedSequence) {
                $issues[] = "Sequence gap detected at block {$block->sequence}.";
                $brokenAt ??= $block->sequence;
            }

            if (! hash_equals($expectedPrevious, $block->previous_hash)) {
                $issues[] = "Block {$block->sequence} does not link to its predecessor.";
                $brokenAt ??= $block->sequence;
            }

            if (! $this->isHashValid($block)) {
                $issues[] = "Block {$block->sequence} hash does not match its contents (tampering).";
                $brokenAt ??= $block->sequence;
            }

            $expectedPrevious = $block->hash;
            $expectedSequence = $block->sequence + 1;
        }

        return new ChainReport(
            intact: $issues === [],
            blocks: $blocks->count(),
            brokenAtSequence: $brokenAt,
            issues: $issues,
        );
    }

    /**
     * Recompute a single block's hash from its stored snapshot and confirm it
     * matches the stored hash.
     */
    public function isHashValid(PropertyBlock $block): bool
    {
        $recomputed = $this->hash(
            $block->sequence,
            $block->property_id,
            $block->data,
            $block->previous_hash,
            $block->created_at?->toIso8601String() ?? '',
        );

        return hash_equals($block->hash, $recomputed);
    }

    /**
     * The immutable, hashed representation of a property at registration time.
     *
     * @return array<string, mixed>
     */
    public function snapshot(Property $property): array
    {
        return [
            'property_number' => $property->property_number,
            'title' => $property->title,
            'type' => $property->type->value,
            'owner_name' => $property->owner_name,
            'owner_cnic' => $property->owner_cnic,
            'address' => $property->address,
            'city' => $property->city,
            'province' => $property->province,
            'area_value' => $property->area_value,
            'area_unit' => $property->area_unit->value,
            'registered_by' => $property->registered_by,
        ];
    }

    /**
     * Deterministic SHA-256 over the block contents and its predecessor hash.
     *
     * @param  array<string, mixed>  $data
     */
    public function hash(int $sequence, int $propertyId, array $data, string $previousHash, string $createdAt): string
    {
        $payload = json_encode([
            'sequence' => $sequence,
            'property_id' => $propertyId,
            // Canonicalised so the hash is independent of key ordering — the DB
            // JSON column does not preserve insertion order.
            'data' => $this->canonicalize($data),
            'previous_hash' => $previousHash,
            'created_at' => $createdAt,
        ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        return hash('sha256', $payload);
    }

    /**
     * Recursively sort array keys to produce a stable, order-independent shape.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function canonicalize(array $data): array
    {
        ksort($data);

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->canonicalize($value);
            }
        }

        return $data;
    }
}
