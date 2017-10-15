<?php

namespace LostInTranslation\Providers;

use Illuminate\Translation\TranslationServiceProvider as BaseProvider;
use LostInTranslation\Translator;

class TranslationServiceProvider extends BaseProvider
{
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
            $loader = $app['translation.loader'];
            $locale = $app['config']['app.locale'];

            $trans = new Translator($loader, $locale);

            $trans->setFallback($app['config']['app.fallback_locale']);

            return $trans;
        });
    }
}
