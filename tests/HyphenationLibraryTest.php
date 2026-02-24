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

use BitAndBlack\Hyphenizer\Sdk\Exception;
use BitAndBlack\Hyphenizer\Sdk\HyphenationLibrary;
use BitAndBlack\Hyphenizer\Sdk\Util\File;
use BitAndBlack\Hyphenizer\Sdk\Util\Path;
use PHPUnit\Framework\TestCase;

class HyphenationLibraryTest extends TestCase
{
    protected function setUp(): void
    {
        $file = (new Path())->getLibraryFolder() . DIRECTORY_SEPARATOR . (new File())->getWordsHyphenatedJsonFile();

        if (true === file_exists($file)) {
            unlink($file);
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
}
