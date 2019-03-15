<?php

namespace JorisvanW\DeepL\Api\Endpoints;

use JorisvanW\DeepL\Api\DeepLApiClient;
use JorisvanW\DeepL\Api\Resources\BaseResource;
use JorisvanW\DeepL\Api\Exceptions\ApiException;
use JorisvanW\DeepL\Api\Resources\BaseCollection;
use JorisvanW\DeepL\Api\Resources\ResourceFactory;

abstract class EndpointAbstract
{
    const REST_CREATE = DeepLApiClient::HTTP_POST;
    const REST_UPDATE = DeepLApiClient::HTTP_PATCH;
    const REST_READ   = DeepLApiClient::HTTP_GET;
    const REST_LIST   = DeepLApiClient::HTTP_GET;
    const REST_DELETE = DeepLApiClient::HTTP_DELETE;

    /**
     * @var DeepLApiClient
     */
    protected $client;

    /**
     * @var string
     */
    protected $resourcePath;

    /**
     * @var string
     */
    protected $resourceCollectionKey;

    /**
     * @param DeepLApiClient $api
     */
    public function __construct(DeepLApiClient $api)
    {
        $this->client = $api;
    }

    /**
     * Make a GET request
     *
     * @param string|null $path
     * @param array       $params
     * @param bool        $reponseIsCollection
     *
     * @return BaseResource|BaseCollection
     * @throws \JorisvanW\DeepL\Api\Exceptions\ApiException
     */
    protected function getRequest($path = null, $params = [], $reponseIsCollection = false)
    {
        $result = $this->client->performHttpCall(self::REST_READ, rtrim("{$this->getResourcePath()}/{$path}", '/') . $this->buildQueryString($params));

        if ($reponseIsCollection) {
            $resultCopy = $result;

            foreach ($result->{$this->getResourceCollectionKey()} as $key => $dataResult) {
                $resultCopy->{$this->getResourceCollectionKey()}[$key] = ResourceFactory::createFromApiResult($dataResult, $this->getResourceObject());
            }

            return $resultCopy;
        }

        return ResourceFactory::createFromApiResult($result, $this->getResourceObject());
    }

    /**
     * @param array $filters
     *
     * @return string
     * @throws \JorisvanW\DeepL\Api\Exceptions\ApiException
     */
    private function buildQueryString(array $filters)
    {
        if (empty($this->client->apiKey)) {
            throw new ApiException('You have not set an API key. Please use setApiKey() to set the API key.');
        }

        $filters['auth_key'] = $this->client->apiKey;

        if (empty($filters)) {
            return '';
        }

        foreach ($filters as $key => $value) {
            if ($value === true) {
                $filters[$key] = 'true';
            }

            if ($value === false) {
                $filters[$key] = 'false';
            }
        }

        return '?' . http_build_query($filters, '', '&');
    }

    /**
     * Get the object that is used by this API endpoint. Every API endpoint uses one type of object.
     *
     * @return BaseResource
     */
    abstract protected function getResourceObject();

    /**
     * @param string $resourceCollectionKey
     */
    public function setResourceCollectionKey($resourceCollectionKey)
    {
        $this->resourceCollectionKey = strtolower($resourceCollectionKey);
    }

    /**
     * @return string
     * @throws ApiException
     */
    public function getResourceCollectionKey()
    {
        return $this->resourceCollectionKey;
    }

    /**
     * @param string $resourcePath
     */
    public function setResourcePath($resourcePath)
    {
        $this->resourcePath = strtolower($resourcePath);
    }

    /**
     * @return string
     * @throws ApiException
     */
    public function getResourcePath()
    {
        if (strpos($this->resourcePath, '_') !== false) {
            list($parentResource, $childResource) = explode('_', $this->resourcePath, 2);

            if (empty($this->parentId)) {
                throw new ApiException("Subresource '{$this->resourcePath}' used without parent '$parentResource' ID.");
            }

            return "$parentResource/{$this->parentId}/$childResource";
        }

        return $this->resourcePath;
    }

    /**
     * @param array $body
     *
     * @return null|string
     * @throws ApiException
     */
    protected function parseRequestBody(array $body)
    {
        if (empty($body)) {
            return null;
        }

        try {
            $encoded = \GuzzleHttp\json_encode($body);
        } catch (\InvalidArgumentException $e) {
            throw new ApiException("Error encoding parameters into JSON: '" . $e->getMessage() . "'.");
        }

        return $encoded;
    }
}
