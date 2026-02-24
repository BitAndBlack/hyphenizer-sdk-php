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

readonly class WordPayload implements PayloadInterface
{
    /**
     * @param array<int, Word> $word
     */
    public function __construct(
        private array $word,
    ) {
    }

    /**
     * @return array<int, Word>
     */
    public function jsonSerialize(): array
    {
        return $this->getWord();
    }

    /**
     * @return array<int, Word>
     */
    public function getWord(): array
    {
        return $this->word;
    }
}
