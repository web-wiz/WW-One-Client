<?php

namespace Api;

require_once 'application/libraries/Api/Driver/Curl.php';
require_once 'application/libraries/Api/ApiResponse.php';
require_once 'application/libraries/Exception/ApiRequestException.php';
require_once 'application/libraries/Exception/ApiResponseException.php';
require_once 'application/libraries/Exception/ValidationException.php';
require_once 'application/libraries/Interface/ApiRequestGetInterface.php';
require_once 'application/libraries/Interface/ApiRequestInterface.php';

use Api\Driver\Curl;
use ApiRequestException;
use ApiResponseException;
use BadMethodCallException;
use Interfaces\ApiRequestGetInterface;
use Interfaces\ApiRequestInterface;
use InvalidArgumentException;
use LogicException;
use RuntimeException;
use stdClass;
use ValidationException;

/**
 * Class ApiRequest
 * @package Api
 */
class ApiRequest implements ApiRequestInterface, ApiRequestGetInterface
{
    /**
     * Get HTTP method
     */
    const HTTP_GET = 'GET';
    /**
     * POST HTTP method
     */
    const HTTP_POST = 'POST';
    /**
     * PUT HTTP method
     */
    const HTTP_PUT = 'PUT';
    /**
     * PATCH HTTP method
     */
    const HTTP_PATCH = 'PATCH';
    /**
     * DELETE HTTP method
     */
    const HTTP_DELETE = 'DELETE';

    /**
     * @var array permitted http methods
     */
    private static $http_methods = [
        self::HTTP_GET,
        self::HTTP_POST,
        self::HTTP_PUT,
        self::HTTP_PATCH,
        self::HTTP_DELETE
    ];

    /**
     * @var array request data
     */
    private $data = [];
    /**
     * @var string api domain
     */
    private $domain;
    /**
     * @var array request filters
     */
    private $filters = [];
    /**
     * @var array request headers
     */
    private $headers = [];
    /**
     * @var string request http method
     */
    private $http_method;
    /**
     * @var bool whether request made or not
     */
    private $made;
    /**
     * @var int request page limit
     */
    private $limit;
    /**
     * @var array additional get params
     */
    private $params = [];
    /**
     * @var int request page
     */
    private $page;
    /**
     * @var string api access password
     */
    private $password;
    /**
     * @var ApiResponse
     */
    private $response;
    /**
     * @var int response http code
     */
    private $response_http_code;
    /**
     * @var string
     */
    private $response_string;
    /**
     * @var string request url
     */
    private $url;
    /**
     * @var string request uri part after domain and version
     */
    private $uri;
    /**
     * @var string api access username
     */
    private $username;
    /**
     * @var string api version
     */
    private $version;

    /**
     * ApiRequest constructor.
     *
     * @param string $domain api domain
     * @param string $version api version
     */
    public function __construct($domain, $version)
    {
        $this->made = false;

        $this->domain = $domain;
        $this->version = $version;
    }

    /**
     * Set api access username
     *
     * @param string $username api username
     * @return ApiRequest
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Set api access password
     *
     * @param string $password api password
     * @return ApiRequest
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Set request http method
     *
     * @param string $http_method HTTP method name
     * @return ApiRequest
     */
    public function setHttpMethod($http_method)
    {
        if (! is_string($http_method) || ! in_array($http_method, self::$http_methods)) {
            throw new InvalidArgumentException("Http method $this->http_method will not perform. Please use one of " .
                implode(', ', self::$http_methods)
            );
        }
        // todo: implement PATCH and DELETE and remove this block
        if ($http_method == self::HTTP_PATCH || $http_method == self::HTTP_DELETE) {
            throw new BadMethodCallException(
                "Using of HTTP method $http_method is not implemented in ApiRequest class. Please, implement it."
            );
        }

        $this->http_method = $http_method;

        return $this;
    }

    /**
     * Set uri part after domain and version
     *
     * @param string $uri uri
     * @return ApiRequest
     */
    public function setUri($uri)
    {
        $this->uri = $uri;

        return $this;
    }

    /**
     * Add data to request
     *
     * @param array $data request data, assoc
     * @return ApiRequest
     */
    public function data(array $data)
    {
        $this->data = array_merge($this->data, $data);

        return $this;
    }

