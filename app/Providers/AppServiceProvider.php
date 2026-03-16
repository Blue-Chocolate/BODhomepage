<?php

namespace App\Providers;

use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Dedoc\Scramble\Support\Generator\Operation;
use Dedoc\Scramble\Support\Generator\Schema;
use Dedoc\Scramble\Support\Generator\Types\ObjectType;
use Dedoc\Scramble\Support\Generator\Types\StringType;
use Dedoc\Scramble\Support\Generator\Types\IntegerType;
use Dedoc\Scramble\Support\Generator\Types\BooleanType;
use Dedoc\Scramble\Support\Generator\RequestBodyObject;
use Dedoc\Scramble\Support\Generator\MediaType;
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
            $openApi->secure(
                SecurityScheme::http('bearer', 'JWT')
            );

            foreach ($openApi->paths as $pathKey => $path) {
                foreach ($path->operations as $method => $operation) {
                    // Remove security from all operations
                    $operation->security = [];

                    // Inject body for POST /api/blogs
                    if ($method === 'post' && str_contains($pathKey, 'blogs')) {
                        $operation->requestBody = $this->buildBlogRequestBody();
                    }
                }
            }
        });
    }

    private function buildBlogRequestBody(): RequestBodyObject
    {
        $schema = Schema::createFromType(
            (new ObjectType)
                ->addProperty('title', (new StringType)->setDescription('Blog title'))
                ->addProperty('slug', (new StringType)->setDescription('URL slug'))
                ->addProperty('short_description', (new StringType)->setDescription('Short description'))
                ->addProperty('content', (new StringType)->setDescription('Full content'))
                ->addProperty('author', (new StringType)->setDescription('Author name'))
                ->addProperty('blog_category_id', (new IntegerType)->setDescription('Category ID'))
                ->addProperty('image_path', (new StringType)->setDescription('Image path')->nullable(true))
                ->addProperty('published_at', (new StringType)->setDescription('Publish date')->nullable(true))
                ->addProperty('is_published', (new BooleanType)->setDescription('Published status')->nullable(true))
                ->setRequired(['title', 'slug', 'short_description', 'content', 'author', 'blog_category_id'])
        );

        $mediaType = new MediaType;
        $mediaType->schema = $schema;

        $requestBody = new RequestBodyObject;
        $requestBody->setContent('application/json', $mediaType);

        return $requestBody;
    }
}