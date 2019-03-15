<?php

namespace JorisvanW\DeepL\Api\Exceptions;

use Throwable;
use GuzzleHttp\Psr7\Response;

class ApiException extends \Exception
{
    /**
     * @var string
     */
    protected $field;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var array
     */
    protected $links = [];

    /**
     * @param string                         $message
     * @param int                            $code
     * @param string|null                    $field
     * @param \GuzzleHttp\Psr7\Response|null $response
     * @param \Throwable|null                $previous
     *
     * @throws \JorisvanW\DeepL\Api\Exceptions\ApiException
     */
    public function __construct(
        $message = '',
        $code = 0,
        $field = null,
        Response $response = null,
        Throwable $previous = null
    ) {
        if (!empty($field)) {
            $this->field = (string)$field;
            $message     .= ". Field: {$this->field}";
        }

        if (!empty($response)) {
            $this->response = $response;

            $object = static::parseResponseBody($this->response);

            if (isset($object->_links)) {
                foreach ($object->_links as $key => $value) {
                    $this->links[$key] = $value;
                }
            }
        }

        parent::__construct($message, $code, $previous);
    }

    /**
     * @param \GuzzleHttp\Exception\RequestException $guzzleException
     * @param \Throwable                             $previous
     *
     * @return \JorisvanW\DeepL\Api\Exceptions\ApiException
     * @throws \JorisvanW\DeepL\Api\Exceptions\ApiException
     */
    public static function createFromGuzzleException($guzzleException, Throwable $previous = null)
    {
        // Not all Guzzle Exceptions implement hasResponse() / getResponse()
        if (method_exists($guzzleException, 'hasResponse') && method_exists($guzzleException, 'getResponse')) {
            if ($guzzleException->hasResponse()) {
                return static::createFromResponse($guzzleException->getResponse());
            }
        }

        return new static($guzzleException->getMessage(), $guzzleException->getCode(), null, $previous);
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param \Throwable|null                     $previous
     *
     * @return \JorisvanW\DeepL\Api\Exceptions\ApiException
     * @throws \JorisvanW\DeepL\Api\Exceptions\ApiException
     */
    public static function createFromResponse($response, Throwable $previous = null)
    {
        $object = static::parseResponseBody($response);

        $field = null;
        if (!empty($object->field)) {
            $field = $object->field;
        }

        return new static(
            'Error executing API call. Statuscode: ' . $response->getStatusCode(),
            $response->getStatusCode(),
            $field,
            $response,
            $previous
        );
    }

    /**
     * @return string|null
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @return Response|null
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return bool
     */
    public function hasResponse()
    {
        return $this->response !== null;
    }

    /**
     * @param $response
     *
     * @return mixed
     * @throws \JorisvanW\DeepL\Api\Exceptions\ApiException
     */
    protected static function parseResponseBody($response)
    {
        $body = (string)$response->getBody();

        $object = @json_decode($body);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new static("Unable to decode DeepL response: '{$body}'.");
        }

        return $object;
    }
}
