<?php

namespace App\Helpers;

use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;

/**
 * RpcServer Class
 * Communication between the microservice
 */
class RpcServer
{
	private $connection;

	public function __construct(){
		$host = config('rabbitmq.connection.host');
        $port = config('rabbitmq.connection.port');
        $user = config('rabbitmq.connection.user');
        $password = config('rabbitmq.connection.password');
        $vhost = config('rabbitmq.connection.vhost');

        $this->connection = new AMQPStreamConnection($host, $port, $user, $password, $vhost);
	}

	public function handle($queue, $exchange, $callback){
		$consumer_tag = config('rabbitmq.connection.consumer');
		
        $channel = $this->connection->channel();

        $channel->queue_declare($queue, false, true, false, false);

        $channel->exchange_declare($exchange, AMQPExchangeType::DIRECT, false, true, false);

        $channel->queue_bind($queue, $exchange);

        $channel->basic_qos(null, 1, null);
        $channel->basic_consume($queue, $consumer_tag, false, false, false, false, $callback);

        // Loop as long as the channel has callbacks registered
        while ($channel ->is_consuming()) {
            $channel->wait();
        }

        $channel->close();
        $this->connection->close();
	}

	public function publish($request, $response){
		$body = json_encode($response);

        $message = new AMQPMessage($body, [
            'content_type' => 'text/plain',
            'correlation_id' => $request->get('correlation_id')
        ]);

        $request->delivery_info['channel']->basic_publish($message, '', $request->get('reply_to'));

        $request->delivery_info['channel']->basic_ack($request->delivery_info['delivery_tag']);
	}
}