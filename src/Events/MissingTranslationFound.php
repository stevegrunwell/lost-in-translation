<?php

namespace LostInTranslation\Events;

class MissingTranslationFound
{
    /**
     * The translation key that was called.
     *
     * @var string
     */
    public $key;

    /**
     * An array of replacements that were passed to the string.
     *
     * @var array
     */
    public $replacements;

    /**
     * The app locale.
     *
     * @var string
     */
    public $locale;

    /**
     * The fallback locale.
     *
     * @var string
     */
    public $fallback;

    /**
     * Create a new event instance.
     *
     * @param string $key
     * @param array  $replace
     * @param string $locale
     * @param string $fallback
     *
     * @return void
     */
    public function __construct(string $key, array $replace = [], string $locale = null, string $fallback = null)
    {
        $this->key          = $key;
        $this->replacements = $replace;
        $this->locale       = $locale ?: config('app.locale');
        $this->fallback     = $fallback ?: config('app.fallback_locale');
    }
}
