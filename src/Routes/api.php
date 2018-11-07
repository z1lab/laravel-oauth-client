<?php
/**
 * Created by Olimar Ferraz
 * webmaster@z1lab.com.br
 * Date: 06/11/2018
 * Time: 22:15
 */
Route::get('password-reset', 'ResetPasswordController@show')->name('reset-password');

Route::get('login', 'LoginController@index')->name('login');

Route::get('register', 'LoginController@register')->name('register');

Route::get('account-recovery', 'LoginController@accountRecovery')->name('account-recovery');

Route::get('email-confirmation', 'EmailConfirmationController@index')->name('email-confirmation');

Route::post('logout', 'LogoutController@logout')->name('logout');
