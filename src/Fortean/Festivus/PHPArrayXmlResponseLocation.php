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

/**
 * Extracts elements from an XML document into a PHP array.
 */
class PHPArrayXmlResponseLocation extends AbstractLocation
{
    /** @var array The flattened XML document being visited */
    private $flatXML = [];

    public function before(
        GuzzleCommandInterface $command,
        ResponseInterface $response,
        Parameter $model,
        &$result,
        array $context = []
    ) {
        $body = $this->stripBom($response->getBody());
        $simple = ($xml = simplexml_load_string($body, null, 0, '')) ? [$xml->getName() => $xml] : [];
        $this->flatXML = json_decode(json_encode((array)$simple), 1);
    }

    public function after(
        GuzzleCommandInterface $command,
        ResponseInterface $response,
        Parameter $model,
        &$result,
        array $context = []
    ) {
        // Handle additional, undefined properties
        $additional = $model->getAdditionalProperties();
        if ($additional instanceof Parameter &&
            $additional->getLocation() == $this->locationName
        ) {
            foreach ($this->flatXML as $prop => $val) {
                if (!isset($result[$prop])) {
                    // Only recurse if there is a type specified
                    $result[$prop] = $additional->getType()
                        ? $this->recurse($additional, $val)
                        : $val;
                }
            }
        }

        $this->flatXML = [];
    }

    public function visit(
        GuzzleCommandInterface $command,
        ResponseInterface $response,
        Parameter $param,
        &$result,
        array $context = []
    ) {
        $name = $param->getName();
        $key = $param->getWireName();

        // Check if the result should be treated as a list
        if ($param->getType() == 'array') {
            // Treat as javascript array
            if ($name) {
                // name provided, store it under a key in the array
                $result[$name] = $this->recurse($param, $this->flatXML);
            } else {
                // top-level `array` or an empty name
                $result = array_merge($result, $this->recurse($param, $this->flatXML));
            }
        } elseif (isset($this->flatXML[$key])) {
            $result[$name] = $this->recurse($param, $this->flatXML[$key]);
        }
    }

    /**
     * Recursively process a parameter while applying filters
     *
     * @param Parameter $param API parameter being validated
     * @param mixed     $value Value to process.
     * @return mixed|null
     */
    private function recurse(Parameter $param, $value)
    {
        if (!is_array($value)) {
            return $param->filter($value);
        }

        $result = [];
        $type = $param->getType();

        if ($type == 'array') {
            $items = $param->getItems();
            foreach ($value as $val) {
                $result[] = $this->recurse($items, $val);
            }
        } elseif ($type == 'object' && !isset($value[0])) {
            // On the above line, we ensure that the array is associative and
            // not numerically indexed
            if ($properties = $param->getProperties()) {
                foreach ($properties as $property) {
                    $key = $property->getWireName();
                    if (isset($value[$key])) {
                        $result[$property->getName()] = $this->recurse(
                            $property,
                            $value[$key]
                        );
                        // Remove from the value so that AP can later be handled
                        unset($value[$key]);
                    }
                }
            }
            // Only check additional properties if everything wasn't already
            // handled
            if ($value) {
                $additional = $param->getAdditionalProperties();
                if ($additional === null || $additional === true) {
                    // Merge the JSON under the resulting array
                    $result += $value;
                } elseif ($additional instanceof Parameter) {
                    // Process all child elements according to the given schema
                    foreach ($value as $prop => $val) {
                        $result[$prop] = $this->recurse($additional, $val);
                    }
                }
            }
        }

        return $param->filter($result);
    }

    /**
     * Cheesy function to strip the UTF header from the incoming document.
     * This help makes XML output match that of the Httpful library.
     * 
     * @param  string $body The response body from the remote endpoint
     * @return [type]       [description]
     */
    protected function stripBom($body)
    {
        // Handle UTF encoded documents
        if (substr($body,0,3) === "\xef\xbb\xbf")
        {
            // UTF-8
            $body = substr($body,3);
        }
        else if (substr($body,0,4) === "\xff\xfe\x00\x00" || substr($body,0,4) === "\x00\x00\xfe\xff")
        {
            // UTF-32
            $body = substr($body,4);
        }
        else if (substr($body,0,2) === "\xff\xfe" || substr($body,0,2) === "\xfe\xff")
        {
            // UTF-16
            $body = substr($body,2);
        }

        return $body;
    }

}
