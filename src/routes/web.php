<?php

Route::group(['namespace' => 'Dorcas\ModulesAccessRequests\Http\Controllers', 'middleware' => ['web','auth'], 'prefix' => 'mpa'], function() {
    Route::get('access-requests-main', 'ModulesAccessRequestsController@index')->name('access-requests-main');
    Route::get('/access-grants-for-user', 'ModulesAccessRequestsController@searchByUser');
    Route::post('/access-requests-main', 'ModulesAccessRequestsController@post');
});

?>