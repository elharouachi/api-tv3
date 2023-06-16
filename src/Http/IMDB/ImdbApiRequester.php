<?php

namespace App\Http\IMDB;

use App\Http\JsonApiRequester;

class ImdbApiRequester
{
    private const API_NAME = 'OMDB API';

    /**
     * @var string
     */
    private $apiUrl;

    /**
     * @var JsonApiRequester
     */
    private $apiRequester;
    private $headers;

    public function __construct(JsonApiRequester $apiRequester, string $apiUrl, string $headers)
    {
        $this->headers = json_decode($headers, true);
        $this->apiRequester = $apiRequester;
        $this->apiUrl = rtrim($apiUrl, '/');
    }

    /**
     * @param array $options see HttpRequester::createAndSendRequest
     */
    public function request(string $method, string $url, ?array $data = null, array $headers, array $options = []): ?array
    {
        $response = $this->apiRequester->createAndSendRequest(
            self::API_NAME,
            $method,
            $url,
            $data,
            $headers,
            $options
        );
        $responseBody = $response->getBody();

        return $responseBody->getSize() ? json_decode($responseBody->getContents(), true) : null;
    }

    /**
     * @param array $options see HttpRequester::createAndSendRequest
     */
    public function getMovieDetail(string $movieTitle, array $options = []): ?array
    {
        $url = $this->getMovieUrl($movieTitle);
        $options['ignoreErrorStatusCodes'] = [404];

        $response = $this->request('GET', $url, null,  $this->headers, $options);

        return !empty($response['results']) ? $response['results'] : null;
    }


    private function getMovieUrl(string $movieTitle): string
    {
        return sprintf('%s/title/find?q=%s', $this->apiUrl, $movieTitle);
    }
}
