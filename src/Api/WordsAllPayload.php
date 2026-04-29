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

readonly class WordsAllPayload implements PayloadInterface
{
    /**
     * @param array<non-empty-string|int, array<int, Word>> $words
     */
    public function __construct(
        private array $words,
    ) {
    }

    /**
     * @return array<non-empty-string|int, array<int, Word>>
     */
    public function jsonSerialize(): array
    {
        return $this->getWords();
    }

    /**
     * @return array<non-empty-string|int, array<int, Word>>
     */
    public function getWords(): array
    {
        return $this->words;
    }
}
