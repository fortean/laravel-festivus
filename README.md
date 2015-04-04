# Festivus for the REST of us... A Guzzle-based REST client for Laravel 4 with packaged service descriptions.

Festivus provides a Laravel 4 Facade wrapping Guzzle with helpers that make it drop-dead simple to use.
Eloquent models can be hydrated from service calls and APIs are defined by service descriptions.  What
more could you ask for?  An aluminum pole?

### Contents
 
- [Installation](#installation)
- [Thanks](#thanks)
- [License](#license)

## Installation

Add laravel-festivus to your composer.json file:

```
"require": {
  "laravel-festivus": "4.*"
}
```

Use composer to install this package.

```
$ composer update
```

### Registering the Package

Register the service provider within the ```providers``` array found in ```app/config/app.php```:

```php
'providers' => array(
	// ...
	
	'Fortean\Festivus\FestivusServiceProvider'
)
```

Add an alias within the ```aliases``` array found in ```app/config/app.php```:


```php
'aliases' => array(
	// ...
	
	'Festivus' => 'Fortean\Festivus\Facade\Festivus',
)
```

## Thanks

Thanks to the Guzzle crew for making such an awesome client!  Without you guys, I'd still be hand-coding
silly REST calls!

## License

This library is licensed under the [MIT license](LICENSE).
