<?php

/**
 * Bit&Black Hyphenizer SDK.
 *
 * @author Tobias Köngeter
 * @copyright Copyright © Bit&Black
 * @link https://www.bitandblack.com
 * @license MIT
 */

namespace BitAndBlack\Hyphenizer\Sdk;

use BitAndBlack\Hyphenizer\Sdk\Api\Word;
use DateTimeInterface;
use JsonSerializable;

interface HyphenationLibraryCacheInterface extends JsonSerializable
{
    /**
     * Returns a simple list of all existing words and their hyphenation.
     * It may look like this:
     *
     * ```
     * [
     *     'Bodensee' => 'Boden|see',
     *     'Bodenseefelchen' => 'Bodensee|felchen',
     * ]
     * ```
     *
     * @return array<non-empty-string, non-empty-string|null>
     */
    public function getWords(): array;

    /**
     * Set a list of words and their hyphenations to the library.
     * **Attention**: This overrides the existing library entirely.
     *
     * The list may contain hyphenations, but also currently missing ones:
     *
     * ```
     * [
     *     'Bodensee' => 'Boden|see',
     *     'Bodenseefelchen' => null,
     * ]
     * ```
     *
     * @param array<non-empty-string, non-empty-string|null> $words
     * @return $this
     */
    public function setWords(array $words): self;

    /**
     * Tells when the library has been updated last.
     */
    public function getDateTimeLibraryUpdated(): DateTimeInterface|null;

    /**
     * Updates the date when the library was last updated.
     */
    public function updateDateTimeLibraryUpdated(): self;

    /**
     * Adds a word and its details to the hyphenation library.
     *
     * @param non-empty-string $word
     * @return $this
     */
    public function addWordDetails(string $word, Word ...$wordDetails): self;

    /**
     * Provides detailed information about a specific word — when it exists in the library.
     * The returned array contains a list of {@see Word}s. There's normally only one possibility to
     * hyphenate a word, but there are few words, where multiple possibilities exist.
     *
     * @return array<non-empty-string, array<int, Word>>
     */
    public function getWordsDetails(): array;
}
