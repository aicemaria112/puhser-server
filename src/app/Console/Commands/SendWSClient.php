<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use WebSocket\Client;
use Workerman\Protocols\Websocket;
use Workerman\Worker;

class SendWSClient extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:sw';

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
        $client = new Client("ws://10.50.4.75:6006");

        // $client->text('{"protocol": "publish", "channel": "channel_1", "message": "Ciente de solo escritura"}');
        // $client->text('{"protocol": "publish", "channel": "channel_2", "message": "Ciente de solo escritura"}');
        $client->text('{"protocol": "publish", "channel": "channel_3", "message": "Ciente de solo escritura"}');

        $client->close();
    }
}
