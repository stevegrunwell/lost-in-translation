<?php

namespace Tests;

use LostInTranslation\Exceptions\MissingTranslationException;
use LostInTranslation\Translator;
use ReflectionProperty;

class TranslatorTest extends TestCase
{
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

    public function testThrowsMissingTranslationException()
    {
        try {
            trans('testData.missing_key');

        } catch (MissingTranslationException $e) {
            $this->assertInstanceOf(MissingTranslationException::class, $e);

            return;
        }

        $this->fail('Did not receive expected exception.');
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
}
