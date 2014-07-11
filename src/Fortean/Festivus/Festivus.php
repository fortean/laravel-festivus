<?php namespace Fortean\Festivus;

/**
 * @author     Bruce Walter <walter@fortean.com>
 * @copyright  Copyright (c) 2014
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */

use Config;
use OutOfBoundsException;
use GuzzleHttp\Client;
use GuzzleHttp\Command\Guzzle\GuzzleClient;
use GuzzleHttp\Command\Guzzle\Description;

class Festivus {

	/**
	 * Return a Guzzle Services client based on the requested service
	 *
	 * @param  string  $service
	 * @return GuzzleHttp\Command\Guzzle\GuzzleClient
	 */
	public function client($service = null)
	{
		/*
		 * Load the requested service description... First check the cascade namespace, then the normal package namespace.
		 * This allows us to have package configs in the override that do not exist in the vendor config.
		 */
		$config = ($config = Config::get('laravel-festivus-cascade::'.$service, null)) ? $config : Config::get('laravel-festivus::'.$service, null);

		// Sanity check the configuration
		if (!is_array($config) || !isset($config['service']))
		{
			throw new OutOfBoundsException('Service configuration file is invalid or does not exist: '.$service);
		}

		// The client and service sections are optional
		$config['client'] = (isset($config['client']) && is_array($config['client'])) ? $config['client'] : [];
		$config['service'] = (isset($config['service']) && is_array($config['service'])) ? $config['service'] : [];

		// Provide an XML parsing ResponseLocation that acts like Httpful
		$config['service']['response_locations']['xml-festivus'] = new FestivusXmlResponseLocation('xml-festivus');

		// Get the core Guzzle client
		$client = new Client($config['client']);

		// Attach our event subscriber to it
		$client->getEmitter()->attach(new FestivusEventSubscriber);

		// Build a service description from the config file
		$description = new Description($config['description']);

		// Return a service client
		return new GuzzleClient($client, $description, $config['service']);
	}

}