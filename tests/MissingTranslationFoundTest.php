<?php

namespace Tests;

use Faker\Factory as Faker;
use LostInTranslation\Events\MissingTranslationFound;

class MissingTranslationFoundTest extends TestCase
{
    protected $faker;

    public function setUp(): void
    {
        parent::setUp();

        $this->faker = Faker::create();
    }

    public function testConstructor()
    {
        $key = 'my.translation.key';
        $replacements = [uniqid()];
        $locale = 'en';
        $fallback = 'fr';

        $event = new MissingTranslationFound($key, $replacements, $locale, $fallback);

        $this->assertEquals($key, $event->key);
        $this->assertEquals($replacements, $event->replacements);
        $this->assertEquals($locale, $event->locale);
        $this->assertEquals($fallback, $event->fallback);
    }

    public function testConstructorWithMissingLocale()
    {
        $locale = $this->faker->languageCode;

        config(['app.locale' => $locale]);

        $event = new MissingTranslationFound('my.translation.key');

        $this->assertEquals(
            $locale,
            $event->locale,
            'When one isn\'t specified, $event->locale should use config("app.locale").'
        );
    }

    public function testConstructorWithMissingFallbackLocale()
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
