# laravel-api
Laravel Integration for randomstate/api.

**Note, for a framework agnostic starter, use randomstate/api. You will need to do some significant wiring up
to make it as useful as this Laravel package.**

# What it does

LaravelApi is an API versioning package that allows you to namespace and version your APIs (web, ajax, rest etc) easily.
It comes with out of the box support and integration for the fantastic `league/fractal` package.

In essence, this package allows you to standardise the output of your API while maintaining flexibility; using namespaces to change context. Add to that the fact you can version your API responses to target particular users, and you have the start to a blissful API building experience.

# Installation and Setup

`composer require randomstate/laravel-api`

Add `\RandomState\LaravelApi\LaravelApiServiceProvider::class` to your app.php config file.

Publish the configuration file by running:
`php artisan vendor:publish --tag=laravel-api`

Because of the deep routing integration required to make the magic work, you need to replace the Http Kernel with the one provided in this package.
You should change the `app/Http/Kernel.php` class to extend `RandomState\LaravelApi\Http\Kernel` instead of the HttpKernel default.

### Namespace Setup

To namespace your routes, you can utilise the `RandomsState\LaravelApi\Http\Middleware\ApiNamespace` middleware class.
Add the following to the `$routeMiddleware` property on your Kernel class.
```php
protected $routeMiddleware = [
    'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
    // ...
    'namespace' => RandomsState\LaravelApi\Http\Middleware\ApiNamespace::class,
];
```

# Usage

### Namespacing

If you followed the setup instructions you will have added a 'namespace' middleware.
You can use this as so:

```php
Route::group(['middleware' => ['namespace:web'], function() {});
Route::group(['middleware' => ['namespace:web,1.0'], function() {}); // Forces version 1.0 to be used (must be specified in your laravel-api.php config file.
```

### Dynamic Versioning

Since the intended use case for this package is to dynamically provide your API depending on your users' target version, I recommend the following way of versioning your routes automatically:
In a service provider register method, (such as AppServiceProvider), you can add the following:

```php
$this->app->bind(\RandomState\LaravelApi\VersionSwitch::class, function() {
    return new class implements \RandomState\LaravelApi\VersionSwitch::class {
        public function getVersionIdentifier() {
            return Auth::user()->getApiVersion();
        }
    }
});
```

**You must implement the storage and `getApiVersion` method yourself - this is just an example of how you could do it.**

