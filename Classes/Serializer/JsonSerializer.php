<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Serializer;

/**
 * Class to serialize data to be sent
 */
class JsonSerializer implements SerializerInterface
{
    /**
     * Serialize the given data
     *
     * @param mixed $data
     * @return string if the data could not be serialized
     */
    public function serialize($data): string
    {
        $serializedData = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        if ($serializedData === false && json_last_error() !== JSON_ERROR_NONE) {
            throw $this->createExceptionFromLastError();
        }

        return $serializedData;
    }

    /**
     * Unserialize the given data
     *
     * @param string $input
     * @return mixed if the data could not be unserialized
     */
    public function unserialize(string $input)
    {
        // Just return NULL if the input is "null", to distinguish NULL and invalid inputs (which decode to NULL)
        if ($input === '' || $input === 'null') {
            return null;
        }
        $data = json_decode($input, true);
        if ($data === null) {
            //if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            throw $this->createExceptionFromLastError();
        }

        return $data;
    }

    /**
     * Returns an exception describing the last JSON error
     *
     * @return Exception
     */
    protected function createExceptionFromLastError()
    {
        if (!function_exists('json_last_error_msg')) {
            switch (json_last_error()) {
                case JSON_ERROR_DEPTH:
                    $errorMessage = 'Maximum stack depth exceeded';
                    break;
                case JSON_ERROR_STATE_MISMATCH:
                    $errorMessage = 'Underflow or the modes mismatch';
                    break;
                case JSON_ERROR_CTRL_CHAR:
                    $errorMessage = 'Unexpected control character found';
                    break;
                case JSON_ERROR_SYNTAX:
                    $errorMessage = 'Syntax error, malformed JSON';
                    break;
                case JSON_ERROR_UTF8:
                    $errorMessage = 'Malformed UTF-8 characters, possibly incorrectly encoded';
                    break;
                default:
                    $errorMessage = 'Unknown JSON error';
            }
        } else {
            $errorMessage = json_last_error_msg();
        }

        return new Exception($errorMessage, json_last_error());
    }

} 