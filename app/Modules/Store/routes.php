<?php

    /*
    |--------------------------------------------------------------------------
    | Module Routes
    |--------------------------------------------------------------------------
    */

    Route::group(array('module' => 'Store', 'namespace' => 'App\Modules\Store\Controllers'), function()
    {
        Route::get('store',  array('uses' => 'StoreController@main'));

        // API actions
        Route::group(array('prefix' => 'api', 'before' => array('valid_api_key', 'valid_api_token')), function()
        {
            Route::get('store',  array('uses' => 'StoreController@api'));
        });

    });