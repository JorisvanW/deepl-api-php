<?php

namespace JorisvanW\DeepL\Api\Resources;

class Usage extends BaseResource
{
    /**
     * Characters translated so far in the current billing period.
     *
     * @var int
     */
    public $character_count;

    /**
     * Total maximum volume of characters that can be translated in the current billing period.
     *
     * @var int
     */
    public $character_limit;
}
