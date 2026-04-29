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

use Fig\Http\Message\StatusCodeInterface;

readonly class WordsAllResponse implements ResponseInterface
{
    /**
     * @param int<100, 599> $status
     * @param array<int, non-empty-string> $messages
     */
    public function __construct(
        private int $status = StatusCodeInterface::STATUS_OK,
        private array $messages = [],
        private WordsAllPayload|null $payload = null,
    ) {
    }

    /**
     * @return array{
     *     status: int<100, 599>,
     *     messages: array<int, non-empty-string>,
     *     payload: WordsAllPayload|null,
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
        return $this->status;
    }

    /**
     * @return array<int, non-empty-string>
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    public function getPayload(): WordsAllPayload|null
    {
        return $this->payload;
    }
}
