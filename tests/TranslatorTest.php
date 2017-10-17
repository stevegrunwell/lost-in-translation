<?php

namespace Tests;

use Illuminate\Log\Writer;
use Illuminate\Support\Facades\Event;
use LostInTranslation\Events\MissingTranslationFound;
use LostInTranslation\Exceptions\MissingTranslationException;
use LostInTranslation\Translator;
use Mockery;
use ReflectionProperty;

class TranslatorTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        Event::fake();
    }

    public function testCanMockTranslationData()
    {
        $unique = uniqid();
        $this->loadTranslations([
            'key' => $unique,
            'nested' => [
                'foo' => [
                    'bar' => [
                        'baz' => 'This was nested',
                    ],
                ],
            ],
        ]);

        $this->assertEquals($unique, trans('testData.key'));
        $this->assertEquals('This was nested', trans('testData.nested.foo.bar.baz'));
    }

    public function testLogsMissingTranslations()
    {
        config(['lostintranslation.log' => true]);

        $mock = $this->createLoggerSpy();

        trans('testData.thisValueHasNotBeenDefined');

        $mock->shouldHaveReceived('notice');
    }

    public function testLoggingCanBeDisabled()
    {
        config(['lostintranslation.log' => false]);

        $mock = $this->createLoggerSpy();

        trans('testData.thisValueHasNotBeenDefined');

        $mock->shouldNotHaveReceived('notice');
    }

    public function testThrowsMissingTranslationException()
    {
        config(['lostintranslation.throw_exceptions' => true]);

        try {
            trans('testData.missing_key');

        } catch (MissingTranslationException $e) {
            $this->assertInstanceOf(MissingTranslationException::class, $e);

            return;
        }

        $this->fail('Did not receive expected exception.');
    }

    public function testFiresMissingTranslationFoundEvent()
    {
        trans('testData.thisValueHasNotBeenDefined');

        Event::assertDispatched(MissingTranslationFound::class, function ($e) {
            return 'testData.thisValueHasNotBeenDefined' === $e->key;
        });
    }

    /**
     * Mock the loaded translations within the Translator instance.
     *
     * All translations will be under the 'testData' file, as if there were a
     * resources/lang/en/testData.php translation file.
     *
     * @link https://laravel.com/docs/5.5/localization
     *
     * @param array $translations Translations as they would be defined in a Laravel language file.
     *
     * @return void
     */
    protected function loadTranslations($translations)
    {
        $translator = resolve('translator');

        $loaded = new ReflectionProperty($translator, 'loaded');
        $loaded->setAccessible(true);

        $loaded->setValue($translator, [
            '*' => [
                'testData' => [
                    'en' => (array) $translations,
                ],
            ],
        ]);
    }

    /**
     * Create a Mockery spy and assign it to Translator::$logger.
     *
     * @return \Mockery\MockInterface
     */
    protected function createLoggerSpy()
    {
        $mock = Mockery::spy(Writer::class);
        $prop = new ReflectionProperty(Translator::class, 'logger');
        $prop->setAccessible(true);
        $prop->setValue(resolve('translator'), $mock);

        return $mock;
    }
}
