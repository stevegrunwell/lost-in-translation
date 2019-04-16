<?php

namespace Tests;

use Faker\Factory as FakerFactory;
use Faker\Generator as FakerGenerator;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    /**
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * Get an instance of Faker.
     *
     * @return \Faker\Generator
     */
    protected function getFaker(): FakerGenerator
    {
        if (null === $this->faker) {
            $this->faker = FakerFactory::create();
        }

        return $this->faker;
    }

    /**
     * Get package providers.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            'LostInTranslation\Providers\TranslationServiceProvider',
        ];
    }

    /**
     * Inject translations into the test environment.
     *
     * All translations will be under the 'testData' file, as if there were a
     * resources/lang/en/testData.php translation file.
     *
     * @param array $translations Translations as they would be defined in a Laravel language file.
     *
     * @return void
     */
    protected function setTranslations(array $translations = [])
    {
        $translator = resolve('translator');

        $loaded = new \ReflectionProperty($translator, 'loaded');
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