    /**
     * Add headers to request
     *
     * @param array $headers request headers, assoc
     * @return ApiRequest
     */
    public function withHeaders(array $headers)
    {
        $this->headers = array_merge($this->headers, $headers);

        return $this;
    }

    /**
     * Add filters to request
     *
     * @param array $filters request filters, assoc
     * @return ApiRequest
     */
    public function withFilters(array $filters)
    {
        $this->filters = array_merge($this->filters, $filters);

        return $this;
    }

    /**
     * Add query param to request
     *
     * @param string $key param name
     * @param mixed $value param value
     * @return ApiRequest
     */
    public function param($key, $value)
    {
        $this->params[$key] = $value;

        return $this;
    }

    /**
     * Set request page limit
     *
     * @param int $limit limit of records on one page
     * @return ApiRequest
     */
    public function limit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * Set request page
     *
     * @param int $page request page
     * @return ApiRequest
     */
    public function page($page)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * Make request to api
     *
     * @return ApiResponse api response
     * @throws ApiRequestException in case of errors at runtime
     * @throws ApiResponseException in case of bad response
     * @throws ValidationException in case of api validation errors
     */
    public function make()
    {
        $this->made = true;

        $this->checkIsValid();

        $this->buildUrl();

        try {
            $driver = new Curl();
            $this->response_string = $driver->make($this);
            $this->response_http_code = $driver->getResponseCode();
        } catch (RuntimeException $e) {
            throw new ApiRequestException($e->getMessage());
        }

        try {
            $this->response = new ApiResponse($this);
        } catch (ApiResponseException $e) {
            throw $e;
        } catch (ValidationException $e) {
            throw $e;
        }

        return $this->response;
    }

    /**
     * Whether request is made or not
     *
     * @return bool
     */
    public function isMade()
    {
        return $this->made;
    }

    /**
     * Check that request built right
     *
     * @throws LogicException in case of request built wrong
     */
    private function checkIsValid()
    {
        if (! isset($this->domain) || ! isset($this->version) || ! isset($this->uri)) {
            throw new LogicException("Please set domain, version and uri parts for request.");
        }
        if (! isset($this->username) || ! isset($this->password)) {
            throw new LogicException("Please set username and password for request.");
        }
        if (! isset($this->http_method)) {
            throw new LogicException("Please set HTTP method for request.");
        }
        if (in_array($this->http_method, [self::HTTP_POST, self::HTTP_PUT, self::HTTP_PATCH]) && empty($this->data)) {
            throw new LogicException("Please set post data for request.");
        }
    }

    /**
     * Build request url from parts
     */
    private function buildUrl()
    {
        $this->url = rtrim($this->domain, '/') . '/' . rtrim($this->version, '/') . '/' . ltrim($this->uri, '/');

        $query = '';
        if (! empty($this->filters)) {
            $query .= $this->filtersToString();
        }
        if (! empty($this->params)) {
            foreach ($this->params as $k => $v) {
                $query .= "&$k=$v";
            }
        }
        if (isset($this->page)) {
            $query .= "&page=$this->page";
        }
        if (isset($this->limit)) {
            $query .= "&limit=$this->limit";
        }

        if (! empty($query)) {
            $this->url .= '?' . ltrim($query, '&');
        }
    }

    /**
     * Build query string from filters
     *
     * @return string
     */
    private function filtersToString()
    {
        $filters = new stdClass();
        foreach ($this->filters as $field_name => $filter) {
            // filter with empty value will be missed
            if (isset($filter[1]) && ! empty($filter[1])) {
                // for array filters params
                if (is_array($filter[1])) {
                    $i = 1;
                    foreach ($filter[1] as $array_param) {
                        $filter[$i] = $array_param;
                        $i++;
                    }
                }
                $filters->$field_name = $filter;
            }
        }

        return empty($filters) ? '' : 'filters=' . json_encode($filters);
    }

    /**
     * Return api access username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Return api access password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }
    /**
     * Return requested url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Return request data
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Return request headers
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Return request http method
     *
     * @return string
     */
    public function getHttpMethod()
    {
        return $this->http_method;
    }

    /**
     * Return requested page
     *
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Return request page limit
     *
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @return string response string
     */
    public function getResponseString()
    {
        return $this->response_string;
    }

    /**
     * @return int response http code
     */
    public function getResponseHttpCode()
    {
        return $this->response_http_code;
    }

    /**
     * @return ApiResponse response
     */
    public function getResponse()
    {
        return $this->response;
    }
}