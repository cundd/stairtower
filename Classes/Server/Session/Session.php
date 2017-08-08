<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Server\Session;


use Cundd\Stairtower\Domain\Model\Exception\InvalidDataException;
use Cundd\Stairtower\Exception\MissingExtensionException;
use Cundd\Stairtower\LogicException;
use Cundd\Stairtower\Utility\GeneralUtility;
use Cundd\Stairtower\Utility\ObjectUtility;

/**
 * Session instance
 */
class Session implements SessionInterface
{
    /**
     * Unique session identifier
     *
     * @var string
     */
    protected $identifier;

    /**
     * Storage for the session data
     *
     * @var array
     */
    protected $data;

    /**
     * Creates a new session object with the given identifier (session ID)
     *
     * @param string $identifier
     * @param array  $data
     */
    function __construct($identifier = null, $data = [])
    {
        $this->identifier = $identifier ?: $this->generateSessionId();
        $this->data = $data;
    }

    /**
     * Returns the session identifier
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Returns the value for the given key from the data
     *
     * @param string $key
     * @return mixed
     */
    public function valueForKey($key)
    {
        if ($key === 'identifier') {
            return $this->identifier;
        }
        if (isset($this->data[$key])) {
            return $this->data[$key];
        }

        return null;
    }


    /**
     * Sets the value for the given key from the data
     *
     * @param mixed  $value
     * @param string $key
     * @throws LogicException
     */
    public function setValueForKey($value, $key)
    {
        if (!is_string($key)) {
            throw new LogicException(
                'Given key path is not of type string (maybe arguments are ordered incorrect)',
                1395484136
            );
        }
        $this->data[$key] = $value;
    }

    /**
     * Returns the value for the given key path from the data
     *
     * @param string $keyPath
     * @return mixed
     */
    public function valueForKeyPath($keyPath)
    {
        if (!strpos($keyPath, '.')) {
            return $this->valueForKey($keyPath);
        }

        return ObjectUtility::valueForKeyPathOfObject($keyPath, $this->getData());
    }

    /**
     * Returns the underlying data
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Returns the underlying data
     *
     * @param array $data
     */
    public function setData($data)
    {
        $this->assertDataType($data);
        $this->data = $data;
    }

    /**
     * Assert if the data type is array
     *
     * @param array $data
     */
    protected function assertDataType($data)
    {
        if (!is_array($data)) {
            throw new InvalidDataException(
                sprintf('Given data is not of type array but %s', GeneralUtility::getType($data)),
                1423687533
            );
        }
    }

    /**
     * Generate a session ID
     *
     * @return string
     */
    protected function generateSessionId()
    {
        if (!is_callable('openssl_random_pseudo_bytes')) {
            throw new MissingExtensionException('OpenSSL is not enabled', 1428151048);
        }

        return bin2hex(openssl_random_pseudo_bytes(24));
    }
}
