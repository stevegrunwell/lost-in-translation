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

        // The "translation" is unchanged from the key.
        if ($translation === $key) {
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
}
