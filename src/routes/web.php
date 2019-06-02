<?php

Route::group(['namespace' => 'Dorcas\ModulesAccessRequests\Http\Controllers', 'middleware' => ['web','auth'], 'prefix' => 'mpa'], function() {
    Route::get('access-requests-main', 'ModulesAccessRequestsController@index')->name('access-requests-main');
    Route::get('/access-grants-for-user', 'ModulesAccessRequestsController@searchByUser');
    //Route::delete('/access-grants-for-user/{id}', 'AccessGrantRequests@deleteRequestForUser');
});


Route::group(['middleware' => ['auth'], 'prefix' => 'access-grants', 'namespace' => 'AccessGrants'], function () {
    Route::get('/', 'AccessGrantRequests@index')->name('access-grants');
    Route::get('/{id}', 'AccessGrantRequests@index');
    Route::post('/', 'AccessGrantRequests@post');
    Route::post('/{id}', 'AccessGrantRequests@post');
});


//xhr
    //Route::get('/access-grants', 'AccessGrantRequests@search')->name('xhr.access-grants');
    //Route::delete('/access-grants/{id}', 'AccessGrantRequests@deleteRequest');

?>