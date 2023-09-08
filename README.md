# OpenAPI Generator using Laravel Data

Generate OpenAPI specification from Laravel routes and Laravel Data objects

# Install

## Add composer repository

In `composer.json` add this repository:

```json
    "repositories": [
        {
            "type": "github",
            "url": "https://github.com/xolvionl/laravel-data-openapi-generator"
        }
    ],
```

## Install

`composer require xolvio/laravel-data-openapi-generator`

# Optional

## Version

Add a `app.version` config in `app.php` to set the version in the openapi specification:
```php
    'version' => env('APP_VERSION', '1.0.0'),
```

## Vite PWA config

If using `vite-plugin-pwa`, make sure to exclude '/api/' routes from the serviceworker using this config:

```ts
VitePWA({
    workbox: {
        navigateFallbackDenylist: [
            new RegExp('/api/.+'),
        ],
    },
})
```

## Vue page

```vue
<route lang="json">
{
    "meta": {
        "public": true
    }
}
</route>

<template>
    <iframe
        :src="url"
        style="width: calc(100vw - 40px);height: calc(100vh - 80px); border: none;"
    />
</template>

<script lang="ts" setup>
const url = `${import.meta.env.VITE_APP_URL}/api/openapi`;
</script>
```

# Usage

## Config

`php artisan vendor:publish --tag=openapi-generator-config`

## Generate

`php artisan openapi:generate`

## View

Swagger available at `APP_URL/api/openapi`