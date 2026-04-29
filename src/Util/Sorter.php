<?php

declare(strict_types=1);

/**
 * Bit&Black Hyphenizer SDK.
 *
 * @author Tobias Köngeter
 * @copyright Copyright © Bit&Black
 * @link https://www.bitandblack.com
 * @license MIT
 */

namespace BitAndBlack\Hyphenizer\Sdk\Util;

use BitAndBlack\Hyphenizer\Sdk\Api\Word;

/**
 * @internal
 */
class Sorter
{
    public static function sortWordItems(Word $itemA, Word $itemB): int
    {
        return strcmp(
            $itemA->getHyphenation(),
            $itemB->getHyphenation()
        );
    }

    public static function sortWords(string|int $itemA, string|int $itemB): int
    {
        return strcasecmp(
            (string) $itemA,
            (string) $itemB
        );
    }
}
