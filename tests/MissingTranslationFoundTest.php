<?php

namespace Tests;

use LostInTranslation\Events\MissingTranslationFound;

/**
 * @testdox MissingTranslationFound event
 * @group Events
 */
class MissingTranslationFoundTest extends TestCase
{
    /**
     * @test
     */
    public function it_should_store_constructor_arguments()
    {
        $key = 'my.translation.key';
        $replacements = [uniqid()];
        $locale = 'en';
        $fallback = 'fr';

        $event = new MissingTranslationFound($key, $replacements, $locale, $fallback);

        $this->assertSame($key, $event->key);
        $this->assertSame($replacements, $event->replacements);
        $this->assertSame($locale, $event->locale);
        $this->assertSame($fallback, $event->fallback);
    }

    /**
     * @test
     */
    public function locale_should_default_to_app_locale()
    {
        $locale = $this->getFaker()->languageCode;

        config(['app.locale' => $locale]);

        $event = new MissingTranslationFound('my.translation.key');

        $this->assertSame(
            $locale,
            $event->locale,
            'When one isn\'t specified, $event->locale should use config("app.locale").'
        );
    }

    /**
     * @test
     */
    public function fallback_locale_should_default_to_app_fallback_locale()
    {
        config(['app.fallback_locale' => 'es']);

        $event = new MissingTranslationFound('my.translation.key', [], 'en');

        $this->assertEquals(
            'es',
            $event->fallback,
            'When one isn\'t specified, $event->fallback should use config("app.fallback_locale").'
        );
    }
}
