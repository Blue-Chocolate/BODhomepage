<?php

namespace App\Providers;

use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Scramble::ignoreDefaultRoutes();
        Scramble::registerUiRoute('docs/api');
        Scramble::registerJsonSpecificationRoute('docs/api.json');

        Gate::define('viewApiDocs', function ($user = null) {
            return request()->query('secret') === env('SCRAMBLE_SECRET');
        });

        Scramble::afterOpenApiGenerated(function (OpenApi $openApi) {
            $openApi->secure(
                SecurityScheme::http('bearer', 'JWT')
            );

            $spec = $openApi->toArray();

            foreach ($spec['paths'] as $pathKey => &$pathItem) {
                foreach ($pathItem as $method => &$operation) {
                    // Remove auth from all
                    $operation['security'] = [];

                    // Inject body for POST blogs
                    if ($method === 'post' && str_contains($pathKey, 'blogs')) {
                        $operation['requestBody'] = [
                            'required' => true,
                            'content'  => [
                                'application/json' => [
                                    'schema' => [
                                        'type'       => 'object',
                                        'required'   => ['title', 'slug', 'short_description', 'content', 'author', 'blog_category_id'],
                                        'properties' => [
                                            'title'             => ['type' => 'string', 'example' => 'My Blog Post'],
                                            'slug'              => ['type' => 'string', 'example' => 'my-blog-post'],
                                            'short_description' => ['type' => 'string', 'example' => 'A short description'],
                                            'content'           => ['type' => 'string', 'example' => 'Full content here'],
                                            'author'            => ['type' => 'string', 'example' => 'John Doe'],
                                            'blog_category_id'  => ['type' => 'integer', 'example' => 1],
                                            'image_path'        => ['type' => 'string', 'nullable' => true, 'example' => 'uploads/img.jpg'],
                                            'published_at'      => ['type' => 'string', 'nullable' => true, 'example' => '2026-01-01 00:00:00'],
                                            'is_published'      => ['type' => 'boolean', 'nullable' => true, 'example' => false],
                                        ],
                                    ],
                                ],
                            ],
                        ];
                    }
                }
            }

            // Push the modified spec back — Scramble uses the object's internal array
            foreach ($openApi->paths as $pathKey => $path) {
                foreach ($path->operations as $method => $operation) {
                    $operation->security = [];
                }
            }
        });
    }
}