## Gondolyn2

![CodeShip Status](https://codeship.com/projects/4157cbc0-bb7a-0132-d17b-3e819ad11634/status?branch=master)

[![Bitdeli Badge](https://d2weczhvl823v0.cloudfront.net/mlantz/gondolyn2/trend.png)](https://bitdeli.com/free "Bitdeli Badge")

Gondolyn is a seed application built with [Laravel](http://laravel.com), [Bootstrap](http://getbootstrap.com), [RawCSS](https://github.com/mlantz/rawCSS) and [jQuery](http://jquery.com). It comes with Oauth set so that logins can be done with Email, Facebook or Twitter. It comes with an SQLite database with the user table pre-built. Gondolyn also utilizes the [Creolab modules package](https://github.com/creolab/laravel-modules), allowing for HMVC application structure. Finally it comes with API structure featuring the login method.

#Installation & Setup

```
composer create-project mlantz/gondolyn2
```

If you wish to switch to a MySQL databse, set the database parameters in app/config/database and then run the following commands:

```
php artisan command:dbuild
php artisan migrate
```

## Official Documentation for Laravel

Documentation for the entire framework can be found on the [Laravel website](http://laravel.com/docs).

### Gondolyn Documentation

Documentation for the entire framework can be found on the [Gondolyn website](http://mlantz.github.io/Gondolyn/).

### Contributing To Gondolyn2

**All issues and pull requests will be reviewed in a timely manner.**

### Contributing To Laravel

**All issues and pull requests should be filed on the [laravel/framework](http://github.com/laravel/framework) repository.**

### License

The Gondolyn seed application is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
The Laravel framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
