<?php

namespace LostInTranslation;

use Illuminate\Translation\Translator as BaseTranslator;
use LostInTranslation\Exceptions\MissingTranslationException;

class Translator extends BaseTranslator {

    /**
     * Get the translation for the given key.
     *
     * This method acts as a pass-through to Illuminate\Translation\Translator::get(), but verifies
     * that a replacement has actually been made.
     *
     * @throws MissingTranslationException When no replacement is made.
     *
     * @param  string       $key
     * @param  array        $replace
     * @param  string|null  $locale
     * @param  bool         $fallback
     *
     * @return string|array|null
     */
    public function get($key, array $replace = [], $locale = null, $fallback = true)
    {
        $translation = parent::get($key, $replace, $locale, $fallback);

        // Throw a MissingTranslationException if no translation was made.
        if ($translation === $key) {
            throw new MissingTranslationException(
                sprintf('Could not find translation for "%s".', $key)
            );
        }

        return $translation;
    }
}
