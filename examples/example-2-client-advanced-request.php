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

$multipleWordsRequest = $hyphenizerClient->getMultipleWordsRequest($wordsToHyphenated);

/**
 * This will dump:
 *
 * object(BitAndBlack\Hyphenizer\Sdk\Api\WordsResponse) {
 *     ["status":"BitAndBlack\Hyphenizer\Sdk\Api\WordsResponse"] => int(200)
 *     ["messages":"BitAndBlack\Hyphenizer\Sdk\Api\WordsResponse"] => array(0) {}
 *     ["payload":"BitAndBlack\Hyphenizer\Sdk\Api\WordsResponse"] => object(BitAndBlack\Hyphenizer\Sdk\Api\WordsPayload) {
 *         ["words":"BitAndBlack\Hyphenizer\Sdk\Api\WordsPayload"] => array(2) {
 *             ["Bodensee"] => array(1) {
 *                 [0] => object(BitAndBlack\Hyphenizer\Sdk\Api\Word) {
 *                     ["hyphenation":"BitAndBlack\Hyphenizer\Sdk\Api\Word"] => string(9) "Boden|see"
 *                     ["score":"BitAndBlack\Hyphenizer\Sdk\Api\Word"] => int(100)
 *                     ["approved":"BitAndBlack\Hyphenizer\Sdk\Api\Word"] => bool(true)
 *                     ["hasTypo":"BitAndBlack\Hyphenizer\Sdk\Api\Word"] => bool(false)
 *                 }
 *             }
 *             ["Bodenseefelchen"] => array(1) {
 *                 [0] => object(BitAndBlack\Hyphenizer\Sdk\Api\Word) {
 *                     ["hyphenation":"BitAndBlack\Hyphenizer\Sdk\Api\Word"] => string(16) "Bodensee|felchen"
 *                     ["score":"BitAndBlack\Hyphenizer\Sdk\Api\Word"] => int(100)
 *                     ["approved":"BitAndBlack\Hyphenizer\Sdk\Api\Word"] => bool(true)
 *                     ["hasTypo":"BitAndBlack\Hyphenizer\Sdk\Api\Word"] => bool(false)
 *                 }
 *             }
 *         }
 *     }
 * }
 */
var_dump($multipleWordsRequest);
