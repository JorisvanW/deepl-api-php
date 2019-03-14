<?php

namespace JorisvanW\DeepL\Api\Cons;

class Translate
{
    /**
     * All supported language code constants
     * @see https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes
     */
    const LANG_AUTO = 'auto'; // Let DeepL decide which language it is (only works for the source language)
    const LANG_DE = 'DE'; // German
    const LANG_EN = 'EN'; // English
    const LANG_FR = 'FR'; // French
    const LANG_ES = 'ES'; // Spanish
    const LANG_IT = 'IT'; // Italian
    const LANG_NL = 'NL'; // Dutch
    const LANG_PL = 'PL'; // Polish
    const LANG_PT = 'PT'; // Portuguese
    const LANG_RU = 'RU'; // Russian

    /**
     * Array with all supported language codes
     * @see https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes
     */
    const LANG_CODES = [
        self::LANG_AUTO,
        self::LANG_DE,
        self::LANG_EN,
        self::LANG_FR,
        self::LANG_ES,
        self::LANG_IT,
        self::LANG_NL,
        self::LANG_PL,
        self::LANG_PT,
        self::LANG_RU,
    ];

    /**
     * Array with language codes as keys and the matching language names in English as values
     * @see https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes
     */
    const LANG_NAMES = [
        self::LANG_AUTO => 'Auto',
        self::LANG_DE => 'German',
        self::LANG_EN => 'English',
        self::LANG_FR => 'French',
        self::LANG_ES => 'Spanish',
        self::LANG_IT => 'Italian',
        self::LANG_NL => 'Dutch',
        self::LANG_PL => 'Polish',
        self::LANG_PT => 'Portuguese',
        self::LANG_RU => 'Russian',
    ];
}
