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

namespace BitAndBlack\Hyphenizer\Sdk\Api;

readonly class EmptyPayload implements PayloadInterface
{
    /**
     * @return array<empty>
     */
    public function jsonSerialize(): array
    {
        return [];
    }
}
