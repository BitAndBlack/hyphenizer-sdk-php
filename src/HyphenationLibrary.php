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

use BitAndBlack\Hyphenizer\Sdk\Util\File;
use BitAndBlack\Hyphenizer\Sdk\Util\FileInterface;
use BitAndBlack\Hyphenizer\Sdk\Util\Path;
use BitAndBlack\Hyphenizer\Sdk\Util\PathInterface;
use Closure;
use CuyZ\Valinor\Mapper\Source\JsonSource;
use CuyZ\Valinor\MapperBuilder;
use CuyZ\Valinor\Normalizer\Format;
use CuyZ\Valinor\NormalizerBuilder;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\FilesystemException;
use League\Flysystem\Local\LocalFilesystemAdapter;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Throwable;

class HyphenationLibrary implements LoggerAwareInterface
{
    /**
     * @var array<string, string|null>
     */
    private array $words = [];

    private LoggerInterface $logger;

    private bool $hasLoaded = false;

    /**
     * @var Closure(string): (string)
     */
    private Closure $callbackFileReadAfter;

    /**
     * @var Closure(string): string
     */
    private Closure $callbackFileWriteBefore;

    private readonly Filesystem $filesystem;

    public function __construct(
        FilesystemAdapter|null $filesystemAdapter = null,
        PathInterface $path = new Path(),
        private readonly FileInterface $file = new File(),
    ) {
        $this->logger = new NullLogger();

        $adapter = $filesystemAdapter ?? new LocalFilesystemAdapter(
            $path->getLibraryFolder()
        );

        $this->filesystem = new Filesystem($adapter);

        $this->callbackFileReadAfter = static fn (string $content): string => $content;
        $this->callbackFileWriteBefore = static fn (string $content): string => $content;
    }

    /**
     * @return array<string, string|null>
     */
    public function getWords(): array
    {
        if (false === $this->hasLoaded) {
            $this->words = $this->readFromFile();
            uksort($this->words, strcasecmp(...));
            $this->hasLoaded = true;
        }

        return $this->words;
    }

    /**
     * Resets the library of hyphenated words. This overrides the existing library entirely.
     *
     * @param array<string, string|null> $wordsHyphenated
     * @return $this
     * @throws Exception
     */
    public function setHyphenationWords(array $wordsHyphenated, bool $saveList = true): self
    {
        $this->words = $wordsHyphenated;
        uksort($this->words, strcasecmp(...));
        $this->hasLoaded = true;

        if (true === $saveList) {
            $this->writeToFile();
        }

        return $this;
    }

    /**
     * Adds one or more unhyphenated words to the library.
     *
     * @param array<int, string> $words
     * @return $this
     * @throws Exception
     */
    public function addWords(array $words, bool $saveList = true): self
    {
        $words = array_combine(
            array_values($words),
            array_fill(0, count($words), null)
        );

        $wordsMerged = array_merge(
            $this->getWords(),
            $words
        );

        return $this->setHyphenationWords($wordsMerged, $saveList);
    }

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
     * @return Closure(string): (string)
     */
    public function getCallbackFileReadAfter(): Closure
    {
        return $this->callbackFileReadAfter;
    }

    /**
     * @param Closure(string): (string) $callbackFileReadAfter
     * @return $this
     */
    public function setCallbackFileReadAfter(Closure $callbackFileReadAfter): self
    {
        $this->callbackFileReadAfter = $callbackFileReadAfter;
        return $this;
    }

    /**
     * @return Closure(string): string
     */
    public function getCallbackFileWriteBefore(): Closure
    {
        return $this->callbackFileWriteBefore;
    }

    /**
     * @param Closure(string): string $callbackFileWriteBefore
     * @return $this
     */
    public function setCallbackFileWriteBefore(Closure $callbackFileWriteBefore): self
    {
        $this->callbackFileWriteBefore = $callbackFileWriteBefore;
        return $this;
    }

    /**
     * @return array<string, string|null>
     */
    private function readFromFile(): array
    {
        try {
            $wordsHyphenatedJsonContent = $this->filesystem->read(
                $this->file->getWordsHyphenatedJsonFile(),
            );
        } catch (FilesystemException $filesystemException) {
            $this->logger->error('Failed to encode hyphenation library: ' . $filesystemException->getMessage());
            return [];
        }

        $wordsHyphenatedJsonContent = $this->getCallbackFileReadAfter()($wordsHyphenatedJsonContent);

        $wordsHyphenated = null;

        try {
            /** @var array<string, string> $wordsHyphenated */
            $wordsHyphenated = (new MapperBuilder())->mapper()->map(
                'array<string, string>',
                new JsonSource($wordsHyphenatedJsonContent)
            );
        } catch (Throwable $throwable) {
            $this->logger->error('Failed to encode hyphenation library: ' . $throwable->getMessage());
        }

        if (false === is_array($wordsHyphenated)) {
            $this->logger->error('The hyphenation library seems to be broken.');
            return [];
        }

        return $wordsHyphenated;
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
            $this->getWords()
        );

        $wordsHyphenatedJson = $this->getCallbackFileWriteBefore()($wordsHyphenatedJson);

        try {
            $this->filesystem->write(
                $this->file->getWordsHyphenatedJsonFile(),
                $wordsHyphenatedJson,
            );
        } catch (FilesystemException $filesystemException) {
            throw new Exception('Failed to update hyphenation library.', 0, $filesystemException);
        }
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
}
