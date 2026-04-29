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

use JsonSerializable;

interface ResponseInterface extends JsonSerializable
{
    /**
     * @return array{
     *     status: int<100, 599>,
     *     messages: array<int, string>,
     *     payload: PayloadInterface|null,
     * }
     */
    public function jsonSerialize(): array;

    /**
     * @return int<100, 599>
     */
    public function getStatusCode(): int;

    /**
     * @return array<int, string>
     */
    public function getMessages(): array;

    public function getPayload(): PayloadInterface|null;
}
