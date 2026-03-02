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
use BitAndBlack\Hyphenizer\Sdk\Util\File;
use BitAndBlack\Hyphenizer\Sdk\Util\FileInterface;
use BitAndBlack\Hyphenizer\Sdk\Util\Path;
use Closure;
use CuyZ\Valinor\Mapper\Source\JsonSource;
use CuyZ\Valinor\MapperBuilder;
use CuyZ\Valinor\Normalizer\Format;
use CuyZ\Valinor\NormalizerBuilder;
use DateTimeInterface;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\FilesystemException;
use League\Flysystem\Local\LocalFilesystemAdapter;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Throwable;

class HyphenationLibrary implements HyphenationLibraryInterface, LoggerAwareInterface
{
    private LoggerInterface $logger;

    /**
     * @var Closure(string): string
     */
    private Closure $callbackFileReadAfter;

    /**
     * @var Closure(string): string
     */
    private Closure $callbackFileWriteBefore;

    private readonly Filesystem $filesystem;

    private HyphenationLibraryCacheInterface $hyphenationLibraryCache;

    public function __construct(
        FilesystemAdapter|null $filesystemAdapter = null,
        private readonly FileInterface $file = new File(),
    ) {
        $this->logger = new NullLogger();

        $adapter = $filesystemAdapter ?? new LocalFilesystemAdapter(
            (new Path())->getLibraryFolder()
        );

        $this->filesystem = new Filesystem($adapter);

        $this->callbackFileReadAfter = static fn (string $content): string => $content;
        $this->callbackFileWriteBefore = static fn (string $content): string => $content;

        /**
         * This loads the cached library, so it can be properly accessed.
         * The library may be encoded or compressed, what makes it unusable here,
         * as long as the callback don't have been set. This should be okay.
         */
        $this->hyphenationLibraryCache = $this->readFromFile();
    }

