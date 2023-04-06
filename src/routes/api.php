<?php

use App\Events\MessageSent;
use BeyondCode\LaravelWebSockets\Console\StartWebSocketServer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use BeyondCode\LaravelWebSockets\Facades\WebSocketsRouter;
use WebSocket\Client;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/



Route::post('send',function(Request $request){

    $protocol = $request->input('protocol');
    $channel = $request->input('channel');
    $message = $request->input('message');
    //$data = $request->input('data');
    try {
    $client = new Client("ws://127.0.0.1:2346");

    $client->text("{\"protocol\": \"$protocol\", \"channel\": \"$channel\", \"message\": \"$message\"}");

    $client->close();
    }catch(Exception $exp){
        $obj = ['success'=>false,'message'=> $exp->getMessage()];
    }
});
