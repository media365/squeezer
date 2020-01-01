# squeezer
URL shortening or squeezing package in Laravel
    
## Installation
You can easily install this package using Composer, by running the following command:

```bash
composer require media365/squeezer
```

### Requirements
This package has the following requirements:

- PHP 7.1 or higher
- Laravel 5.1 or higher

### Laravel 5.5+
If you use Laravel 5.5 or higher, that's it. You can now use the package, continue to the [usage](#usage) section.

### Laravel 5.1-5.4
If you're using an older version of Laravel, register the package's service provider to your application. You can do
this by adding the following line to your `config/app.php` file:

```php
'providers' => [
   ...
   Media365\Squeezer\SqueezerServiceProvider::class,
   ...
],
```

## Usage

```php
$shortener = app('url.shortener');

```

Once you have an instance of the shortener, you can shorten your URLs:

```php
// This will return your shortened URL as a string
$shortener->shorten('https://github.com');
```

You can provide driver to shorten url

```php
// This will return your shortened URL as a string with provided driver
$shortener->driver('is_gd')->shorten('https://github.com')
```
