<?php

namespace Api;

require_once 'application/libraries/Exception/ApiException.php';
require_once 'application/libraries/Exception/ApiRequestException.php';
require_once 'application/libraries/Exception/ApiResponseException.php';
require_once 'application/libraries/Interface/ApiRequestInterface.php';
require_once 'application/libraries/Interface/ApiServiceInterface.php';
require_once 'application/libraries/Api/ApiRequestFactory.php';
require_once 'application/libraries/Logger/FileLogger.php';

use ApiException;
use ApiRequestException;
use ApiResponseException;
use CI_Controller;
use FileLogger;
use Interfaces\ApiRequestInterface;
use Interfaces\ApiServiceInterface;
use ValidationException;

/**
 * Class ApiService common api service class
 * @package Api
 */
class ApiService implements ApiServiceInterface
{
    /**
     * @var CI_Controller
     */
    protected $_CI;
    /**
     * @var FileLogger
     */
    private $logger;
    /**
     * @var ApiRequest api request instance
     */
    private $request;

    public function __construct()
    {
        $this->_CI =& get_instance();

        $this->logger = new FileLogger(
            'custom-logs/', FileLogger::DAILY, constant('FileLogger::' . $this->_CI->config->item('log_level'))
        );
    }

    /**
     * Set request instance.
     *
     * @param string $api_request_version api request version
     * @return ApiRequestInterface
     */
    protected function request($api_request_version)
    {
        $this->request = ApiRequestFactory::getInstance($api_request_version);

        return $this->request;
    }

    /**
     * Add headers to request
     *
     * @param array $headers assoc, key is header name, value is header value
     * @return ApiService
     */
    public function withHeaders(array $headers)
    {
        $this->request->withHeaders($headers);

        return $this;
    }

    /**
     * Add filters to request
     *
     * @param array $filters assoc, key is filter field name, value is filter
     * @return ApiService
     */
    public function withFilters(array $filters)
    {
        $this->request->withFilters($filters);

        return $this;
    }

    /**
     * Add query param to request
     *
     * @param string $key param name
     * @param mixed $value param value
     * @return ApiService
     */
    public function param($key, $value)
    {
        $this->request->param($key, $value);

        return $this;
    }

    /**
     * Set request page
     *
     * @param int $page page number
     * @return ApiService
     */
    public function page($page)
    {
        $this->request->page($page);

        return $this;
    }

    /**
     * Set request page limit
     *
     * @param int $limit page limit
     * @return ApiService
     */
    public function limit($limit)
    {
        $this->request->limit($limit);

        return $this;
    }

    /**
     * Perform request
     *
     * @return ApiResponse
     * @throws ApiException in case of problems with request or bad response from api
     * @throws ValidationException in case of api validation errors
     */
    public function make()
    {
        try {
            $response = $this->request->make();
        } catch (ApiRequestException $e) {
            $log_data = [
                'url' => $this->request->getUrl(),
                'http_method' => $this->request->getHttpMethod(),
                'headers' => $this->request->getHeaders(),
                'data' => self::exceptPassword($this->request->getData()),
            ];
            $this->logger->critical("Api request exception: " . $e->getMessage(), $log_data);

            throw new ApiException($e->getMessage());
        } catch (ApiResponseException $e){
            $log_data = [
                'url' => $this->request->getUrl(),
                'http_method' => $this->request->getHttpMethod(),
                'headers' => $this->request->getHeaders(),
                'data' => self::exceptPassword($this->request->getData()),
                'response_http_code' => $this->request->getResponseHttpCode(),
                'response_string' => $this->request->getResponseString()
            ];
            $this->logger->error($e->getMessage(), $log_data);

            throw new ApiException($e->getMessage());
        } catch (ValidationException $e) {
            throw $e;
        }

        return clone $response;
    }

    /**
     * Except password field from array. Use before log data.
     *
     * @param array $data
     * @return array data
     */
    private static function exceptPassword(array $data)
    {
        if (isset($data['password'])) {
            unset($data['password']);
        }

        return $data;
    }
}