<?php

Route::resource('post', 'TestPostController');
Route::group(['prefix' => 'user/password', 'group' => 'user.'], function () {
    Route::get('{user}/edit', ['as' => 'password-edit', 'uses' => 'UserController@passwordEdit']);
    Route::put('{user}', ['as' => 'password-update', 'uses' => 'UserController@passwordUpdate']);
});
Route::resource('user', 'TestUserController');
