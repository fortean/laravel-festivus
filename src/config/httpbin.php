<?php

/**
 * An example service description configuration for httpbin.
 *
 * @author     Bruce Walter <walter@fortean.com>
 * @copyright  Copyright (c) 2014
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */

return [

	'config' => [
		'defaults' => [
			'foo' => 'bar',
		],
	],

	'service' => [
		'name' => 'httpbin(1): HTTP Request & Response Service',
		'apiVersion' => '1',
		'description' => 'Testing an HTTP Library can become difficult sometimes. Postbin is fantastic for testing POST requests, but not much else. This exists to cover all kinds of HTTP scenarios. Additional endpoints are being considered. All endpoint responses are JSON-encoded.',
		'baseUrl' => 'http://httpbin.org/',
		'operations' => [
			'testing' => [
				'httpMethod' => 'GET',
				'uri' => '/get',
				'responseModel' => 'getResponse',
				'parameters' => [
					'foo' => [
					    'type' => 'string',
					    'location' => 'query'
					],
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
