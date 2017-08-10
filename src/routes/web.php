<?php

use App\OAuthProvider;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/auth/{service}', 'AuthController@AuthHandler');

Route::get('/api/command', 'CommandController@CommandHandler');

Route::group(['prefix' => 'dashboard', 'middleware' => 'CheckOAuth:Bungie'], function()
{
    Route::get('/', function()
	{
		echo 'dashboard home';
    });

	Route::prefix('settings')->group(function()
    {
		Route::get('/', function()
		{
			echo 'settings';
		});
		
		Route::get('/nightbot', ['middleware' => 'CheckOAuth:Nightbot', function()
		{
			echo 'nightbot settings';
		}]);
    });

});

Route::get('/', function () 
{	
    return view('welcome');
});
