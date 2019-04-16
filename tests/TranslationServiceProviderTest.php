<?php

namespace Tests;

use LostInTranslation\Providers\TranslationServiceProvider;
use LostInTranslation\Translator;

/**
 * @testdox Translation service provider
 */
class TranslationServiceProviderTest extends TestCase
{
    /**
     * @test
     */
    public function it_should_override_the_default_translator_class()
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

    /**
     * @test
     * @group Config
     */
    public function it_should_provide_configuration()
    {
        $this->assertNotEmpty(
            config('lostintranslation'),
            'The TranslationServiceProvider should define the "lostintranslation" config array.'
        );
    }

    /**
     * @test
     * @group Config
     */
    public function it_should_permit_the_config_to_be_published()
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
