<?php

namespace App\Providers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        JsonResource::withoutWrapping();

        $appUrl = rtrim((string) config('app.url'), '/');
        $forceHttps = filter_var(env('FORCE_HTTPS', false), FILTER_VALIDATE_BOOL)
            || ($this->app->environment('production') && str_starts_with($appUrl, 'https://'));

        if ($forceHttps) {
            URL::forceHttps();

            if ($appUrl !== '') {
                URL::useOrigin($appUrl);
                URL::useAssetOrigin($appUrl);
            }
        }
    }
}
