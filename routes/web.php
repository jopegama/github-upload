<?php
use GuzzleHttp\Client;

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

Route::get('/', function () {
    return view('welcome');
});
//	https://dev.azure.com/altamiraarep/arep/_apis/test/plans?api-version=5.0-preview.2
//	pepe:ldgqzgxkofp5g42tgtlkcfkwupnsqe2jzu4kgmhsgipb6nfzisgq
Route::get('arep', function()
{
	$client = new Client([
		'base_uri' => 'https://dev.azure.com/altamiraarep/_apis/',
		'timeout'  => 2.0,
	]);	
	$response = $client->request('GET',
                                 'projects',
								 ['query' => ['api-version' => '2.0'],
								 'auth' => ['pepe', 'ldgqzgxkofp5g42tgtlkcfkwupnsqe2jzu4kgmhsgipb6nfzisgq']]);
    $content = $response->getBody()->getContents();
	$json =  json_decode( $content );
	return $content;
});
Route::get('arep2', function()
{
	$client = new Client([
		'base_uri' => 'https://dev.azure.com/altamiraarep/arep/_apis/',
		'timeout'  => 2.0,
	]);	
	$response = $client->request('GET',
                                 'test/runs',
								 ['query' => ['planId' => '531', 'includeRunDetails' => 'false', 'api-version' => '5.0-preview.2'],
								 'auth' => ['pepe', 'ldgqzgxkofp5g42tgtlkcfkwupnsqe2jzu4kgmhsgipb6nfzisgq']]);
    $content = $response->getBody()->getContents();
	$json =  json_decode( $content );
	return $content;
});
Route::get('arepx', 'azureDevops@testRuns');

Route::get('rest', function()
{
	$client = new Client([
		'base_uri' => 'http://jsonplaceholder.typicode.com',
		'timeout'  => 2.0,
	]);
	$response = $client->request('GET', 'posts');
//	dd($response);
    $content = $response->getBody()->getContents();
	$json =  json_decode( $content );
    return $json;
});

Route::get('users', function()
{
    return 'Users! (Sólo devuelve este literal sin hacer nada más)';
});

Route::get('users2', function()
{
    return View::make('users');
});

Route::get('users3', function()
{
    return View::make('users3');
});

Route::get('users4', function()
{
    $users = App\Models\User::all();
    return View::make('users4')->with('users', $users);
});

Route::get('users5', function()
{
    $users = App\Models\User::all();
    return View::make('users5')->with('users', $users);
});
Route::get('monitor', 'Monitor\MonitorController@minuto');
Route::get('minuto1', function()
{
    $minutos = App\Models\Minuto::where('MinAbsUTC', '<', 1054054)->get();
//    $minutos = App\Models\Minuto::where('MinAbsUTC', 1054054)->get();
//    $minutos = App\Models\Minuto::first();
//	echo json_encode( [ $minutos ] );
    return View::make('minuto1')->with('minutos', $minutos);
});
?>