<?php

namespace JorisvanW\DeepL\Api\Endpoints;

use JorisvanW\DeepL\Api\Resources\Usage;

class UsageEndpoint extends EndpointAbstract
{
    protected $resourcePath = 'usage';

    protected function getResourceObject()
    {
        return new Usage($this->client);
    }

    /**
     * Get the usage.
     * @throws \JorisvanW\DeepL\Api\Exceptions\ApiException
     */
    public function get()
    {
        return $this->getRequest();
    }
}
