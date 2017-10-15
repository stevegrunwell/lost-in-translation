<?php
/**
 * Configuration for the Lost In Translation package.
 */

return [

    /**
     * Throw exceptions when an untranslated string is found?
     *
     * When true, MissingTranslationException exceptions will be thrown when a string is unable to
     * be translated.
     */
    'throw_exceptions' => env('TRANS_ERROR_ON_MISSING', config('app.debug')),
];
