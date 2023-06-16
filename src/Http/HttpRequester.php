<?php

namespace App\Http;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class HttpRequester
{
    private const HTTP_INTERNAL_SERVER_ERROR = 500;
    private const HTTP_SERVICE_UNAVAILABLE = 503;

    /**
     * @var ClientInterface
     */
    private $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    public function createAndSendRequest(
        string $targetName,
        string $requestMethod,
        string $requestUrl,
        ?string $requestBody = null,
        array $headers = [],
        array $options = []
    ): ResponseInterface {
        $request = new Request(
            $requestMethod,
            $requestUrl,
            $headers,
            $requestBody
        );

        return $this->sendRequest($targetName, $request, $options);
    }

    private function sendRequest(
        string $targetName,
        RequestInterface $request,
        array $options = []
    ) {
        $noResponseBody = $this->getOption('noResponseBody', $options, false);
        $timeout = $this->getOption('timeout', $options, 0);
        $clientOptions = ['http_errors' => false];

        if ($timeout > 0) {
            $clientOptions['timeout'] = $timeout;
            $clientOptions['connect_timeout'] = $timeout;
        }

        if ($noResponseBody) {
            $clientOptions['curl'] = [CURLOPT_NOBODY => true];
        }

        try {
            $start = microtime(true);
            $response = $this->client->send($request, $clientOptions);
        } catch (GuzzleException $exception) {
                //TODO
        }

        return $response;
    }

    /**
     * @return mixed
     */
    private function getOption(string $optionName, array $options, $defaultValue)
    {
        return array_key_exists($optionName, $options) ? $options[$optionName] : $defaultValue;
    }

    private function getLogTitle(string $targetName)
    {
        return sprintf('%s call.', $targetName);
    }
}
