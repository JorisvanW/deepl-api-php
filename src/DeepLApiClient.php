<?php

namespace JorisvanW\DeepL\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\ClientInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\GuzzleException;
use JorisvanW\DeepL\Api\Exceptions\ApiException;
use JorisvanW\DeepL\Api\Endpoints\UsageEndpoint;
use JorisvanW\DeepL\Api\Endpoints\TranslateEndpoint;

class DeepLApiClient
{
    /**
     * Endpoint of the remote API.
     */
    const API_ENDPOINT = 'https://api.deepl.com';

    /**
     * Version of the remote API.
     */
    const API_VERSION = 'v2';

    /**
     * HTTP Methods
     */
    const HTTP_GET    = 'GET';
    const HTTP_POST   = 'POST';
    const HTTP_DELETE = 'DELETE';
    const HTTP_PATCH  = 'PATCH';

    /**
     * HTTP status codes
     */
    const HTTP_NO_CONTENT = 204;

    /**
     * Default response timeout (in seconds).
     */
    const TIMEOUT = 10;

    /**
     * @var ClientInterface
     */
    protected $httpClient;

    /**
     * @var string
     */
    protected $apiEndpoint = self::API_ENDPOINT;

    /**
     * RESTful usage resource.
     *
     * @var UsageEndpoint
     */
    public $usage;

    /**
     * RESTful translations resource.
     *
     * @var TranslateEndpoint
     */
    public $translations;

    /**
     * @var string
     */
    public $apiKey;

    /**
     * @var int
     */
    protected $lastHttpResponseStatusCode;

    /**
     * @param ClientInterface $httpClient
     */
    public function __construct(ClientInterface $httpClient = null)
    {
        $this->httpClient = $httpClient ?: new Client([
            \GuzzleHttp\RequestOptions::TIMEOUT => self::TIMEOUT,
        ]);

        $this->initializeEndpoints();
    }

    public function initializeEndpoints()
    {
        $this->usage        = new UsageEndpoint($this);
        $this->translations = new TranslateEndpoint($this);
    }

    /**
     * @param string $url
     *
     * @return DeepLApiClient
     */
    public function setApiEndpoint($url)
    {
        $this->apiEndpoint = rtrim(trim($url), '/');

        return $this;
    }

    /**
     * @return string
     */
    public function getApiEndpoint()
    {
        return $this->apiEndpoint;
    }

    /**
     * @param string $apiKey The DeepL API key.
     *
     * @return DeepLApiClient
     *
     * @throws ApiException
     */
    public function setApiKey($apiKey)
    {
        $apiKey = trim($apiKey);

        if (empty($apiKey)) {
            throw new ApiException("Invalid DeepL API key: '{$apiKey}'.");
        }

        $this->apiKey      = $apiKey;
        $this->oauthAccess = false;

        return $this;
    }

    /**
     * Perform an http call. This method is used by the resource specific classes. Please use the $payments property to
     * perform operations on payments.
     *
     * @see $payments
     * @see $isuers
     *
     * @param string               $httpMethod
     * @param string               $apiMethod
     * @param string|null|resource $httpBody
     *
     * @return object
     * @throws ApiException
     *
     * @codeCoverageIgnore
     */
    public function performHttpCall($httpMethod, $apiMethod, $httpBody = null)
    {
        $url = $this->apiEndpoint . '/' . self::API_VERSION . '/' . $apiMethod;

        return $this->performHttpCallToFullUrl($httpMethod, $url, $httpBody);
    }

    /**
     * Perform an http call to a full url. This method is used by the resource specific classes.
     *
     * @see $payments
     * @see $isuers
     *
     * @param string               $httpMethod
     * @param string               $url
     * @param string|null|resource $httpBody
     *
     * @return object|null
     * @throws ApiException
     *
     * @codeCoverageIgnore
     */
    public function performHttpCallToFullUrl($httpMethod, $url, $httpBody = null)
    {
        if (empty($this->apiKey)) {
            throw new ApiException('You have not set an API key. Please use setApiKey() to set the API key.');
        }

        $headers = [
            'Accept'        => 'application/json',
//            'Authorization' => "Bearer {$this->apiKey}",
        ];

        $request = new Request($httpMethod, $url, $headers, $httpBody);

        try {
            $response = $this->httpClient->send($request, ['http_errors' => false]);
        } catch (GuzzleException $e) {
            throw ApiException::createFromGuzzleException($e);
        }

        if (!$response) {
            throw new ApiException('Did not receive API response.');
        }

        return $this->parseResponseBody($response);
    }

    /**
     * Parse the PSR-7 Response body
     *
     * @param ResponseInterface $response
     *
     * @return object|null
     * @throws ApiException
     */
    private function parseResponseBody(ResponseInterface $response)
    {
        $body = (string)$response->getBody();
        if (empty($body)) {
            if ($response->getStatusCode() === self::HTTP_NO_CONTENT) {
                return null;
            }

            throw new ApiException('No response body found.');
        }

        $object = @json_decode($body);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ApiException("Unable to decode DeepL response: '{$body}'.");
        }

        if ($response->getStatusCode() >= 400) {
            throw ApiException::createFromResponse($response);
        }

        return $object;
    }

    /**
     * Serialization can be used for caching. Of course doing so can be dangerous but some like to live dangerously.
     *
     * \serialize() should be called on the collections or object you want to cache.
     *
     * We don't need any property that can be set by the constructor, only properties that are set by setters.
     *
     * Note that the API key is not serialized, so you need to set the key again after unserializing if you want to do
     * more API calls.
     *
     * @deprecated
     * @return string[]
     */
    public function __sleep()
    {
        return ['apiEndpoint'];
    }

    /**
     * When unserializing a collection or a resource, this class should restore itself.
     *
     * Note that if you use a custom GuzzleClient, this client is lost. You can't re set the Client, so you should
     * probably not use this feature.
     */
    public function __wakeup()
    {
        $this->__construct();
    }
}
