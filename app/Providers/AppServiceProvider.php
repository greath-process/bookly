<?php

namespace App\Providers;

use App\Contracts\BooksSearchInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $default = config('books.search_service');
        $service = config("books.{$default}.class");

        $this->app->bind(BooksSearchInterface::class, function() use ($service){
            return new $service;
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