    /**
     * @inheritDoc
     */
    public function addDataFromApiWordsResponse(WordsResponse $wordsResponse, bool $saveLibrary = true): self
    {
        $payload = $wordsResponse->getPayload();
        $words = $payload?->getWords() ?? [];

        foreach ($words as $word => $wordHyphenations) {
            $this->addWordDetails($word, ...$wordHyphenations);
        }

        if (true === $saveLibrary) {
            $this->saveLibrary();
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addWordDetails(string $word, Word ...$wordDetails): self
    {
        $this->hyphenationLibraryCache->addWordDetails($word, ...$wordDetails);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getHyphenationWords(): array
    {
        $words = $this->hyphenationLibraryCache->getWords();

        foreach ($this->hyphenationLibraryCache->getWordsDetails() as $word => $wordDetails) {
            if (true === array_key_exists($word, $words)) {
                continue;
            }

            $words[$word] = $wordDetails[0]->getHyphenation();
        }

        return $words;
    }

    /**
     * @inheritDoc
     */
    public function setHyphenationWords(array $wordsHyphenated, bool $saveLibrary = true): self
    {
        $this->hyphenationLibraryCache->setWords($wordsHyphenated);

        if (true === $saveLibrary) {
            $this->saveLibrary();
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addWords(array $words, bool $saveLibrary = true): self
    {
        $words = array_combine(
            array_values($words),
            array_fill(0, count($words), null)
        );

        $wordsMerged = array_merge(
            $this->hyphenationLibraryCache->getWords(),
            $words
        );

        return $this->setHyphenationWords($wordsMerged, $saveLibrary);
    }

    /**
     * @inheritDoc
     */
    public function isLibraryExisting(): bool
    {
        try {
            return $this->filesystem->fileExists(
                $this->file->getWordsHyphenatedJsonFile(),
            );
        } catch (FilesystemException $filesystemException) {
            $this->logger->error($filesystemException->getMessage());
            return false;
        }
    }

    /**
     * Gets the callback that gets used after reading the list.
     * This can be used to decode or uncompress a file.
     *
     * @return Closure(string): string
     */
    public function getCallbackFileReadAfter(): Closure
    {
        return $this->callbackFileReadAfter;
    }

    /**
     * Defines the callback that gets used after reading the list.
     * This can be used to decode or uncompress a file.
     *
     * **Attention**: Setting a callback will cause the library to reload.
     *
     * @param Closure(string): string $callbackFileReadAfter
     * @return $this
     */
    public function setCallbackFileReadAfter(Closure $callbackFileReadAfter): self
    {
        $this->callbackFileReadAfter = $callbackFileReadAfter;

        /**
         * As the library may be encoded or compressed, we need to load it again, once the callback has been defined.
         */
        $this->hyphenationLibraryCache = $this->readFromFile();

        return $this;
    }

    /**
     * Gets the callback that gets used before writing the list.
     * This can be used to encode or compress a file.
     *
     * @return Closure(string): string
     */
    public function getCallbackFileWriteBefore(): Closure
    {
        return $this->callbackFileWriteBefore;
    }

    /**
     * Defines the callback that gets used before writing the list.
     * This can be used to encode or compress a file.
     *
     * @param Closure(string): string $callbackFileWriteBefore
     * @return $this
     */
    public function setCallbackFileWriteBefore(Closure $callbackFileWriteBefore): self
    {
        $this->callbackFileWriteBefore = $callbackFileWriteBefore;
        return $this;
    }

    private function readFromFile(): HyphenationLibraryCacheInterface
    {
        $wordsHyphenatedJsonContent = '{}';

        if (false === $this->isLibraryExisting()) {
            $this->filesystem->write(
                $this->file->getWordsHyphenatedJsonFile(),
                $wordsHyphenatedJsonContent
            );
        }

        try {
            $wordsHyphenatedJsonContent = $this->filesystem->read(
                $this->file->getWordsHyphenatedJsonFile(),
            );
        } catch (FilesystemException $filesystemException) {
            $this->logger->error('Failed to encode hyphenation library: ' . $filesystemException->getMessage());
        }

        $wordsHyphenatedJsonContent = $this->getCallbackFileReadAfter()($wordsHyphenatedJsonContent);

        $hyphenationLibraryCache = null;

        try {
            $hyphenationLibraryCache = (new MapperBuilder())->mapper()->map(
                HyphenationLibraryCache::class,
                new JsonSource($wordsHyphenatedJsonContent)
            );
        } catch (Throwable $throwable) {
            $this->logger->error('Failed to encode hyphenation library: ' . $throwable->getMessage());
        }

        return $hyphenationLibraryCache ?? new HyphenationLibraryCache();
    }

    /**
     * @throws Exception
     */
    private function writeToFile(): void
    {
        $jsonNormalizer = (new NormalizerBuilder())
            ->normalizer(Format::json())
            ->withOptions(JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        ;

        $wordsHyphenatedJson = $jsonNormalizer->normalize(
            $this->hyphenationLibraryCache
        );

        $wordsHyphenatedJson = $this->getCallbackFileWriteBefore()($wordsHyphenatedJson);

        try {
            $this->filesystem->write(
                $this->file->getWordsHyphenatedJsonFile(),
                $wordsHyphenatedJson,
            );
        } catch (FilesystemException $filesystemException) {
            throw new Exception('Failed to update hyphenation library.', $filesystemException);
        }
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function getHyphenatedWord(string $word): string|null
    {
        return $this->getHyphenationWords()[$word] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function saveLibrary(): bool
    {
        try {
            $this->writeToFile();
        } catch (Exception $exception) {
            $this->logger->error('Failed to save hyphenation library: ' . $exception->getMessage());
            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function getWordsDetails(): array
    {
        return $this->hyphenationLibraryCache->getWordsDetails();
    }

    /**
     * @inheritDoc
     */
    public function getWordDetails(string $word): array|null
    {
        return $this->getWordsDetails()[$word] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function getDateTimeLibraryUpdated(): DateTimeInterface|null
    {
        return $this->hyphenationLibraryCache->getDateTimeLibraryUpdated();
    }
}
