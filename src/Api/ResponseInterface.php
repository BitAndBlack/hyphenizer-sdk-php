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

interface ResponseInterface
{
    /**
     * @return array{
     *     status: int<100, 599>,
     *     messages: array<int, string>,
     *     payload: WordPayload|null,
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