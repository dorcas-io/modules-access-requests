<?php

Route::group(['namespace' => 'Dorcas\ModulesAccessRequests\Http\Controllers', 'middleware' => ['web']], function() {
    Route::get('sales', 'ModulesAccessRequestsController@index')->name('sales');
});


Route::group(['middleware' => ['auth'], 'prefix' => 'access-grants', 'namespace' => 'AccessGrants'], function () {
    Route::get('/', 'AccessGrantRequests@index')->name('access-grants');
    Route::get('/{id}', 'AccessGrantRequests@index');
    Route::post('/', 'AccessGrantRequests@post');
    Route::post('/{id}', 'AccessGrantRequests@post');
});

?>