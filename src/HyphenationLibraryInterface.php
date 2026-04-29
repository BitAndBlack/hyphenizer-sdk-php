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
use BitAndBlack\Hyphenizer\Sdk\Api\WordsResponse;
use DateTimeInterface;

interface HyphenationLibraryInterface
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
     * To request only a single word instead of all, {@see HyphenationLibraryInterface::getHyphenatedWord()} can be used.
     *
     * @return array<non-empty-string|int, non-empty-string|null>
     */
    public function getHyphenationWords(): array;

    /**
     * Returns a single hyphenation possibility of a word if it exists in the library.
     * For example: `Boden|see`
     *
     * To request all existing words at once, {@see HyphenationLibraryInterface::getHyphenationWords()} can be used.
     *
     * @return non-empty-string|null
     */
    public function getHyphenatedWord(string $word): string|null;

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
     * @param array<non-empty-string|int, non-empty-string|null> $wordsHyphenated
     * @return $this
     * @throws Exception
     */
    public function setHyphenationWords(array $wordsHyphenated, bool $saveLibrary = true): self;

    /**
     * Adds one or more unhyphenated words to the library.
     * This is a simple list of words that need to be hyphenated later:
     *
     * ```
     * [
     *     'Bodensee',
     *     'Bodenseefelchen',
     * ]
     * ```
     *
     * @param array<int, non-empty-string|int> $words
     * @return $this
     * @throws Exception
     */
    public function addWords(array $words, bool $saveLibrary = true): self;

    /**
     * Provides detailed information about all existing words in the library.
     * Each word contains a list of {@see Word}s. There's normally only one possibility to
     * hyphenate a word, but there are few words, where multiple possibilities exist.
     *
     * @return array<non-empty-string|int, array<int, Word>>
     */
    public function getWordsDetails(): array;

    /**
     * Provides detailed information about a specific word — when it exists in the library.
     * The returned array contains a list of {@see Word}s. There's normally only one possibility to
     * hyphenate a word, but there are few words, where multiple possibilities exist.
     *
     * @return array<int, Word>|null
     */
    public function getWordDetails(string $word): array|null;

    /**
     * Adds the words and their details from a {@see WordsResponse} to the hyphenation library.
     * This makes use of {@see HyphenationLibraryInterface::addWordDetails()} method, which can be used to add single words, too.
     *
     * @return $this
     */
    public function addDataFromApiWordsResponse(WordsResponse $wordsResponse, bool $saveLibrary = true): self;

    /**
     * Adds a word and its details to the hyphenation library.
     * To add the details from an API response, you can use the {@see HyphenationLibraryInterface::addDataFromApiWordsResponse()} method.
     *
     * @param non-empty-string|int $word
     * @return $this
     */
    public function addWordDetails(string|int $word, Word ...$wordDetails): self;

    /**
     * Tells if the library exists.
     */
    public function isLibraryExisting(): bool;

    /**
     * Saves the current state of the library to the file system.
     */
    public function saveLibrary(): bool;

    /**
     * Tells when the library has been updated last.
     * This refers to the date when the list of hyphenations was last updated – not when new words were added.
     * This date can then be used to decide whether the API should be contacted again.
     */
    public function getDateTimeLibraryUpdated(): DateTimeInterface|null;
}
