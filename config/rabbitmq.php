<?php

return [
	// rabbitmq connection
	'connection' => [
		'host' => env('RABBITMQ_HOST', 'localhost'),
		'port' => env('RABBITMQ_PORT', 5672),
		'user' => env('RABBITMQ_USER', 'guest'),
		'password' => env('RABBITMQ_PASSWORD', 'guest'),
		'vhost' => env('RABBITMQ_VHOST', '/'),
		'consumer_tag' => env('RABBITMQ_CONSUMER_TAG', 'consumer'),
	],

	// rabbitmq service
	'test' => [
		'routes' => [
			'/v1.0/test' => [
				'method' => 'resource',
				'action' => 'TestController',
				'auth' => true,
				'except' => ['list', 'show']
			],
		],
		'rpc' => [
			'key' => 'test_rpc',
			'queue' => 'test_rpc_queue',
			'exchange' => 'test_rpc_exchange'
		]
	],
	'blog' => [
		'path' => '/v1.0/blog',
		'rpc' => [
			'key' => 'blog_rpc',
			'queue' => 'blog_rpc_queue',
			'exchange' => 'blog_rpc_exchange'
		]
	],
	'user' => [
		'path' => '/v1.0/user',
		'rpc' => [
			'key' => 'user_rpc',
			'queue' => 'user_rpc_queue',
			'exchange' => 'user_rpc_exchange'
		]
	]
];