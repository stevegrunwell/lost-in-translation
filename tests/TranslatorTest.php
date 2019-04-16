<?php

namespace Tests;

use Illuminate\Log\Writer;
use Illuminate\Support\Facades\Event;
use LostInTranslation\Events\MissingTranslationFound;
use LostInTranslation\Exceptions\MissingTranslationException;
use LostInTranslation\Translator;
use Mockery;
use Psr\Log\LoggerInterface;

/**
 * @testdox Translator class
 */
class TranslatorTest extends TestCase
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    public function setUp(): void
    {
        parent::setUp();

        $this->logger = Mockery::spy(LoggerInterface::class);

        $this->app->extend(LoggerInterface::class, function () {
            return $this->logger;
        });

        Event::fake([
            MissingTranslationFound::class,
        ]);
    }

    /**
     * @test
     */
    public function it_should_do_nothing_if_the_translation_is_found()
    {
        try {
            $this->setTranslations([
                'key' => 'Some translation',
            ]);
        } catch (MissingTranslationException $e) {
            $this->fail('Should not have received a MissingTranslationException.');
        }

        $this->logger->shouldNotHaveReceived('notice');

        Event::assertNotDispatched(MissingTranslationFound::class);
    }

    /**
     * @test
     * @group Logging
     */
    public function it_should_be_able_to_log_missing_translations()
    {
        trans('testData.thisValueHasNotBeenDefined');

        $this->logger->shouldHaveReceived('notice')
            ->once();
    }

    /**
     * @test
     * @group Logging
     */
    public function it_should_log_any_replacements_that_were_provided()
    {
        trans('testData.thisValueHasNotBeenDefined', [
            'foo' => 'bar',
        ]);

        $this->logger->shouldHaveReceived('notice')
            ->once()
            ->withArgs(function (string $msg, array $context) {
                $this->assertSame(['foo' => 'bar'], data_get($context, 'replacements'));

                return true;
            });
    }

    /**
     * @test
     * @testdox It should respect the value of the `lostintranslations.log` configuration
     * @group Logging
     */
    public function logging_can_be_disabled()
    {
        config(['lostintranslation.log' => false]);

        trans('testData.thisValueHasNotBeenDefined');

        $this->logger->shouldNotHaveReceived('notice');
    }

    /**
     * @test
     * @group Exceptions
     */
    public function it_should_be_able_to_throw_MissingTranslationException_exceptions()
    {
        config(['lostintranslation.throw_exceptions' => true]);

        $this->expectException(MissingTranslationException::class);

        trans('testData.missing_key');
    }

    /**
     * @test
     * @group Events
     */
    public function it_should_fire_a_MissingTranslationFound_event()
    {
        trans('testData.thisValueHasNotBeenDefined');

        Event::assertDispatched(MissingTranslationFound::class, function ($e) {
            return 'testData.thisValueHasNotBeenDefined' === $e->key;
        });
    }
}
