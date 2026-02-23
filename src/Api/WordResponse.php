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

use Fig\Http\Message\StatusCodeInterface;
use JsonSerializable;

readonly class WordResponse implements ResponseInterface, JsonSerializable
{
    /**
     * @param int<100, 599> $statusCode
     * @param array<int, string> $messages
     */
    public function __construct(
        private int $statusCode = StatusCodeInterface::STATUS_OK,
        private array $messages = [],
        private WordPayload|null $wordPayload = null,
    ) {
    }

    /**
     * @return array{
     *     status: int<100, 599>,
     *     messages: array<int, string>,
     *     payload: WordPayload|null,
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'status' => $this->getStatusCode(),
            'messages' => $this->getMessages(),
            'payload' => $this->getPayload(),
        ];
    }

    /**
     * @return int<100, 599>
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @return array<int, string>
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    public function getPayload(): WordPayload|null
    {
        return $this->wordPayload;
    }
}
