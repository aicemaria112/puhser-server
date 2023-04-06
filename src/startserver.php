<?php

use Workerman\Worker;
use Workerman\Timer;
require_once __DIR__ . '/vendor/autoload.php';


$context = array(
    'ssl' => array(
        'local_cert' => './certs/root_portal.crt',
        'local_pk'   => './certs/root_portal.key',
        )
    );

$ws_worker = new Worker('websocket://bienestar-estable-vcl.prod.xutil.cu:6006', $context);

$connections = [];
$array_massage = [];

// $a = '{"type": "notification" ,"message":"Se ha realizado satisfactoriamente su reservación para la fecha: 17-04-2023 en el servicio: Inscripción del establecimiento: Registro de la Propiedad Ciego de Ávila en el turno: 8:30-12:00."}';
// dd(json_decode($a));
//dd(json_decode('{"protocol": "publish", "channel": "yanehrosell@gmail.com", "message": "a"}'));
$ws_worker->transport = 'ssl';


$ws_worker->onMessage = function ($connection, $message) use (&$connections, &$array_massage) {
    echo $message."\n";

    try {
        $data = (array) json_decode($message);
        //var_dump($data)."\n";

        $protocol = $data['protocol'];

        if($data['protocol']=='pong'){
            echo "PONG RECEIVED \n";
        }else{
        $channel = $data['channel'];

        if ($protocol == 'subscribe') {
            $connections[$channel][] = $connection;
            echo "Subscribed to channel $channel\n";
        } else if ($protocol == 'publish') {
            $message = base64_decode($data['message'])==false ? $data['message'] : base64_decode($data['message']);
            $con = 0;
            foreach ($connections[$channel] as $key => $conn) {

                try {
                    $conn->send($message);
                    $con++;
                } catch (Exception $exp) {
                    unset($connections[$channel][$con]);
                }
            }
            $array_massage[$channel] = $message;
        } else if ($protocol == 'update') {
            try {
                $connection->send($array_massage[$channel]);
                unset($array_massage[$channel]);
            } catch (Exception $exp) {
                $connection->send('{"protocol":"error","message":"No message stored in this chanel"}');
            }
        }
    }
        $GLOBALS['connections'] = $connections;
    } catch (\ErrorException $exp) {
        $connection->send("Undefinded Channel Name" . $exp->getMessage());
    }
};

 $array_timmer = [];
$ws_worker->onConnect = function ($connection) use (&$array_a, &$array_timmer) {
    echo "New connection\n";
    $time_interval = 59;
    $timer_id = Timer::add($time_interval, function () use ($connection) {
        echo "Timer run\n";
        try{
        $connection->send('{"protocol":"ping","message":"ping"}');
        }catch(Exception $exp){
           echo "error";
        }
    });
    $array_timmer[$timer_id]= $connection;
};

$ws_worker->onClose = function ($connection) use(&$array_timmer){
    Timer::del(array_search($connection,$array_timmer));
};
// dd($ws_worker);
// Run worker
Worker::runAll();
