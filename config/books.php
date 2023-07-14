<?php

return [

    /*
    |--------------------------------------------------------------------------
    | API KEYS
    |--------------------------------------------------------------------------
    */
    'key' => env('GOOGLE_BOOKS_KEY'),
    'isbn_key' => env('ISBN_BOOKS_KEY'),


    /*
    |--------------------------------------------------------------------------
    | Book search service to use
    | 'google' or 'isbn' or 'db'
    |--------------------------------------------------------------------------
    */
    'search_service' => 'db',
    'search_db_logic' => true,
    'google' => [
        'class' => App\Services\GoogleBooks::class,
    ],
    'isbn' => [
        'class' => App\Services\ISBNBooks::class,
    ],
    'db' => [
        'class' => App\Services\DBBooks::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Downloading or not the large covers from OpenLibrary
    | true/false
    |--------------------------------------------------------------------------
    */
    'download_covers_from_ol' => false,
    'non_public_tag' => 'plan to read',
];
