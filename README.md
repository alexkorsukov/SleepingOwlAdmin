## Laravel 5.2 Admin Module

[![Latest Stable Version](https://poser.pugx.org/sleeping-owl/admin/v/unstable.svg)](https://packagist.org/packages/laravelrus/sleepingowl)
[![License](https://poser.pugx.org/laravelrus/sleepingowl/license.svg)](https://packagist.org/packages/laravelrus/sleepingowl)

*Note: this is development version. If you are looking for stable version check out [master branch](https://github.com/LaravelRUS/SleepingOwlAdmin).*

SleepingOwl Admin is administrative interface builder for Laravel.

## Installation

 1. Require this package in your composer.json and run composer update:

		"laravelrus/sleepingowl": "4.*@dev"

 2. After composer update, add service providers to the `config/app.php`

	    SleepingOwl\Admin\Providers\SleepingOwlServiceProvider::class,
 3. Run this command in terminal (if you want to know what exactly this command makes, see [install command documentation](http://sleeping-owl.github.io/en/Commands/Install.html)):

		$ php artisan sleepingowl:install

## Demo project

You can download demo project https://github.com/LaravelRUS/SleepingOwlAdminDemo

## Documentation

Documentation can be found at [sleeping owl documentation](http://sleeping-owl.github.io/v4).

## Copyright and License

Admin was written by Sleeping Owl for the Laravel framework and is released under the MIT License. See the LICENSE file for details.