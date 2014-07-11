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
		// Load the requested service description
		$config = Config::get('laravel-festivus::'.$service, null);

		// Sanity check the configuration
		if (!is_array($config) || !isset($config['service']))
		{
			throw new OutOfBoundsException('Service configuration file is invalid or does not exist: '.$service);
		}

		// The config section is optional
		$config['config'] = (isset($config['config']) && is_array($config['config'])) ? $config['config'] : [];

		// Get the core Guzzle client
		$client = new Client();

		// Attach our event subscriber to it
		$client->getEmitter()->attach(new FestivusEventSubscriber);

		// Build a service description from the config file
		$description = new Description($config['service']);

		// Return a service client
		return new GuzzleClient($client, $description, $config['config']);
	}

}