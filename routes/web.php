<?php
Route::group(['middleware' => 'language'], function () {
	Route::group(['middleware' => 'auth'], function () {

	    Route::group(['middleware' => ['adminmenu', 'permission:read-admin-panel']], function () {
	        Route::group(['prefix' => 'milk'], function () {
	            Route::resource('production', 'Production', ['middleware' => ['dateformat', 'money']]);
	            
	        });
	    });
	});	

	Route::group(['middleware' => 'guest'], function () {

        Route::group(['prefix' => 'auth', 'namespace'=>'\ErpNET\ProfitingCalendar\Http\Controllers\Auth'], function () {
            //Route::get('login', 'Login@create')->name('login');
            //Route::post('login', 'Login@store');

            //Route::get('forgot', 'Forgot@create')->name('forgot');
            //Route::post('forgot', 'Forgot@store');

            //Route::get('reset', 'Auth\Reset@create');
            //Route::get('reset/{token}', 'Reset@create')->name('reset');
            //Route::post('reset', 'Reset@store');
        });

	});	
});