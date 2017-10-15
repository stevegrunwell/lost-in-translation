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

    public function testProvidesConfiguration()
    {
        $this->assertNotEmpty(
            config('lostintranslation'),
            'The TranslationServiceProvider should define the "lostintranslation" config array.'
        );
    }

    public function testPublishesConfig()
    {
        $provided = TranslationServiceProvider::$publishes[TranslationServiceProvider::class];

        $this->assertArrayHasKey(
            TranslationServiceProvider::CONFIG_PATH,
            $provided,
            'The config file defined in TranslationServiceProvider::CONFIG_PATH should be provided.'
        );

        $this->assertEquals(
            config_path('lostintranslation.php'),
            $provided[TranslationServiceProvider::CONFIG_PATH],
            'Configuration should be published to lostintranslation.php'
        );
    }
}
