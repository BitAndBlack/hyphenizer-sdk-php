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
     * Resets the library of hyphenated words. This overrides the existing library entirely.
     *
     * @param array<non-empty-string, non-empty-string|null> $wordsHyphenated
     * @return $this
     * @throws Exception
     */
    public function setHyphenationWords(array $wordsHyphenated, bool $saveList = true): self;

    /**
     * Adds one or more unhyphenated words to the library.
     *
     * @param array<int, non-empty-string> $words
     * @return $this
     * @throws Exception
     */
    public function addWords(array $words, bool $saveList = true): self;

    /**
     * Tells if the library exists.
     *
     * @return bool
     */
    public function isLibraryExisting(): bool;
}