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

Route::group(array('prefix' => 'errors'), function() {
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
Route::get('/pratt', "MainController@pratt");
Route::get('/change-log', "MainController@changelog");

/*
|--------------------------------------------------------------------------
| API
|--------------------------------------------------------------------------
*/

Route::group(array('prefix' => 'api'), function() {
    Route::group(array('before' => array('valid_api_key')), function() {
        Route::put('login', "ApiController@request");
    });

    Route::group(array('before' => array('valid_api_key', 'valid_api_token')), function() {
        Route::get('logout', "ApiController@logout");
        Route::get('user', "ApiController@getUserData");
    });
});

/*
|--------------------------------------------------------------------------
| Assets & Downloads
|--------------------------------------------------------------------------
*/

Route::get('public-asset/{encFileName}', "AssetController@asPublic");
Route::get('public-download/{encFileName}/{encRealFileName}', "AssetController@asDownload");
Route::get('module-asset/{module}/{path}/{contentType}', "AssetController@moduleAsset");

/*
|--------------------------------------------------------------------------
| Admins
|--------------------------------------------------------------------------
*/

Route::group(array('before' => 'is_logged_in', 'role' => 'admin'), function() {
    Accounts::setStripeKey(Config::get("gondolyn.stripe.secret_key"));

    Route::get('admin/home', "AdminController@home");
    Route::get('admin/users', "AdminController@users");
    Route::get('admin/users/create', "AdminController@creator");
    Route::get('admin/users/editor/{id}', "AdminController@editor");

    Route::get('admin/users/deactivate', "AdminController@deactivate");
    Route::get('admin/users/activate', "AdminController@activate");
    Route::get('admin/users/delete/{id?}', "AdminController@delete");

    Route::post('admin/users/generate', "AdminController@createAccount");
    Route::post('admin/users/update', "AdminController@update");

    // General FormMaker with Validation example
    Route::post('admin/submit/form',  array('uses' => 'AdminController@formSubmission'));
});

/*
|--------------------------------------------------------------------------
| Members
|--------------------------------------------------------------------------
*/

Route::group(array('before' => 'is_logged_in', 'role' => 'groups.all'), function() {
    Route::get('/member/home', "MemberController@home");
});

/*
|--------------------------------------------------------------------------
| Accounts
|--------------------------------------------------------------------------
*/

Route::group(array('prefix' => 'account', 'before' => 'is_logged_in', 'role' => 'groups.all'), function() {
    Accounts::setStripeKey(Config::get("gondolyn.stripe.secret_key"));

    Route::get('settings', "AccountController@settings");
    Route::get('settings/password', "AccountController@password");
    Route::get('settings/subscription', "SubscriptionController@subscription");
    Route::get('settings/subscription/invoices', "SubscriptionController@subscriptionInvoices");
    Route::get('settings/subscription/change-card', array('uses' => 'SubscriptionController@subscriptionChangeCard'));
    Route::get('settings/subscription/download/{id}', array('uses' => 'SubscriptionController@downloadInvoice'));

    Route::post('settings/update', array('uses' => 'AccountController@update'));
    Route::post('settings/update/password', array('uses' => 'AccountController@updatePassword'));
    Route::post('settings/set/subscription', array('uses' => 'SubscriptionController@setSubscription'));
    Route::post('settings/update/subscription', array('uses' => 'SubscriptionController@updateSubscription'));
    Route::post('settings/change-card/subscription', array('uses' => 'SubscriptionController@changeCardSubscription'));

    Route::get('delete/account', "AccountController@deleteAccount");
    Route::get('cancel/subscription', "SubscriptionController@cancelSubscription");

    Route::get('two-factor', "TwoFactorController@twoFactor");
    Route::post('two-factor/authenticate', "TwoFactorController@twoFactorAuthenticate");
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

Route::get('login/email', "AccountController@login");
Route::post('login/request', array('uses' => 'AccountController@withEmail'));
Route::get('login/confirm-email', "AccountController@loginConfirmEmail");
Route::get('login/confirm/{email}', "AccountController@confirmEmail");

Route::get('login/facebook', "AccountController@withFacebook");
Route::get('login/twitter', "AccountController@withTwitter");
Route::get('login/twitter/verify', "AccountController@loginTwitterVerify");
Route::post('login/twitter/verified', "AccountController@loginTwitterVerified");

Route::get('logout', "AccountController@logout");

/*
|--------------------------------------------------------------------------
| Forgot Password
|--------------------------------------------------------------------------
*/

Route::group(array('prefix' => 'forgot'), function() {
    Route::get('password', "AccountController@forgotPassword");
    Route::post('password/request', "AccountController@generateNewPassword");
});

/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/

Route::group(array('prefix' => 'dashboard', 'before' => 'is_logged_in', 'role' => 'groups.all'), function() {
    Route::get('', "DashboardController@main");
});

/*
|--------------------------------------------------------------------------
| App Specific
|--------------------------------------------------------------------------
*/

Route::group(array('before' => 'is_logged_in', 'role' => 'groups.all'), function() {

    // Put Routes Here

});
