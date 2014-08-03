<?php

/**
 * An example service description configuration for httpbin.
 *
 * @author     Bruce Walter <walter@fortean.com>
 * @copyright  Copyright (c) 2014
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */

return [

	'client' => [
	],

	'service' => [
		'defaults' => [
			'foo' => 'bar',
			'bat' => 'baz',
		],
	],

	'parameters' => [
		'global' => [
			'bat' => [
				'type' => 'string',
				'location' => 'query'
			],
		],
	],

	'description' => [
		'name' => 'httpbin(1): HTTP Request & Response Service',
		'apiVersion' => '1',
		'description' => 'Testing an HTTP Library can become difficult sometimes. Postbin is fantastic for testing POST requests, but not much else. This exists to cover all kinds of HTTP scenarios. Additional endpoints are being considered. All endpoint responses are JSON-encoded.',
		'baseUrl' => 'http://httpbin.org/',
		'operations' => [
			'testing' => [
				'httpMethod' => 'GET',
				'uri' => 'get',
				'responseModel' => 'getResponse',
				'parameters' => [
					'foo' => [
						'type' => 'string',
						'location' => 'query'
					],
					'bat' => 'global:bat',
				],
			],
		],
		'models' => [
			'getResponse' => [
				'type' => 'object',
				'additionalProperties' => [
					'location' => 'json'
				],
			],
		],
	],

];