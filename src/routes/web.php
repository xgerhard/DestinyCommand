<?php
use App\OAuth\OAuthHandler;

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

Route::get('/api/command', 'CommandController@parseRequest');

Route::get('/tools/bungienameconverter', 'BungieNameConverterController@convert');
Route::post('/tools/bungienameconverter', 'BungieNameConverterController@convert');

Route::group(['prefix' => 'dashboard', 'middleware' => 'CheckOAuth:Bungie'], function()
{
    Route::get('/', function()
    {
        return view('dashboard/home', []);
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

Route::get('/dashboard/login', function () 
{
    $OAuthHandler = new OAuthHandler('Bungie');
    return view('dashboard/login', [
        'auth_url' => $OAuthHandler->getAuthUrl()
    ]);
});

Route::get('/dashboard/settings/nightbot/login', function () 
{
    $OAuthHandler = new OAuthHandler('Nightbot');
    return view('dashboard/nightbot/login', [
        'auth_url' => $OAuthHandler->getAuthUrl()
    ]);
});

Route::get('/', function () 
{   
    return view('welcome');
});
