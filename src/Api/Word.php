<?php

/**
 * Bit&Black Hyphenizer SDK.
 *
 * @author Tobias Köngeter
 * @copyright Copyright © Bit&Black
 * @link https://www.bitandblack.com
 * @license MIT
 */

namespace BitAndBlack\Hyphenizer\Sdk\Api;

use JsonSerializable;

readonly class Word implements JsonSerializable
{
    /**
     * @param non-empty-string $hyphenation
     * @param int<0, 100> $score
     */
    public function __construct(
        private string $hyphenation,
        private int $score,
        private bool $isApproved,
        private bool $hasTypo,
    ) {
    }

    /**
     * @return array{
     *     hyphenation: non-empty-string,
     *     score: int<0, 100>,
     *     approved: bool,
     *     hasTypo: bool,
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'hyphenation' => $this->getHyphenation(),
            'score' => $this->getScore(),
            'approved' => $this->isApproved(),
            'hasTypo' => $this->hasTypo(),
        ];
    }

    /**
     * @return non-empty-string
     */
    public function getHyphenation(): string
    {
        return $this->hyphenation;
    }

    /**
     * @return int<0, 100>
     */
    public function getScore(): int
    {
        return $this->score;
    }

    public function isApproved(): bool
    {
        return $this->isApproved;
    }

    public function hasTypo(): bool
    {
        return $this->hasTypo;
    }
}
