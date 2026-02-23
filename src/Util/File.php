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

class File implements FileInterface
{
    public function getWordsHyphenatedJsonFile(): string
    {
        return 'words-hyphenated.json';
    }
}
