<?php declare(strict_types=1);

namespace Colossal\Http;

use \Psr\Http\Message\MessageInterface;
use \Psr\Http\Message\StreamInterface;
use \UnexpectedValueException;

function isStringOrArrayOfStrings(mixed $value): bool
{
    if (is_string($value)) {
        return true;
    }

    if (is_array($value)) {
        foreach ($value as $val) {
            if (!is_string($val)) {
                return false;
            }
        }
        return true;
    }

    return false;
}

class Message implements MessageInterface
{
    const DEFAULT_PROTOCOL_VERSION      = "1.1";
    const SUPPORTED_PROTOCOL_VERSIONS   = ["1.0", "1.1"];

    private string $protocolVersion;
    private array  $headers;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->protocolVersion  = self::DEFAULT_PROTOCOL_VERSION;
        $this->headers          = [];
    }

    /**
     * @see MessageInterface::getProtocolVersion()
     */
    public function getProtocolVersion() : string
    {
        return $this->protocolVersion;
    }

    /**
     * @see MessageInterface::withProtocolVersion()
     */
    public function withProtocolVersion($version) : Message
    {
        if (!is_string($version)) {
            throw new \InvalidArgumentException("Argument 'version' must have type string.");
        }
        if (!in_array($version, self::SUPPORTED_PROTOCOL_VERSIONS)) {
            throw new \UnexpectedValueException("The protocol version $version is not a valid value.");
        }

        $newMessage = clone $this;
        $newMessage->protocolVersion = $version;

        return $newMessage;
    }

    /**
     * @see MessageInterface::getHeaders()
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @see MessageInterface::hasHeader()
     */
    public function hasHeader($name): bool
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException("Argument 'name' must have type string.");
        }

        foreach ($this->headers as $headerName => $_) {
            if (strcasecmp($name, $headerName) == 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * @see MessageInterface::getHeader()
     */
    public function getHeader($name)
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException("Argument 'name' must have type string.");
        }

        foreach ($this->headers as $headerName => $headerValues) {
            if (strcasecmp($name, $headerName) == 0) {
                return $headerValues;
            }
        }

        return [];
    }

    /**
     * @see MessageInterface::getHeaderLine()
     */
    public function getHeaderLine($name)
    {
        return implode(",", $this->getHeader($name));
    }

    /**
     * @see MessageInterface::withHeader()
     */
    public function withHeader($name, $value)
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException("Argument 'name' must have type string.");
        }
        if (!isStringOrArrayOfStrings($value)) {
            throw new \InvalidArgumentException("Argument 'value' must have type string or string[].");
        }

        $valueAsArray = is_array($value) ? $value : [$value];

        $nameToSetValuesFor = $this->getMatchingHeaderNameIfExistsOrDefault($name);

        $newMessage = clone $this;
        $newMessage->headers[$nameToSetValuesFor] = $valueAsArray;

        return $newMessage;
    }

    /**
     * @see MessageInterface::withAddedHeader()
     */
    public function withAddedHeader($name, $value)
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException("Argument 'name' must have type string.");
        }
        if (!isStringOrArrayOfStrings($value)) {
            throw new \InvalidArgumentException("Argument 'value' must have type string or string[].");
        }

        $valueAsArray = is_array($value) ? $value : [$value];

        $nameToSetValuesFor = $this->getMatchingHeaderNameIfExistsOrDefault($name);

        $newMessage = clone $this;
        $newMessage->headers[$nameToSetValuesFor] = array_merge(
            $this->getHeader($nameToSetValuesFor),
            $valueAsArray);

        return $newMessage;
    }

    /**
     * @see MessageInterface::withoutHeader()
     */
    public function withoutHeader($name)
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException("Argument 'name' must have type string.");
        }

        $nameToSplice = $this->getMatchingHeaderNameIfExistsOrDefault($name);

        $newMessage = clone $this;
        unset($newMessage->headers[$nameToSplice]);

        return $newMessage;
    }

    /**
     * @see MessageInterface::getBody()
     */
    public function getBody()
    {
        // TODO
    }

    /**
     * @see MessageInterface::withBody()
     */
    public function withBody(StreamInterface $body)
    {
        // TODO
    }

    /**
     * Performs a non case-sensitive search of all the current header names versus a name provided returning:
     *     - If a match is found    => The name of the matching header.
     *     - If no match is found   => The name provided.
     * @param string $name The name provided.
     * @return string Either the name of the matching header or the name provided.
     */
    private function getMatchingHeaderNameIfExistsOrDefault($name)
    {
        foreach ($this->headers as $headerName => $_) {
            if (strcasecmp($name, $headerName) == 0) {
                return $headerName;
            }
        }

        return $name;
    }
}