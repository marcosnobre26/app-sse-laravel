<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Trade;
use Illuminate\Support\Facades\Cache;

class StreamsController extends Controller
{
    /**
     * The stream source.
     *
     * @return \Illuminate\Http\Response
     */
    public function stream(){
        return response()->stream(function () {
            while (true) {
                echo "event: ping\n";
                $curDate = date(DATE_ISO8601);
                echo 'data: {"time": "' . $curDate . '"}';
                echo "\n\n";

                $trades = Trade::latest()->get();
                echo 'data: {"total_trades":' . $trades->count() . '}' . "\n\n";

                $latestTrades = Trade::with('user', 'stock')->latest()->first();
                if ($latestTrades) {
                    echo 'data: {"latest_trade_user":"' . $latestTrades->user->name . '", "latest_trade_stock":"' . $latestTrades->stock->symbol . '", "latest_trade_volume":"' . $latestTrades->volume . '", "latest_trade_price":"' . $latestTrades->stock->price . '", "latest_trade_type":"' . $latestTrades->type . '"}' . "\n\n";
                }

                ob_flush();
                flush();

                // Break the loop if the client aborted the connection (closed the page)
                if (connection_aborted()) {break;}
                usleep(50000); // 50ms
            }
        }, 200, [
            'Cache-Control' => 'no-cache',
            'Content-Type' => 'text/event-stream',
        ]);
    }

    public function streamRandom()
    {

         // Abre o cache do Redis para escrita
        Cache::store('redis')->put('stream_data', '', 10); // chave vazia, expiração de 10 minutos

        $cachedData = Cache::store('redis')->get('stream_data');
        //echo($cachedData);
        return response()->stream(function () {
            while (true) {
                echo "\n\n";

                // Gerar um número aleatório a cada 5 segundos
                $randomNumber = rand(1, 100);
                echo 'data:'. $randomNumber;

                // Limpar o buffer de saída e enviar os dados imediatamente
                ob_flush();
                flush();

                // Break the loop if the client aborted the connection (closed the page)
                if (connection_aborted()) {
                    break;
                }
                
                // Aguardar 5 segundos antes de continuar para a próxima iteração
                sleep(2);
            }
        }, 200, [
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Content-Type' => 'text/event-stream',
            'Connection' => 'keep-alive',
        ]);
    }
}