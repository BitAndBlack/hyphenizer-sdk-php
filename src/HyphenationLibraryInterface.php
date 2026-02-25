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

interface HyphenationLibraryInterface
{
    /**
     * Returns a list of all existing words and their hyphenation.
     *
     * @return array<non-empty-string, non-empty-string|null>
     */
    public function getHyphenationWords(): array;

    /**
     * Returns a hyphenated word if it exists in the library.
     *
     * @param string $word
     * @return non-empty-string|null
     */
    public function getHyphenatedWord(string $word): string|null;

    /**
     * Resets the library of hyphenated words. This overrides the existing library entirely.
     *
     * @param array<non-empty-string, non-empty-string|null> $wordsHyphenated
     * @return $this
     * @throws Exception
     */
    public function setHyphenationWords(array $wordsHyphenated, bool $saveLibrary = true): self;

    /**
     * Adds one or more unhyphenated words to the library.
     *
     * @param array<int, non-empty-string> $words
     * @return $this
     * @throws Exception
     */
    public function addWords(array $words, bool $saveLibrary = true): self;

    /**
     * Tells if the library exists.
     *
     * @return bool
     */
    public function isLibraryExisting(): bool;

    /**
     * Saves the current state of the library to the file system.
     *
     * @return bool
     */
    public function saveLibrary(): bool;
}