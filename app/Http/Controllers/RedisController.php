<?php


namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redis;
use Illuminate\Http\Request;


class RedisController extends Controller
{
    public function index()
    {
        Redis::set('user:1:first_name', 'Mike');
        Redis::set('user:1:first_name', 'Mike');
        Redis::set('user:1:first_name', 'Mike');
        return view('sse'); //for rendering sse blade file
    }


    public function sendSSE()
    {
        header('Content-Type: text/event-stream'); //set header text/event-stream for event streaming
            header('Cache-Control: no-cache');
        header('Connection: keep-alive');


        $data = [
            'diamond' => rand(200, 300),
            'gold' => rand(100, 200),
                'silver' => rand(50, 100),
                'bronze' => rand(1, 50),
            ]; //dummy data for metal amount.


            echo "data: " . json_encode($data) . "\n\n";
            ob_flush();
            flush();
    }
}