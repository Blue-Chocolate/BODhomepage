<?php

namespace App\Providers;

use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Scramble::ignoreDefaultRoutes();
        Scramble::registerUiRoute('docs/api');
        Scramble::registerJsonSpecificationRoute('docs/api.json');

        Gate::define('viewApiDocs', function ($user = null) {
            return request()->query('secret') === env('SCRAMBLE_SECRET');
        });

        Scramble::afterOpenApiGenerated(function (OpenApi $openApi) {
            // Register bearer token as available scheme
            $openApi->secure(
                SecurityScheme::http('bearer', 'JWT')
            );

            // Remove security requirement from every operation
            foreach ($openApi->paths as $path) {
                foreach ($path->operations as $operation) {
                    $operation->security = [];
                }
            }
        });
    }
}