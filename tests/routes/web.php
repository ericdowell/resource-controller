<?php

Route::resource('post', 'TestPostController');
Route::get('user/password/{user}/edit', ['as' => 'user.password-edit', 'uses' => 'TestUserController@passwordEdit']);
Route::put('user/password/{user}', ['as' => 'user.password-update', 'uses' => 'TestUserController@passwordUpdate']);
Route::resource('user', 'TestUserController');
Route::resource('user-update', 'TestUserUpdateController');
