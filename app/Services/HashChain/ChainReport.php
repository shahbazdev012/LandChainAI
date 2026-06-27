<?php

declare(strict_types=1);

namespace App\Services\HashChain;

final readonly class ChainReport
{
    /**
     * @param  list<string>  $issues
     */
    public function __construct(
        public bool $intact,
        public int $blocks,
        public ?int $brokenAtSequence,
        public array $issues,
    ) {}

    /**
     * @return array{intact: bool, blocks: int, broken_at_sequence: int|null, issues: list<string>}
     */
    public function toArray(): array
    {
        return [
            'intact' => $this->intact,
            'blocks' => $this->blocks,
            'broken_at_sequence' => $this->brokenAtSequence,
            'issues' => $this->issues,
        ];
    }
}
