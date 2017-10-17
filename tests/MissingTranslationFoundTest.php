<?php

namespace Tests;

use LostInTranslation\Events\MissingTranslationFound;

class MissingTranslationFoundTest extends TestCase
{
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
}
