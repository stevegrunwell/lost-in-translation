<?php

namespace LostInTranslation;

use Illuminate\Contracts\Translation\Loader;
use Illuminate\Log\Logger;
use Illuminate\Translation\Translator as BaseTranslator;
use LostInTranslation\Events\MissingTranslationFound;
use LostInTranslation\Exceptions\MissingTranslationException;
use Psr\Log\LoggerInterface;

class Translator extends BaseTranslator
{
    /**
     * The current logger instance.
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /*
     * Add the pattern of the key that you allow to be non-translated
     */
    private $ignoreMissing = [];

    /**
     * Create a new translator instance.
     *
     * @param \Illuminate\Contracts\Translation\Loader  $loader
     * @param string                                    $locale
     * @param \Psr\Log\LoggerInterface                  $logger
     *
     * @return void
     */
    public function __construct(Loader $loader, string $locale, LoggerInterface $logger)
    {
        parent::__construct($loader, $locale);

        $this->logger = $logger;
    }

    /**
     * Get the translation for the given key.
     *
     * This method acts as a pass-through to Illuminate\Translation\Translator::get(), but verifies
     * that a replacement has actually been made.
     *
     * @throws MissingTranslationException When no replacement is made.
     *
     * @param  string      $key
     * @param  array       $replace
     * @param  string|null $locale
     * @param  bool        $fallback
     *
     * @return string|array|null
     */
    public function get($key, array $replace = [], $locale = null, $fallback = true)
    {
        $translation = parent::get($key, $replace, $locale, $fallback);

        /*
         * When the translation is the same as the key, then the translation is not found
         */
        if ($translation === $key) {

            if ($this->shouldIgnore($key)) {
                return $translation;
            }

            // Log the missing translation.
            if (config('lostintranslation.log')) {
                $this->logMissingTranslation($key, $replace, $locale, $fallback);
            }

            // Throw a MissingTranslationException if no translation was made.
            if (config('lostintranslation.throw_exceptions')) {
                throw new MissingTranslationException(
                    sprintf('Could not find translation for "%s".', $key)
                );
            }

            // Dispatch a MissingTranslationFound event.
            event(new MissingTranslationFound($key, $replace, $locale, $fallback ? config('app.fallback_locale') : ''));
        }

        return $translation;
    }

    /**
     * Check if there is a translation in a json file
     */
    private function hasJsonTranslation(string $locale, string $key): bool
    {
        return isset($this->loaded['*']['*'][$locale ?: $this->locale][$key]);
    }

    /**
     * Log a missing translation.
     *
     * @param string $key
     * @param array  $replacements
     * @param string $locale
     * @param bool   $fallback
     */
    protected function logMissingTranslation(string $key, array $replacements, ?string $locale, bool $fallback): void
    {
        $this->logger->notice('Missing translation: ' . $key, [
            'replacements' => $replacements,
            'locale'       => $locale ?: config('app.locale'),
            'fallback'     => $fallback ? config('app.fallback_locale') : '',
        ]);
    }


    private function shouldIgnore(string $key): bool
    {
        if ($this->ignoreCustomValues($key)) {
            return true;
        }

        $result = false;
        foreach ($this->ignoreMissing as $pattern) {

            if (false !== str_contains($key, $pattern)) {
                $result = true;
            }
        }

        return $result;
    }

    /*
     * Laravel allows for translatable custom values which results in a key to be translated like
     * 'validation.values.postal_code.<user-input>' i.e. validation.values.postal_code.2263AB
     * FormatsMessages->getDisplayableValue() uses the translator to translate this string,
     * and that triggers this translator class and returns the key if not found
     *
     * these custom values translations always have the pattern : "validation.values.{$attribute}.{$value}"
     * the $value can be null then the key will be "validation.values.iban." note the DOT at the end
     *
     */
    private function ignoreCustomValues(string $key): bool
    {
        if (false !== str_contains($key, 'validation.values.')) {
            return true;
        }

        return false;
    }
}
