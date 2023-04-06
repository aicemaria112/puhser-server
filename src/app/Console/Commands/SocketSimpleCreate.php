<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Workerman\Worker;

class SocketSimpleCreate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $ws_worker = new Worker('websocket://0.0.0.0:2346');

        $connections = [];
        $array_massage=[];

        $ws_worker->onMessage = function ($connection, $message) use (&$connections, &$array_massage) {
            echo $message;
            try{
            $data = (array)json_decode($message);
            $protocol = $data['protocol'];
            $channel = $data['channel'];

            if ($protocol == 'subscribe') {
                $connections[$channel][] = $connection;
                echo "Subscribed to channel $channel\n";
            } else if ($protocol == 'publish') {
                $message = $data['message'];
                $con =0;
                foreach ($connections[$channel] as $key => $conn) {

                   try{
                    $conn->send($message);
                    $conn++;
                   }catch(Exception $exp){
                     unset($connections[$channel][$con]);
                   }
                }
                 $array_massage[$channel] = $message;
            }else if($protocol=='update'){
                try{
                $connection->send($array_massage[$channel]);
                }catch (Exception $exp){
                        $connection->send('{"protocol":"error","message":"No message stored in this chanel"}');
                }
            }
              $GLOBALS['connections']= $connections;
             }catch (\ErrorException $exp){
                        $connection->send("Undefinded Channel Name".$exp->getMessage());
            }


        };

        //     $array_a =  [];
            $ws_worker->onConnect = function ($connection) use (&$array_a) {
                echo "New connection\n";

            };

            $ws_worker->onClose = function ($connection) {
                echo "Connection closed\n";
            };
        // dd($ws_worker);
        // Run worker0
        Worker::runAll();
    }
}
