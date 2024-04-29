<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Sarfraznawaz2005\Sse\SSE;
use Sarfraznawaz2005\Sse\Models\SSELog;
use App\Http\Controllers\ApostaController;
use App\Http\Controllers\StreamsController;
use App\Http\Controllers\RedisController;
use Symfony\Component\HttpFoundation\StreamedResponse;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('/redis', [RedisController::class, 'index']);
Route::get('/stream', [StreamsController::class, 'streamRandom']);

Route::get('/stream1', function () {
    return new StreamedResponse(function () {
        $counter = 0;
        while (true) {
            echo "data: " . json_encode(["value" => $counter++]) . "\n\n";
            ob_flush();
            flush();
            sleep(1); // Define um intervalo de tempo entre os eventos (opcional)
        }
    });
});