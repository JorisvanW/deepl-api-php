<?php

namespace JorisvanW\DeepL\Api\Resources;

use JorisvanW\DeepL\Api\DeepLApiClient;

abstract class BaseResource
{
    /**
     * @var DeepLApiClient
     */
    protected $client;

    /**
     * @param $client
     */
    public function __construct(DeepLApiClient $client)
    {
        $this->client = $client;
    }
}
