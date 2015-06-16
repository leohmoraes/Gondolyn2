## Gondolyn2

![CodeShip Status](https://codeship.com/projects/4157cbc0-bb7a-0132-d17b-3e819ad11634/status?branch=master) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mlantz/Gondolyn2/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/mlantz/Gondolyn2/?branch=master)

Gondolyn2 is a seed application built with [Laravel](http://laravel.com), [Bootstrap](http://getbootstrap.com), [RawCSS](https://github.com/mlantz/rawCSS) and [jQuery](http://jquery.com). It comes with Oauth set so that logins can be done with Email, Facebook or Twitter, or others. It comes with an SQLite database with the user table pre-built. Allowing for HMVC application structure Gondolyn2 enables developers to reuse components and add them via composer. It also uses a methodology called MSCR (Model - Service - Controller - Response), this makes unit testing even easier since you can build your code focusing all your tests on the service layer which would contain all the buisness logic of your application. This means that controllers load your service providers and corresponding services and responses/views are doing little more the moving processed information around and displaying it. Finally it comes with basic Token API structure featuring the login, user data, and logout methods. For more information about Gondolyn2 and some of the modules available for the structure please checkout: [http://gondolyn.com](http://gondolyn.com).

#Installation & Setup

```
composer create-project mlantz/gondolyn2
```

If you wish to switch to a MySQL databse, set the database parameters in app/config/database and then run the following commands:

```
php artisan gondolyn:dbuild
php artisan migrate
```

#Commands

```
// Builds a module
php artisan gondolyn:module <name> <option (--table=true - creates a basic database table as well)>
```

## Official Documentation for Laravel

Documentation for the entire framework can be found on the [Laravel website](http://laravel.com/docs).

### Gondolyn2 Documentation

Documentation for the entire framework can be found on the [Gondolyn2 website](http://gondolyn.com).

### Contributing To Gondolyn2

**All issues and pull requests will be reviewed in a timely manner.**

### Contributing To Laravel

**All issues and pull requests should be filed on the [laravel/framework](http://github.com/laravel/framework) repository.**

### License

The Gondolyn2 seed application is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
The Laravel framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
