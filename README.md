[![PHP from Packagist](https://img.shields.io/packagist/php-v/bitandblack/hyphenizer-sdk-php)](http://www.php.net)
[![Total Downloads](https://poser.pugx.org/bitandblack/hyphenizer-sdk-php/downloads)](https://packagist.org/packages/bitandblack/hyphenizer-sdk-php)
[![License](https://poser.pugx.org/bitandblack/hyphenizer-sdk-php/license)](https://packagist.org/packages/bitandblack/hyphenizer-sdk-php)

<p align="center">
    <a href="https://www.bitandblack.com" target="_blank">
        <img src="https://www.bitandblack.com/build/images/BitAndBlack-Logo-Full.png" alt="Bit&Black Logo" width="400">
    </a>
</p>

# Bit&Black Hyphenizer SDK for PHP

Use the Bit&Black Hyphenizer API in PHP to supercharge your typography.

## Installation

This library is made for the use with [Composer](https://packagist.org/packages/bitandblack/hyphenizer-sdk-php). Add it to your project by running `$ composer require bitandblack/hyphenizer-sdk-php`.

## Usage 

First of all, make sure you have an API token to access the Hyphenizer API. You can create on under [www.hyphenizer.com](https://www.hyphenizer.com).

### Creating and using the client

To communicate with the Hyphenizer API, the [`HyphenizerClient`](./src/HyphenizerClient.php) class can be used:

```php
<?php

use BitAndBlack\Hyphenizer\Sdk\HyphenizerClient;

$hyphenizerClient = new HyphenizerClient('your_token');
```

There are two methods to call the API and get its response: `getSingleWordRequest` and `getMultipleWordsRequest`. They provide detailed information about the requested words:

```php
<?php

$wordsToHyphenated = [
    'Bodensee',
    'Bodenseefelchen',
];

$wordsHyphenated = $hyphenizerClient->getMultipleWordsRequest($wordsToHyphenated);

/**
 * This will dump:
 *
 * object(BitAndBlack\Hyphenizer\Sdk\Api\WordsResponse) {
 *     ["status":"BitAndBlack\Hyphenizer\Sdk\Api\WordsResponse"] => int(200)
 *     ["messages":"BitAndBlack\Hyphenizer\Sdk\Api\WordsResponse"] => array(0) {}
 *     ["payload":"BitAndBlack\Hyphenizer\Sdk\Api\WordsResponse"] => object(BitAndBlack\Hyphenizer\Sdk\Api\WordsPayload) {
 *         ["words":"BitAndBlack\Hyphenizer\Sdk\Api\WordsPayload"] => array(2) {
 *             ["Bodensee"] => array(1) {
 *                 [0] => object(BitAndBlack\Hyphenizer\Sdk\Api\Word) {
 *                     ["hyphenation":"BitAndBlack\Hyphenizer\Sdk\Api\Word"] => string(9) "Boden|see"
 *                     ["score":"BitAndBlack\Hyphenizer\Sdk\Api\Word"] => int(100)
 *                     ["approved":"BitAndBlack\Hyphenizer\Sdk\Api\Word"] => bool(true)
 *                     ["hasTypo":"BitAndBlack\Hyphenizer\Sdk\Api\Word"] => bool(false)
 *                 }
 *             }
 *             ["Bodenseefelchen"] => array(1) {
 *                 [0] => object(BitAndBlack\Hyphenizer\Sdk\Api\Word) {
 *                     ["hyphenation":"BitAndBlack\Hyphenizer\Sdk\Api\Word"] => string(16) "Bodensee|felchen"
 *                     ["score":"BitAndBlack\Hyphenizer\Sdk\Api\Word"] => int(100)
 *                     ["approved":"BitAndBlack\Hyphenizer\Sdk\Api\Word"] => bool(true)
 *                     ["hasTypo":"BitAndBlack\Hyphenizer\Sdk\Api\Word"] => bool(false)
 *                 }
 *             }
 *         }
 *     }
 * }
 */
var_dump($wordsHyphenated);
```

If you are satisfied with receiving a simple list of the hyphenated words, you can also use methods `getSingleWordHyphenated` and `getWordsHyphenated`:

```php
<?php

$wordsToHyphenated = [
    'Bodensee',
    'Bodenseefelchen',
];

$wordsHyphenated = $hyphenizerClient->getWordsHyphenated($wordsToHyphenated);

/**
 * This will dump: 
 * 
 * array(2) {
 *     ["Bodensee"] => string(9) "Boden|see"
 *     ["Bodenseefelchen"] => string(16) "Bodensee|felchen"
 * }
 */
var_dump($wordsHyphenated);
```

### Using the hyphenation library 

To store and manage the words and their hyphenations, you can make use of the [`HyphenationLibrary`](./src/HyphenationLibrary.php) class.

The Hyphenation Library makes use of us the [`league/flysystem`](https://packagist.org/packages/league/flysystem) library, so you can decide where you want to store the words. Per default, it makes use of the [`LocalFilesystemAdapter`](https://github.com/thephpleague/flysystem/blob/3.x/src/Local/LocalFilesystemAdapter.php):

```php
<?php

use BitAndBlack\Hyphenizer\Sdk\HyphenationLibrary;

$hyphenationLibrary = new HyphenationLibrary(
    // Optional: new MyCustomFileSystemAdapter()
);
```

Those methods will help you to maintain you library:

-   `addWords`: Adds one or more unhyphenated words to the library.
-   `getHyphenationWords`: Returns a list of all existing words and their hyphenation.
-   `setHyphenationWords`: Resets the library of hyphenated words. This overrides the existing library entirely.

Before reading or writing the list of words, the Hyphenation Library will call a callback, that you can use the encode/decode or compress/uncompress the list:

-   `setCallbackFileReadAfter`: Defines the callback that gets used after reading the list. For example:

    ```php
    <?php
    
    $hyphenationLibrary->setCallbackFileReadAfter(
        static fn (string $content): string => base64_decode($content);
    );
    ```

-   `setCallbackFileWriteBefore`: Defines the callback that gets used before writing the list. For example:
  
    ```php
    <?php
    
    $hyphenationLibrary->setCallbackFileWriteBefore(
        static fn (string $content): string => base64_encode($content);
    );
    ```

## Help

If you have any questions, feel free to contact us under `hello@bitandblack.com`.

Further information about Bit&Black can be found under [www.bitandblack.com](https://www.bitandblack.com).