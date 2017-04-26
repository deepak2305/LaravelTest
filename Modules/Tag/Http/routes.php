<?php

Route::group(['middleware' => 'web', 'prefix' => 'app/tag', 'namespace' => 'Modules\Tag\Http\Controllers'], function()
{
    Route::get('', 'TagController@index');
    Route::post('/build', 'TagController@index');
});

