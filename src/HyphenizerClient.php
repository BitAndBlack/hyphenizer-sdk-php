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

use BitAndBlack\Hyphenizer\Sdk\Api\WordPayload;
use BitAndBlack\Hyphenizer\Sdk\Api\WordResponse;
use BitAndBlack\Hyphenizer\Sdk\Api\WordsPayload;
use BitAndBlack\Hyphenizer\Sdk\Api\WordsResponse;
use BitAndBlack\Hyphenizer\Sdk\Exception\RequestException;
use CuyZ\Valinor\Mapper\Source\Source;
use CuyZ\Valinor\MapperBuilder;
use Fig\Http\Message\StatusCodeInterface;
use Http\Client\Common\HttpMethodsClient;
use Http\Client\Common\HttpMethodsClientInterface;
use Http\Client\Exception as HttpClientException;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Throwable;

class HyphenizerClient implements HyphenizerClientInterface, LoggerAwareInterface
{
    private readonly HttpMethodsClientInterface $httpMethodsClient;

    /**
     * @var array<int, string>
     */
    private array $wordsWithTypos = [];

    private string $hyphenizerUrl = 'https://api.hyphenizer.com';

    private LoggerInterface $logger;

    /**
     * @param non-empty-string $token
     * @param non-empty-string|null $hyphenizerUrl
     */
    public function __construct(
        private readonly string $token,
        string|null $hyphenizerUrl = null
    ) {
        $this->logger = new NullLogger();
        $this->httpMethodsClient = new HttpMethodsClient(
            Psr18ClientDiscovery::find(),
            Psr17FactoryDiscovery::findRequestFactory(),
            Psr17FactoryDiscovery::findStreamFactory(),
        );
        $this->hyphenizerUrl = $hyphenizerUrl ?? $this->hyphenizerUrl;
    }

    /**
     * @param non-empty-string $word
     * @throws RequestException
     */
    public function getSingleWordRequest(string $word): WordResponse
    {
        $headers = [
            'Authorization' => 'Bearer ' . $this->token,
        ];

        try {
            $response = $this->httpMethodsClient->get($this->hyphenizerUrl . '/v2/words/' . $word, $headers);
        } catch (HttpClientException $httpClientException) {
            throw new RequestException('Failed to request Hyphenizer API.', $httpClientException);
        }

        if ($response->getStatusCode() >= 400) {
            throw new RequestException('Failed to request Hyphenizer API. (Response code is "' . $response->getStatusCode() . '")');
        }

        $contents = $response->getBody()->getContents();
        $mapper = (new MapperBuilder())->mapper();

        try {
            $responseDecoded = $mapper->map(
                WordResponse::class,
                Source::json($contents)
            );
        } catch (Throwable $throwable) {
            throw new RequestException('Failed to decode response.', $throwable);
        }

        return $responseDecoded;
    }

    /**
     * @param array<int, non-empty-string> $words
     * @throws RequestException
     */
    public function getMultipleWordsRequest(array $words): WordsResponse
    {
        $headers = [
            'Authorization' => 'Bearer ' . $this->token,
        ];

        $body = http_build_query([
            'words' => $words,
        ]);

        try {
            $response = $this->httpMethodsClient->post($this->hyphenizerUrl . '/v2/multiple-words', $headers, $body);
        } catch (HttpClientException $httpClientException) {
            throw new RequestException('Failed to request Hyphenizer API.', $httpClientException);
        }

        if ($response->getStatusCode() >= 400) {
            throw new RequestException('Failed to request Hyphenizer API. (Response code is "' . $response->getStatusCode() . '")');
        }

        $contents = $response->getBody()->getContents();
        $mapper = (new MapperBuilder())->mapper();

        try {
            $wordsResponse = $mapper->map(
                WordsResponse::class,
                Source::json($contents)
            );
        } catch (Throwable $throwable) {
            throw new RequestException('Failed to decode response.', $throwable);
        }

        return $wordsResponse;
    }

    /**
     * @param non-empty-string $word
     * @param int<0, 100> $minScoreRequired
     */
    public function getSingleWordHyphenated(string $word, int $minScoreRequired = 50): string
    {
        try {
            $singleWordRequest = $this->getSingleWordRequest($word);
        } catch (Exception $exception) {
            $this->logger->error('Failed hyphenation words with exception: {exception}.', [
                'exception' => $exception->getMessage(),
            ]);
            return $word;
        }

        if (StatusCodeInterface::STATUS_OK !== $singleWordRequest->getStatusCode()) {
            $this->logger->error('Failed hyphenation words because of status code: {code}.', [
                'code' => $singleWordRequest->getStatusCode(),
            ]);
            return $word;
        }

        $payload = $singleWordRequest->getPayload();

        if (!$payload instanceof WordPayload) {
            $this->logger->error('Failed hyphenation words (got empty payload).');
            return $word;
        }

        $wordHyphenated = $payload->getWord()[0] ?? null;

        if (null === $wordHyphenated) {
            $this->logger->error('Failed hyphenation words (word is missing from response).');
            return $word;
        }

        if (true === $wordHyphenated->hasTypo()) {
            $this->wordsWithTypos[] = $word;
        }

        if ($wordHyphenated->getScore() < $minScoreRequired) {
            return $word;
        }

        return $wordHyphenated->getHyphenation();
    }

    /**
     * @param array<int, non-empty-string> $words
     * @param int<0, 100> $minScoreRequired
     * @return array<non-empty-string, non-empty-string>
     */
    public function getWordsHyphenated(array $words, int $minScoreRequired = 50): array
    {
        try {
            $wordsResponse = $this->getMultipleWordsRequest($words);
        } catch (Exception $exception) {
            $this->logger->error('Failed hyphenation words with exception: {exception}.', [
                'exception' => $exception->getMessage(),
            ]);

            return array_combine(
                $words,
                $words,
            );
        }

        return $this->getWordsHyphenatedFromWordsResponse(
            $wordsResponse,
            $minScoreRequired
        );
    }

    /**
     * @param int<0, 100> $minScoreRequired
     * @return array<non-empty-string, non-empty-string>
     */
    public function getWordsHyphenatedFromWordsResponse(WordsResponse $wordsResponse, int $minScoreRequired = 50): array
    {
        $wordsHyphenated = [];

        if (StatusCodeInterface::STATUS_OK !== $wordsResponse->getStatusCode()) {
            $this->logger->error('Failed hyphenation words because of status code: {code}.', [
                'code' => $wordsResponse->getStatusCode(),
            ]);
            return $wordsHyphenated;
        }

        $payload = $wordsResponse->getPayload();

        if (!$payload instanceof WordsPayload) {
            $this->logger->error('Failed hyphenation words (got empty payload).');
            return $wordsHyphenated;
        }

        foreach ($payload->getWords() as $word => $hyphenationPossibilities) {
            $hyphenationPossibility = $hyphenationPossibilities[0] ?? null;

            if (null === $hyphenationPossibility) {
                continue;
            }

            if (true === $hyphenationPossibility->hasTypo()) {
                $this->wordsWithTypos[] = $word;
            }

            if ($hyphenationPossibility->getScore() < $minScoreRequired) {
                continue;
            }

            $wordsHyphenated[$word] = $hyphenationPossibility->getHyphenation();
        }

        return $wordsHyphenated;
    }

    /**
     * @return array<int, string>
     */
    public function getWordsWithTypos(): array
    {
        return $this->wordsWithTypos;
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
}
