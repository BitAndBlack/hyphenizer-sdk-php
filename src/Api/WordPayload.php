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

use BitAndBlack\Hyphenizer\Sdk\Api\PayloadInterface;
use BitAndBlack\Hyphenizer\Sdk\Api\Word;
use JsonSerializable;

readonly class WordPayload implements PayloadInterface, JsonSerializable
{
    /**
     * @param array<string, array<int, Word>> $words
     */
    public function __construct(
        private array $words,
    ) {
    }

    /**
     * @return array<string, array<int, Word>>
     */
    public function jsonSerialize(): array
    {
        return $this->getWords();
    }

    /**
     * @return array<string, array<int, Word>>
     */
    public function getWords(): array
    {
        return $this->words;
    }
}
