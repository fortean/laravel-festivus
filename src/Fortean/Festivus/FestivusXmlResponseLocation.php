<?php namespace Fortean\Festivus;

/**
 * This response location provides XML conversion similar to the Httpful library.
 *
 * @author     Bruce Walter <walter@fortean.com>
 * @copyright  Copyright (c) 2014
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */

use GuzzleHttp\Command\Guzzle\ResponseLocation\AbstractLocation;
use GuzzleHttp\Command\Guzzle\Parameter;
use GuzzleHttp\Message\ResponseInterface;
use GuzzleHttp\Command\Guzzle\GuzzleCommandInterface;

class FestivusXmlResponseLocation extends AbstractLocation
{
	private $body;

	public function before(GuzzleCommandInterface $command, ResponseInterface $response, Parameter $model, &$result, array $context = [])
	{
		$this->body = $this->stripBom($response->getBody());
	}

	public function after(GuzzleCommandInterface $command, ResponseInterface $response, Parameter $model, &$result, array $context = [])
	{
		$result = ($xml = simplexml_load_string($this->body, null, 0, '')) ? [$xml->getName() => $xml] : []
		$this->body = null;
	}

    protected function stripBom($body)
    {
        if ( substr($body,0,3) === "\xef\xbb\xbf" ) // UTF-8
            $body = substr($body,3);
        else if ( substr($body,0,4) === "\xff\xfe\x00\x00" || substr($body,0,4) === "\x00\x00\xfe\xff" ) // UTF-32
            $body = substr($body,4);
        else if ( substr($body,0,2) === "\xff\xfe" || substr($body,0,2) === "\xfe\xff" ) // UTF-16
            $body = substr($body,2);
        return $body;
    }
}