<?php

namespace JorisvanW\DeepL\Api\Resources;

use JorisvanW\DeepL\Api\DeepLApiClient;

class Translate extends BaseResource
{
    /**
     * The translated text.
     *
     * @var object
     */
    public $translations;
}
