<?php

namespace LostInTranslation\Providers;

use Illuminate\Translation\TranslationServiceProvider as BaseProvider;
use LostInTranslation\Translator;
use Psr\Log\LoggerInterface;

class TranslationServiceProvider extends BaseProvider
{
    /**
     * Filesystem path to the configuration file.
     */
    const CONFIG_PATH = __DIR__ . '/../config/lostintranslation.php';

    /**
     * Register the service provider.
     *
     * This should mirror the Illuminate\Translation\TranslationServiceProvider::register() method
     * exactly, but the Translator class being referenced is LostInTranslation\Translator instead.
     *
     * @return void
     */
    public function register()
    {
        $this->registerLoader();

        $this->app->singleton('translator', function ($app) {
            $trans = new Translator(
                $app['translation.loader'],
                $app['config']['app.locale'],
                $app->make(LoggerInterface::class)
            );

            $trans->setFallback($app['config']['app.fallback_locale']);

            return $trans;
        });

        // Load package configuration.
        $this->mergeConfigFrom(self::CONFIG_PATH, 'lostintranslation');
    }

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        /*
         * TranslationServiceProvider doesn't currently have a boot() method, but this ensures it
         * will still be run should it ever be added.
         */
         // @codeCoverageIgnoreStart
        if (is_callable('parent::boot')) {
            parent::boot();
        }
        // @codeCoverageIgnoreEnd

        // Enable developers to publish the configuration.
        $this->publishes([
            self::CONFIG_PATH => config_path('lostintranslation.php'),
        ]);
    }
}
