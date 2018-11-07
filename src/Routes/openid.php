<?php
/**
 * Created by Olimar Ferraz
 * webmaster@z1lab.com.br
 * Date: 06/11/2018
 * Time: 22:05
 */
Route::get('callback', 'CallbackController@callback')->name('callback');

Route::get('user', 'ApiController@show')->name('user');

Route::post('refresh', 'RefreshController@refreshToken')->name('refresh');
