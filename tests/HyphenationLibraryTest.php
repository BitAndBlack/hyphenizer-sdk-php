<?php

/**
 * Bit&Black Hyphenizer SDK.
 *
 * @author Tobias Köngeter
 * @copyright Copyright © Bit&Black
 * @link https://www.bitandblack.com
 * @license MIT
 */

namespace BitAndBlack\Hyphenizer\Sdk\Tests;

use BitAndBlack\Hyphenizer\Sdk\Api\Word;
use BitAndBlack\Hyphenizer\Sdk\Api\WordsPayload;
use BitAndBlack\Hyphenizer\Sdk\Api\WordsResponse;
use BitAndBlack\Hyphenizer\Sdk\Exception;
use BitAndBlack\Hyphenizer\Sdk\HyphenationLibrary;
use BitAndBlack\Hyphenizer\Sdk\Util\File;
use BitAndBlack\Hyphenizer\Sdk\Util\Path;
use PHPUnit\Framework\TestCase;

class HyphenationLibraryTest extends TestCase
{
    private static string $hyphenationLibraryFile;

    public static function setUpBeforeClass(): void
    {
        self::$hyphenationLibraryFile = (new Path())->getLibraryFolder() . DIRECTORY_SEPARATOR . (new File())->getWordsHyphenatedJsonFile();
        parent::setUpBeforeClass();
    }

    protected function setUp(): void
    {
        if (true === file_exists(self::$hyphenationLibraryFile)) {
            unlink(self::$hyphenationLibraryFile);
        }
    }

    protected function tearDown(): void
    {
        if (true === file_exists(self::$hyphenationLibraryFile)) {
            unlink(self::$hyphenationLibraryFile);
        }
    }

    /**
     * @throws Exception
     */
    public function testCanHandleFileWriterCallback(): void
    {
        $hyphenationLibrary = new HyphenationLibrary();

        $callbackFileWriteBefore = static function (string $content): string {
            self::assertStringContainsString(
                'Seeelefant',
                $content
            );

            return $content;
        };

        $hyphenationLibrary->setCallbackFileWriteBefore($callbackFileWriteBefore);

        $hyphenationWords = [
            'Seeelefant' => 'See|elefant',
        ];

        $hyphenationLibrary->setHyphenationWords($hyphenationWords, true);
    }

    /**
     * @throws Exception
     */
    public function testCanAddWords(): void
    {
        $hyphenationLibrary = new HyphenationLibrary();

        self::assertCount(
            0,
            $hyphenationLibrary->getHyphenationWords()
        );

        $hyphenationWords = [
            'Nonnenkloster' => 'Nonnen|kloster',
            'Seeelefant' => 'See|elefant',
        ];

        $hyphenationLibrary->setHyphenationWords($hyphenationWords, false);

        self::assertCount(
            2,
            $hyphenationLibrary->getHyphenationWords()
        );

        $wordsToAdd = [
            'Nonnenkloster',
            'Schnabeltasse',
        ];

        $hyphenationLibrary->addWords($wordsToAdd, false);

        self::assertCount(
            3,
            $hyphenationLibrary->getHyphenationWords()
        );
    }

    /**
     * @throws Exception
     */
    public function testCanAddDataFromApiResponse(): void
    {
        $wordsResponse = new WordsResponse(
            payload: new WordsPayload([
                'Staubecken' => [
                    new Word(
                        'Stau|becken',
                        100,
                        true,
                        false,
                    ),
                    new Word(
                        'Staub|ecken',
                        100,
                        true,
                        false,
                    ),
                ],
            ])
        );

        $hyphenationLibrary = new HyphenationLibrary();
        $hyphenationLibrary->addDataFromApiWordsResponse($wordsResponse);

        $hyphenationLibrary->saveLibrary();

        $wordsResponse = new WordsResponse(
            payload: new WordsPayload([
                'Bodensee' => [
                    new Word(
                        'Boden|see',
                        100,
                        true,
                        false,
                    ),
                ],
            ])
        );

        $hyphenationLibrary = new HyphenationLibrary();

        $hyphenationLibrary->addDataFromApiWordsResponse($wordsResponse);

        $wordDetails = $hyphenationLibrary->getWordDetails('Bodensee');

        self::assertIsArray(
            $wordDetails
        );

        self::assertCount(
            1,
            $wordDetails
        );

        $wordDetails = $hyphenationLibrary->getWordDetails('Staubecken');

        self::assertIsArray(
            $wordDetails
        );

        self::assertCount(
            2,
            $wordDetails
        );

        $hyphenationLibrary->setHyphenationWords([
            'Bodenseefelchen' => 'Bodensee|felchen',
        ]);

        $hyphenationLibrary->saveLibrary();

        self::assertCount(
            3,
            $hyphenationLibrary->getHyphenationWords()
        );

        $wordDetails = $hyphenationLibrary->getWordDetails('Bodenseefelchen');

        self::assertNull(
            $wordDetails
        );
    }
}
