# Lost in Translation

[![Build Status](https://travis-ci.org/stevegrunwell/lost-in-translation.svg?branch=develop)](https://travis-ci.org/stevegrunwell/lost-in-translation)
[![Coverage Status](https://coveralls.io/repos/github/stevegrunwell/lost-in-translation/badge.svg?branch=develop)](https://coveralls.io/github/stevegrunwell/lost-in-translation?branch=develop)

Lost in Translation is designed to help developers locate instances of [localization strings within a Laravel application](https://laravel.com/docs/5.5/localization) that haven't been provided translations.


## Installation

Lost in Translation can be installed into your Laravel project via Composer:

```sh
$ composer require stevegrunwell/lost-in-translation
```

In config.app you must replace default `Illuminate\Translation\TranslationServiceProvider`  with `LostInTranslation\Providers\TranslationServiceProvider::class`

```
...
// Illuminate\Translation\TranslationServiceProvider::class,
LostInTranslation\Providers\TranslationServiceProvider::class,
...
```
To resume default behavior (even in a production environment), see [the "Configuration" section below](#configuration).


## Configuration

By default, Lost in Translation will catch missing translations in two ways:

1. In environments where `APP_DEBUG` is true, a `LostInTranslation\MissingTranslationException` will be found if the application attempts to load a translation that hasn't been defined.
2. Missing translations will be written to `storage/logs/lost-in-translation.log`.

Either of these can be disabled via the package's configuration, making Lost in Translation safe to use in production. These values can be set using the following environment variables:

<dl>
    <dt>TRANS_LOG_MISSING</dt>
    <dd>Determines whether or not missing translations should be logged. Default is "true".</dd>
    <dt>TRANS_ERROR_ON_MISSING</dt>
    <dd>Should <code>MissingTranslationException</code> exceptions be thrown when a translation is missing? Default is "false".</dd>
</dl>

To override package configuration, run the following to copy the configuration to your app's `config/` directory:

```sh
$ php artisan vendor:publish --provider="LostInTranslation\Providers\TranslationServiceProvider"
```

This will create a new file in `config/lostintranslation.php`, where default values for your application can be set.

## Extending

When a missing translation is found, the a `LostInTranslation\MissingTranslationFound` event will be dispatched. This event makes it easy to do something (send an email, open a GitHub issue, etc.)when a missing translation is encountered.

First, create a new event listener in your application; in this example, we're using `app/Listeners/NotifyOfMissingTranslation.php`:

```php
<?php

namespace App\Listeners;

use LostInTranslation\Events\MissingTranslationFound;

class NotifyOfMissingTranslation
{
    /**
     * Handle the event.
     *
     * @param MissingTranslationFound $event
     *
     * @return void
     */
    public function handle(MissingTranslationFound $event)
    {
        // Do something with the event.
    }
}
```

The `MissingTranslationFound` event has four public properties of note:

1. `$key` - The translation key that was not found.
2. `$replacements` - Any replacements that were passed to the translation call.
3. `$locale` - The locale that was being used.
4. `$fallback` - The fallback locale, if defined.

Then, in `app/Providers/EventServiceProvider.php`, add the following to register `NotifyOfMissingTranslation` as a callback when a `MissingTranslationFound` event occurs:

```php
/**
 * The event listener mappings for the application.
 *
 * @var array
 */
protected $listen = [
    'LostInTranslation\Events\MissingTranslationFound' => [
        'App\Listeners\NotifyOfMissingTranslation',
    ],
];
```

For more on event listeners, [please see the Laravel Events documentation](https://laravel.com/docs/5.5/events).
