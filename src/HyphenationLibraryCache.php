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
use DateTimeImmutable;
use DateTimeInterface;

class HyphenationLibraryCache implements HyphenationLibraryCacheInterface
{
    /**
     * @var array<non-empty-string|int, non-empty-string|null>
     */
    private array $words = [];

    /**
     * @var array<non-empty-string|int, array<int, Word>>
     */
    private array $wordsDetails = [];

    private DateTimeInterface|null $dateTimeLibraryUpdated = null;

    /**
     * @return array{
     *     words: array<non-empty-string|int, non-empty-string|null>,
     *     wordsDetails: array<non-empty-string|int, array<int, Word>>,
     *     dateTimeLibraryUpdated: DateTimeInterface|null,
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'words' => $this->getWords(),
            'wordsDetails' => $this->getWordsDetails(),
            'dateTimeLibraryUpdated' => $this->getDateTimeLibraryUpdated(),
        ];
    }

    /**
     * @inheritDoc
     */
    public function getWords(): array
    {
        return $this->words;
    }

    /**
     * @inheritDoc
     */
    public function setWords(array $words): self
    {
        $this->words = $words;

        uksort(
            $this->words,
            static fn (string|int $itemA, string|int $itemB): int => strcasecmp(
                (string) $itemA,
                (string) $itemB
            )
        );

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getDateTimeLibraryUpdated(): DateTimeInterface|null
    {
        return $this->dateTimeLibraryUpdated;
    }

    /**
     * @inheritDoc
     */
    public function addWordDetails(string|int $word, Word ...$wordDetails): self
    {
        $wordDetails = array_values($wordDetails);

        usort(
            $wordDetails,
            static fn (Word $itemA, Word $itemB): int => strcmp(
                $itemA->getHyphenation(),
                $itemB->getHyphenation()
            )
        );

        $this->wordsDetails[$word] = $wordDetails;

        uksort(
            $this->wordsDetails,
            static fn (string|int $itemA, string|int $itemB): int => strcasecmp(
                (string) $itemA,
                (string) $itemB
            )
        );

        /**
         * If the hyphenated word already exists in the simple words list, but is null,
         * we assume, that this word has been collected but not hyphenized yet.
         * So we remove it, to keep this list as short as possible.
         * Words from this list having a hyphenation won't get remove, as this is a manually added,
         * individual hyphenation.
         */
        if (true === array_key_exists($word, $this->words) && null === $this->words[$word]) {
            unset($this->words[$word]);
        }

        $this->updateDateTimeLibraryUpdated();
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getWordsDetails(): array
    {
        return $this->wordsDetails;
    }

    private function updateDateTimeLibraryUpdated(): self
    {
        $this->dateTimeLibraryUpdated = new DateTimeImmutable('now');
        return $this;
    }
}
