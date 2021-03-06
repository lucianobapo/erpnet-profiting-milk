<?php
Route::group(['middleware' => 'web','language'], function () {
    
	Route::group(['middleware' => 'auth'], function () {
	    Route::group(['middleware' => ['adminmenu', 'permission:read-admin-panel']], function () {
	        Route::group(['prefix' => 'milk', 'namespace'=>'\ErpNET\Profiting\Milk\Http\Controllers'], function () {
	            Route::resource('production', 'Productions', ['middleware' => ['dateformat', 'money']]);
	            
	            Route::post('production/import', 'Productions@import')->name('production.import');
	            Route::get('production/export', 'Productions@export')->name('production.export');
	            Route::get('production/{production}/duplicate', 'Productions@duplicate')->name('production.duplicate');
	        });
	    });
	});	
	
/*
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
	*/
});