# Laravel Throttleable

[![Packagist](https://img.shields.io/packagist/v/madmikeyb/throttleable.svg?style=flat-square)](https://packagist.org/packages/madmikeyb/throttleable)
[![Packagist](https://img.shields.io/packagist/l/madmikeyb/throttleable.svg?style=flat-square)]()

Throttle requests to your application based on users IP address.

 - Set a threshold on how many requests an IP address can make.
 - Throttles expire after a configurable period of time.
 - Throttles are unique per IP address.
 - Configurable through `config/throttleable.php`

## Installation

Pull in the package using Composer

    composer require madmikeyb/throttleable

> **Note**: If you are using Laravel 5.5, the next steps are unnecessary. Laravel Throttleable supports Laravel [Package Discovery](https://laravel.com/docs/5.5/packages#package-discovery).

Include the service provider within `app/config/app.php`.

```php
'providers' => [
    ...
    MadMikeyB\Throttleable\Providers\ThrottleableServiceProvider::class,
],
```

## Migrations

You must publish [the migration](https://github.com/madmikeyb/throttleable/blob/master/database/migrations/create_throttles_table.php.stub) by running the following command:

```bash
php artisan vendor:publish --provider="MadMikeyB\Throttleable\Providers\ThrottleableServiceProvider" --tag="migrations" && php artisan migrate
```

## Configuration

Throttleable supports optional configuration.

You can publish the config file with:

```bash
php artisan vendor:publish --provider="MadMikeyB\Throttleable\Providers\ThrottleableServiceProvider" --tag="config"
```

When published, [the `config/throttleable.php` config file](https://github.com/madmikeyb/throttleable/blob/master/config/throttleable.php) contains:

```php
<?php

return [
    /**
     * Number of attempts permitted to a single
     * IP address before being throttled.
     */
    'attempt_limit' => 10,
    
    /**
     * The datetime metric to use for expirations
     * Available options are hour, day or week.
     */
    'expiry_metric' => 'week',

    /**
     * The number of hours, days or weeks to
     * keep a throttle valid for.
     */
    'expiry_timelimit' => 1
];

```

These are merely the default values and can be overriden on a case-by-case basis if needed.

## Sample Usage

Simply import the Throttle Model in your controller.

```php
<?php
namespace App\Http\Controllers;

use MadMikeyB\Throttleable\Models\Throttle;
```

Then, on whichever method you'd like to throttle, new up a `Throttle` instance. The minimum parameters required by this class is an instance of `Illuminate\Http\Request`.

The `check()` method of `Throttle` returns a boolean, which indicates whether the IP address has been throttled or not.

```php
public function create(Request $request) 
{
    $throttle = new Throttle($request->instance());
    
    if (!$throttle->check()) {
       alert()->error('Sorry, you have made too many requests. Please try again later.');
       return back();
    }
}
```

**NB. the `alert()` helper is provided by [uxweb/sweet-alert](https://github.com/uxweb/sweet-alert) and is not included in this package.**

## Full Example

```php
<?php
namespace App\Http\Controllers;

use App\Comment;
use MadMikeyB\Throttleable\Models\Throttle;

class CommentsController 
{
    public function store(Request $request) 
    {
        $throttle = new Throttle($request->instance());
        
        if (!$throttle->check()) {
           alert()->error('Sorry, you have made too many requests. Please try again later.');
           return back();
        }

        // save comment here
        Comment::create($request->all());
        alert()->success('Comment Created!');
        return back();
    }
}
```

## Overriding Configuration on a case-by-case basis

In some cases you may want to override the default attempt and time limits which are set in the configuration file. For example, a user creating a comment may not be throttled as quickly as someone trying to log into an admin control panel.

The second and third arguments to the constructor of the `Throttle` model are the `attempt_limit` and the `expiry_weeks` configuration variables respectively.

If you need to override the defaults set in `config/throttleable.php`, you would do so as follows:

```php
public function store(Request $request) 
{
    $attemptLimit = 10000;
    $expiryWeeks = 52;

    $throttle = new Throttle($request->instance(), $attemptLimit, $expiryWeeks);
    
    if (!$throttle->check()) {
       alert()->error('Sorry, you have made too many requests. Please try again later.');
       return back();
    }

    // save comment here
    Comment::create($request->all());
    alert()->success('Comment Created!');
    return back();
}
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
