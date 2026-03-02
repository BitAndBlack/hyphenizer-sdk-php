<?php

namespace BitAndBlack\Hyphenizer\Sdk;

use BitAndBlack\Hyphenizer\Sdk\Api\Word;
use DateTimeImmutable;
use DateTimeInterface;

class HyphenationLibraryCache implements HyphenationLibraryCacheInterface
{
    /**
     * @var array<non-empty-string, non-empty-string|null>
     */
    private array $words = [];

    /**
     * @var array<non-empty-string, array<int, Word>>
     */
    private array $wordsDetails = [];

    private DateTimeInterface|null $dateTimeLibraryUpdated = null;

    /**
     * @return array{
     *     words: array<non-empty-string, non-empty-string|null>,
     *     wordsDetails: array<non-empty-string, array<int, Word>>,
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
     * @return array<non-empty-string, non-empty-string|null>
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
        uksort($this->words, strcasecmp(...));
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
    public function addWordDetails(string $word, Word ...$wordDetails): self
    {
        $this->wordsDetails[$word] = array_values($wordDetails);
        $this->updateDateTimeLibraryUpdated();
        return $this;
    }

    /**
     * @inheritDoc
     * @return array<non-empty-string, array<int, Word>>
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
