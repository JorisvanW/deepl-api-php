<?php

namespace JorisvanW\DeepL\Api\Resources;

use JorisvanW\DeepL\Api\DeepLApiClient;

class Translate extends BaseResource
{
    /**
     * The translated text.
     *
     * @var string
     */
    public $text;

    /**
     * The language which has been detected for the source text. It reflects the value of the source_lang parameter if it has been specified.
     *
     * @var string
     */
    public $detected_source_language;
}
