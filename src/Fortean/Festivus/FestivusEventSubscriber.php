<?php namespace Fortean\Festivus;

/**
 * @author     Bruce Walter <walter@fortean.com>
 * @copyright  Copyright (c) 2014
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */

use Event;
use GuzzleHttp\Event\SubscriberInterface;

class FestivusEventSubscriber implements SubscriberInterface
{

	public function getEvents()
	{
		return [
			'before' => ['eventHandler'],
			'after' => ['eventHandler'],
			'headers' => ['eventHandler'],
			'complete' => ['eventHandler'],
			'error' => ['eventHandler'],
		];
	}

	public function eventHandler($event, $name)
	{
		Event::fire('festivus.client.'.$name, array($event));
	}

}