<?php

namespace Xolvio\OpenApiGenerator\Data;

use Illuminate\Routing\Route;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Transformation\TransformationContext;
use Spatie\LaravelData\Support\Transformation\TransformationContextFactory;

class SecurityScheme extends Data
{
    public const BEARER_SECURITY_SCHEME = 'bearer';

    public function __construct(
        protected string $scheme,
        /** @var string[] */
        public array $permissions = [],
    ) {}

    /**
     * @return Collection<int,static>
     */
    public static function fromRoute(Route $route): Collection
    {
        $security    = [];
        $permissions = static::getPermissions($route);

        /** @var string[] $middlewares */
        $middlewares = $route->middleware();
        if (array_intersect(config('openapi-generator.security_middlewares.' . self::BEARER_SECURITY_SCHEME), $middlewares)) {
            $security[] = new self(
                scheme: self::BEARER_SECURITY_SCHEME,
                permissions: $permissions,
            );
        }

        return self::collect($security, Collection::class);
    }

    /**
     * @return string[]
     */
    public static function getPermissions(Route $route): array
    {
        /** @var string[] */
        $permissions = [];

        /** @var string[] $middlewares */
        $middlewares = $route->middleware();

        foreach ($middlewares as $middleware) {
            if (str_starts_with($middleware, 'can:')) {
                $permissions[] = self::strAfter($middleware, 'can:');
            }
        }

        return $permissions;
    }

    /**
     * @return array<int|string,mixed>
     */
    public function transform(
        null|TransformationContext|TransformationContextFactory $transformationContext = null,
    ): array {
        return [$this->scheme => $this->permissions];
    }

    protected static function strAfter(string $subject, string $search): string
    {
        return '' === $search ? $subject : array_reverse(explode($search, $subject, 2))[0];
    }
}
