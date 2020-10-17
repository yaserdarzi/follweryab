<?php

use Illuminate\Support\Facades\Route;

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
    return redirect('https://www.instagram.com/oauth/authorize?client_id=260333612055546&redirect_uri=https://followeryab.local/callback&scope=user_profile,user_media&response_type=code');
});

Route::get('callback', function () {
    $code = $_GET['code'];

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.instagram.com/oauth/access_token",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => array(
            'client_id' => '260333612055546',
            'client_secret' => '70cfcb8ce4134999b952382dc0524168',
            'grant_type' => 'authorization_code',
            'redirect_uri' => 'https://followeryab.local/callback',
            'code' => $code),
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    $response = json_decode($response);
    if ($instagram = \App\Models\Instagram::where('user_id', $response->user_id)->first()) {
        $instagram->access_token = $response->access_token;
        $instagram->save();
    } else {
        $instagram = \App\Models\Instagram::create([
            'user_id' => $response->user_id,
            'access_token' => $response->access_token
        ]);
    }
    return response()->json($instagram->user_id);
});

Route::get('/user/{id}', function ($id) {
    $instagram = \App\Models\Instagram::where('id', $id)->first();

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://graph.instagram.com/" . $instagram->user_id . "?fields=id,account_type,ig_id,media_count,username&access_token=" . $instagram->access_token,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    $response = json_decode($response);

    return response()->json($response);
});

Route::get('/user/{id}/media', function ($id) {
    $instagram = \App\Models\Instagram::where('id', $id)->first();

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://graph.instagram.com/" . $instagram->user_id . "/media?access_token=" . $instagram->access_token,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    $response = json_decode($response);

    return response()->json($response);
});
Route::get('/user/{id}/media/{media_id}', function ($id, $media_id) {
    $instagram = \App\Models\Instagram::where('id', $id)->first();

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://graph.instagram.com/" . $media_id . "?fields=caption,id,media_type,media_url,permalink,thumbnail_url,timestamp,username&access_token=" . $instagram->access_token,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    $response = json_decode($response);

    return response()->json($response);
});
