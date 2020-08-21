<?php

namespace App\Helpers;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * RpcClient Class
 * Communication between the microservice
 */
class RpcClient
{
    private $connection;
    private $channel;
    private $callback_queue;
    private $response;
    private $corr_id;
    public $queue;

    public function __construct($queue)
    {
    	$this->queue = $queue;

        $this->connection = new AMQPStreamConnection(
            config('rabbitmq.connection.host'),
            config('rabbitmq.connection.port'),
            config('rabbitmq.connection.user'),
            config('rabbitmq.connection.password')
        );
        $this->channel = $this->connection->channel();
        list($this->callback_queue, ,) = $this->channel->queue_declare(
            "",
            false,
            false,
            true,
            false
        );
        $this->channel->basic_consume(
            $this->callback_queue,
            '',
            false,
            true,
            false,
            false,
            [
                $this,
                'onResponse'
            ]
        );
    }

    public function onResponse($response){
        if ($response->get('correlation_id') == $this->corr_id) {
            $this->response = $response->body;
        }
    }

    public function call($request){
        $this->response = null;
        $this->corr_id = uniqid();

        $message = new AMQPMessage(
            $request,
            [
                'correlation_id' => $this->corr_id,
                'reply_to' => $this->callback_queue
            ]
        );
        $this->channel->basic_publish($message, '', $this->queue);
        while (!$this->response) {
            $this->channel->wait();
        }
        return $this->response;
    }
}