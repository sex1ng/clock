<?php

//获取用户信息
Route::any('2022060903', 'UserController@getPersonInfo');
//openid登录
Route::any('2024012901', 'UserController@openidLogin');

