<?php namespace Fortean\Festivus;

/**
 * @author     Bruce Walter <walter@fortean.com>
 * @copyright  Copyright (c) 2014
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */

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
	public function client($service)
	{
		// Load the requested service description
		$config = [
			'client' => config('festivus.'.$service.'.client'),
			'service' => config('festivus.'.$service.'.service'),
			'parameters' => config('festivus.'.$service.'.parameters'),
			'description' => config('festivus.'.$service.'.description'),
		];

		// Provide an XML parsing ResponseLocation that acts like Httpful
		$config['service']['response_locations']['xml-array'] = new PHPArrayXmlResponseLocation('xml-array');

		/*
		 * For complex REST API descriptions, parameters are often repeated.  We allow a shorthand way of
		 * defining parameters once and referencing them by name throughout all of the operations.
		 */
		if (isset($config['description']['operations']) && is_array($config['description']['operations']))
		{
			foreach ($config['description']['operations'] as $opName => $opConfig)
			{
				if (isset($opConfig['parameters']) && is_array($opConfig['parameters']))
				{
					foreach ($opConfig['parameters'] as $paramName => $paramConfig)
					{
						// In lieu of a possibly repetitions config array, check for a default description
						if (is_string($paramConfig) && preg_match('/^(.*?):(.*)$/', $paramConfig, $regs))
						{
							// Allow default parameters to be namespaced
							list($match, $namespace, $key) = $regs;

							// Use the default parameter configuration from the service section
							if (isset($config['parameters'][$namespace][$key]))
							{
								$config['description']['operations'][$opName]['parameters'][$paramName] = $config['parameters'][$namespace][$key];
							}
						}
					}
				}
			}
		}

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