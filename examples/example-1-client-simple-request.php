<?php

/**
 * Bit&Black Hyphenizer SDK.
 *
 * @author Tobias Köngeter
 * @copyright Copyright © Bit&Black
 * @link https://www.bitandblack.com
 * @license MIT
 */

use BitAndBlack\Hyphenizer\Sdk\HyphenizerClient;

require_once dirname(__FILE__, 2) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

$hyphenizerClient = new HyphenizerClient('your_token');

$wordsToHyphenated = [
    'Bodensee',
    'Bodenseefelchen',
];

$wordsHyphenated = $hyphenizerClient->getWordsHyphenated($wordsToHyphenated);

/**
 * This will dump:
 *
 * array(2) {
 *     ["Bodensee"] => string(9) "Boden|see"
 *     ["Bodenseefelchen"] => string(16) "Bodensee|felchen"
 * }
 */
var_dump($wordsHyphenated);
