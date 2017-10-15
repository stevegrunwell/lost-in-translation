<?php

namespace Tests;

use LostInTranslation\Providers\TranslationServiceProvider;
use LostInTranslation\Translator;

class TranslationServiceProviderTest extends TestCase
{
    public function testOverridesDefaultTranslator()
    {
        $this->assertInstanceOf(
            Translator::class,
            resolve('translator'),
            sprintf(
                'The %s provider should override the default Laravel TranslationServiceProvider.',
                TranslationServiceProvider::class
            )
        );
    }
}
