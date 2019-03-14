<?php

namespace JorisvanW\DeepL\Api\Endpoints;

use JorisvanW\DeepL\Api\Resources\Translate;
use JorisvanW\DeepL\Api\Cons\Translate as TranslateType;

class TranslateEndpoint extends EndpointAbstract
{
    protected $resourcePath = 'translate';

    protected function getResourceObject()
    {
        return new Translate($this->client);
    }

    /**
     * Translate a text with DeepL.
     *
     * @param string $text
     * @param string $to
     * @param string $from
     * @param array  $options
     *
     * @return \JorisvanW\DeepL\Api\Resources\BaseResource|\JorisvanW\DeepL\Api\Resources\Translate
     * @throws \JorisvanW\DeepL\Api\Exceptions\ApiException
     */
    public function translate(
        $text,
        $to = TranslateType::LANG_EN,
        $from = TranslateType::LANG_AUTO,
        $options = []
    ) {
        return $this->getRequest(null, array_merge($options, [
            'text'        => $text,
            'source_lang' => $from,
            'target_lang' => $to,
        ]));
    }
}
