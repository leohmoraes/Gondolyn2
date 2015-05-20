<?php

    /*
    |--------------------------------------------------------------------------
    | Module Routes
    |--------------------------------------------------------------------------
    */

    Route::group(array('module' => 'Sample', 'namespace' => 'App\Modules\Sample\Controllers'), function() {
        Route::get('sample',  array('uses' => 'SampleController@main'));

        // API actions
        Route::group(array('prefix' => 'api', 'before' => array('valid_api_key', 'valid_api_token')), function() {
            Route::get('sample',  array('uses' => 'SampleController@api'));
        });
    });
