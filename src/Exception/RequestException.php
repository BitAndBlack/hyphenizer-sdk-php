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
    public function __construct(string $message, Throwable|null $previous = null)
    {
        parent::__construct($message, $previous);
    }
}
