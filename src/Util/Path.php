<?php

/**
 * Bit&Black Hyphenizer SDK.
 *
 * @author Tobias Köngeter
 * @copyright Copyright © Bit&Black
 * @link https://www.bitandblack.com
 * @license MIT
 */

namespace BitAndBlack\Hyphenizer\Sdk\Util;

use BitAndBlack\Composer\VendorPath;

class Path implements PathInterface
{
    public function getRootFolder(): string
    {
        return dirname(new VendorPath());
    }

    public function getLibraryFolder(): string
    {
        return $this->getRootFolder() . DIRECTORY_SEPARATOR . 'library';
    }
}
