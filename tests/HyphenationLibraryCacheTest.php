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
use BitAndBlack\Hyphenizer\Sdk\HyphenationLibraryCache;
use PHPUnit\Framework\TestCase;

class HyphenationLibraryCacheTest extends TestCase
{
    public function testCanSortWords(): void
    {
        $hyphenationLibraryCache = new HyphenationLibraryCache();

        $hyphenationLibraryCache->setWords([
            'c' => '3',
            'B' => '2',
            'a' => '1',
        ]);

        self::assertSame(
            [
                'a' => '1',
                'B' => '2',
                'c' => '3',
            ],
            $hyphenationLibraryCache->getWords()
        );
    }

    public function testCanSortWordDetails(): void
    {
        $wordA1 = new Word('1', 100, true, false);
        $wordB1 = new Word('21', 100, true, false);
        $wordB2 = new Word('22', 100, true, false);
        $wordC1 = new Word('3', 100, true, false);

        $hyphenationLibraryCache = new HyphenationLibraryCache();

        $hyphenationLibraryCache->addWordDetails('c', $wordC1);
        $hyphenationLibraryCache->addWordDetails('B', $wordB2, $wordB1);
        $hyphenationLibraryCache->addWordDetails('a', $wordA1);

        self::assertSame(
            [
                'a' => [$wordA1],
                'B' => [$wordB1, $wordB2],
                'c' => [$wordC1],
            ],
            $hyphenationLibraryCache->getWordsDetails()
        );
    }

    public function testMovesWordsBetweenLists(): void
    {
        $wordA = new Word('Bodensee', 100, true, false);
        $wordB = new Word('Bodenseefelchen', 100, true, false);

        $hyphenationLibraryCache = new HyphenationLibraryCache();

        $hyphenationLibraryCache->setWords([
            'Bodensee' => 'Bodensee',
            'Bodenseefelchen' => null,
        ]);

        $hyphenationLibraryCache->addWordDetails('Bodensee', $wordA);
        $hyphenationLibraryCache->addWordDetails('Bodenseefelchen', $wordB);

        self::assertSame(
            [
                'Bodensee' => 'Bodensee',
            ],
            $hyphenationLibraryCache->getWords()
        );

        self::assertSame(
            [
                'Bodensee' => [$wordA],
                'Bodenseefelchen' => [$wordB],
            ],
            $hyphenationLibraryCache->getWordsDetails()
        );
    }
}
