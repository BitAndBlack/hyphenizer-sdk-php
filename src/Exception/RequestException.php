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

namespace BitAndBlack\Hyphenizer\Sdk\Exception;

use BitAndBlack\Hyphenizer\Sdk\Exception;
use Throwable;

class RequestException extends Exception
{
    public function __construct(string|null $message = null, Throwable|null $previous = null)
    {
        parent::__construct(
            $message ?? 'Failed to request Hyphenizer API.',
            $previous
        );
    }

    public static function causeStatusCode(int $statusCode, Throwable|null $previous = null): self
    {
        return new self('Failed to request Hyphenizer API. (Response code is "' . $statusCode . '")', $previous);
    }

    public static function causeDecode(Throwable|null $previous = null): self
    {
        return new self('Failed to decode response.', $previous);
    }
}
