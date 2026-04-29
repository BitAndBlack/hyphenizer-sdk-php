<?php

/**
 * Bit&Black Hyphenizer SDK.
 *
 * @author Tobias Köngeter
 * @copyright Copyright © Bit&Black
 * @link https://www.bitandblack.com
 * @license MIT
 */

namespace BitAndBlack\Hyphenizer\Sdk;

use BitAndBlack\Hyphenizer\Sdk\Api\WordsAllResponse;
use BitAndBlack\Hyphenizer\Sdk\Api\WordResponse;
use BitAndBlack\Hyphenizer\Sdk\Api\WordsResponse;
use BitAndBlack\Hyphenizer\Sdk\Exception\RequestException;

interface HyphenizerClientInterface
{
    /**
     * Request a single word.
     *
     * @param non-empty-string $word
     * @throws RequestException
     */
    public function getSingleWordRequest(string $word): WordResponse;

    /**
     * Request multiple words ot once.
     *
     * @param array<int, non-empty-string> $words
     * @throws RequestException
     */
    public function getMultipleWordsRequest(array $words): WordsResponse;

    /**
     * Request all existing words.
     *
     * @return WordsAllResponse
     */
    public function getWordsAllRequest(): WordsAllResponse;
}
