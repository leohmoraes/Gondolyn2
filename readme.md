## Gondolyn

![CodeShip Status](https://www.codeship.io/projects/fbbc0390-c6c2-0131-e065-0277a4446f20/status)

Gondolyn is a seed application built with [Laravel](http://laravel.com), [Bootstrap](http://getbootstrap.com), [RawCSS](https://github.com/mlantz/rawCSS) and [jQuery](http://jquery.com). It comes with Oauth set so that logins can be done with Email, Facebook or Twitter. It comes with an SQLite database with the user table pre-built. Gondolyn also utilizes the [Creolab modules package](https://github.com/creolab/laravel-modules), allowing for HMVC application structure. Finally it comes with API structure featuring the login method.

#Installation & Setup

```
composer create-project mlantz/gondolyn
```

If you wish to switch to a MySQL databse, set the database parameters in app/config/database and then run the following commands:

```
php artisan dbuild
php artisan migrate
```

## Official Documentation for Laravel

Documentation for the entire framework can be found on the [Laravel website](http://laravel.com/docs).

### Gondolyn Documentation

Documentation for the entire framework can be found on the [Gondolyn website](http://mlantz.github.io/Gondolyn/).

### Contributing To Gondolyn

**All issues and pull requests will be reviewed in a timely manner.**

### Contributing To Laravel

**All issues and pull requests should be filed on the [laravel/framework](http://github.com/laravel/framework) repository.**

### License

The Gondolyn seed application is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
The Laravel framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
