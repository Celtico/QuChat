<?php

    $id = '127.0.0.1'; $port = '9000';

    chdir(dirname(__DIR__));

    // Setup autoloading
    require 'init_autoloader.php';

    use Ratchet\Server\IoServer;
    use Ratchet\WebSocket\WsServer;
    use Ratchet\Wamp;
    use React\EventLoop\Factory;
    use React\Socket\Server as Reactor;
    use QuChat\Server\QuChatNoData;

    $loop    = Factory::create();
    $webSock = new Reactor($loop);

    try{
        $webSock->listen($port,$id);
        echo 'CONNETD';
    }catch(Exception $e){
        echo 'Message: ' .$e->getMessage();
    }

    $webServer = new IoServer(new WsServer(new QuChatNoData()),$webSock);

    $loop->run();