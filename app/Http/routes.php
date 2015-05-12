<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

/*
|--------------------------------------------------------------------------
| Errors
|--------------------------------------------------------------------------
*/

Route::group(array('prefix' => 'errors'), function () {
    Route::get('/', "ErrorController@general");
    Route::get('/general', "ErrorController@general");
    Route::get('/critical', "ErrorController@critical");
});

/*
|--------------------------------------------------------------------------
| Home & General Pages
|--------------------------------------------------------------------------
*/

Route::get('/', "MainController@welcome");
Route::get('/change-log', "MainController@changelog");

/*
|--------------------------------------------------------------------------
| API
|--------------------------------------------------------------------------
*/

Route::group(array('prefix' => 'api'), function () {
    Route::put('login', "ApiController@request");

    Route::group(array('before' => array('valid_api_key', 'valid_api_token')), function () {
        Route::get('logout', "ApiController@logout");
        Route::get('user', "ApiController@getUserData");
    });
});

/*
|--------------------------------------------------------------------------
| Admins
|--------------------------------------------------------------------------
*/

Route::group(array('before' => 'is_logged_in', 'role' => 'admin'), function () {
    Users::setStripeKey(Config::get("gondolyn.stripe.secret_key"));

    Route::get('admin/home', "AdminController@home");
    Route::get('admin/users', "AdminController@users");
    Route::get('admin/editor/{id}', "AdminController@editor");

    Route::get('admin/deactivate', "AdminController@deactivate");
    Route::get('admin/activate', "AdminController@activate");
    Route::get('admin/delete/user', "AdminController@delete");

    Route::post('admin/update', "AdminController@update");
});

/*
|--------------------------------------------------------------------------
| Members
|--------------------------------------------------------------------------
*/

Route::group(array('before' => 'is_logged_in', 'role' => 'groups.all'), function () {
    Route::get('/member/home', "MemberController@home");
});

/*
|--------------------------------------------------------------------------
| Users
|--------------------------------------------------------------------------
*/

Route::group(array('prefix' => 'user', 'before' => 'is_logged_in', 'role' => 'groups.all'), function () {
    Users::setStripeKey(Config::get("gondolyn.stripe.secret_key"));

    Route::get('settings', "UserController@settings");
    Route::get('settings/password', "UserController@password");
    Route::get('settings/subscription', "UserController@subscription");
    Route::get('settings/subscription/invoices', "UserController@subscriptionInvoices");
    Route::get('settings/subscription/download/{id}', array('uses' => 'UserController@downloadInvoice'));

    Route::post('settings/update', array('uses' => 'UserController@update'));
    Route::post('settings/update/password', array('uses' => 'UserController@updatePassword'));
    Route::post('settings/set/subscription', array('uses' => 'UserController@setSubscription'));
    Route::post('settings/update/subscription', array('uses' => 'UserController@updateSubscription'));

    Route::get('delete/account', "UserController@deleteUserAccount");
    Route::get('cancel/subscription', "UserController@cancelSubscription");
});

/*
|--------------------------------------------------------------------------
| Failed Payments
|--------------------------------------------------------------------------
*/

Route::post('failed/payment', 'Laravel\Cashier\WebhookController@handleWebhook');

/*
|--------------------------------------------------------------------------
| Login / Join
|--------------------------------------------------------------------------
*/

Route::get('login/email', "UserController@login");
Route::post('login/request', array('uses' => 'UserController@withEmail'));

Route::get('login/facebook', "UserController@withFacebook");
Route::get('login/twitter', "UserController@withTwitter");
Route::get('login/twitter/verify', "UserController@loginTwitterVerify");
Route::post('login/twitter/verified', "UserController@loginTwitterVerified");

Route::get('logout', "UserController@logout");

/*
|--------------------------------------------------------------------------
| Forgot Password
|--------------------------------------------------------------------------
*/

Route::group(array('prefix' => 'forgot'), function () {
    Route::get('password', "UserController@forgotPassword");
    Route::post('password/request', "UserController@generateNewPassword");
});

/*
|--------------------------------------------------------------------------
| App Specific
|--------------------------------------------------------------------------
*/

Route::group(array('before' => 'is_logged_in', 'role' => 'groups.all'), function () {

    // Put Routes Here

});
