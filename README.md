# Lost in Translation

[![Build Status](https://travis-ci.org/stevegrunwell/lost-in-translation.svg?branch=develop)](https://travis-ci.org/stevegrunwell/lost-in-translation)

Lost in Translation is designed to help developers locate instances of [localization strings within a Laravel application](https://laravel.com/docs/5.5/localization) that haven't been provided translations.


## Installation

Lost in Translation can be installed into your Laravel project via Composer:

```sh
$ composer require stevegrunwell/lost-in-translation
```

By default, this will replace the default `TranslationServiceProvider` class with a sub-class that adds additional logic when a translation isn't found. To resume default behavior (even in a production environment), see [the "Configuration" section below](#configuration).


## Configuration

By default, Lost in Translation will catch missing translations in two ways:

1. In environments where `APP_DEBUG` is true, a `LostInTranslation\MissingTranslationException` will be found if the application attempts to load a translation that hasn't been defined.
2. Missing translations will be written to `storage/logs/lost-in-translation.log`.

Either of these can be disabled via the package's configuration, making Lost in Translation safe to use in production. These values can be set using the following environment variables:

<dl>
    <dt>TRANS_LOG_MISSING</dt>
    <dd>Determines whether or not missing translations should be logged. Default is "true".</dd>
    <dt>TRANS_ERROR_ON_MISSING</dt>
    <dd>Should <code>MissingTranslationException</code> exceptions be thrown when a translation is missing? If not defined, this will default to the value of <code>config('app.debug')</code>.</dd>
</dl>

To override package configuration, run the following to copy the configuration to your app's `config/` directory:

```sh
$ php artisan vendor:publish --provider="LostInTranslation\Providers\TranslationServiceProvider"
```

This will create a new file in `config/lostintranslation.php`, where default values for your application can be set.
