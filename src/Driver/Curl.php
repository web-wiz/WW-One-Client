<?php

namespace Api\Driver;

require_once 'application/libraries/Api/ApiRequest.php';
require_once 'application/libraries/Interface/ApiRequestDriverInterface.php';

use Api\ApiRequest;
use Exception;
use Interfaces\ApiRequestDriverInterface;
use InvalidArgumentException;
use RuntimeException;

/**
 * Class Curl
 * @package Api\Driver
 */
class Curl implements ApiRequestDriverInterface
{
    /**
     * @var resource cURl session
     */
    private $session;
    /**
     * @var int http response code
     */
    private $response_code;

    /**
     * @param ApiRequest $request request
     * @return string response
     * @throws RuntimeException in case of request errors
     * @throws InvalidArgumentException
     */
    public function make(ApiRequest $request)
    {
        $this->startSession($request->getUrl());

        try {
            $this->setAuth($request->getUsername(), $request->getPassword());

            $this->prepareRequestData($request->getHttpMethod(), $request->getHeaders(), $request->getData());

            $response = curl_exec($this->session);
            if ($response === false) {
                throw new RuntimeException(curl_error($this->session));
            }
            $this->setResponseCode();
        } catch (Exception $e) {
            throw $e;
        } finally {
            $this->closeSession();
        }

        return $response;
    }

    /**
     * Return http response code
     *
     * @return int response code
     */
    public function getResponseCode()
    {
        return $this->response_code;
    }

    /**
     * Starts cURL session.
     *
     * @throws RuntimeException in case of problems with starting session
     */
    private function startSession($url)
    {
        $this->session = curl_init($url);
        if ($this->session === false) {
            throw new RuntimeException("Cannot start cURL session.");
        }
    }

    /**
     * Close cURL session
     */
    private function closeSession()
    {
        curl_close($this->session);
    }

    /**
     * Set basic auth to request
     *
     * @param string $username api username
     * @param string $password api password
     */
    private function setAuth($username, $password)
    {
        curl_setopt($this->session, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($this->session, CURLOPT_USERPWD, "$username:$password");
        curl_setopt($this->session, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->session, CURLOPT_SSL_VERIFYHOST, false);
    }

    /**
     * Prepare request data
     *
     * @param string $http_method HTTP method
     * @param array $headers request headers
     * @param array $data post data
     */
    private function prepareRequestData($http_method, $headers = [], $data = [])
    {
        curl_setopt($this->session, CURLOPT_RETURNTRANSFER, true);

        switch ($http_method) {
            case ApiRequest::HTTP_POST:
                if (empty($data)) {
                    throw new InvalidArgumentException('Data for POST http request cannot be empty.');
                }
                curl_setopt($this->session, CURLOPT_POST, true);
                curl_setopt($this->session, CURLOPT_POSTFIELDS, $data);
                break;
            case ApiRequest::HTTP_PUT:
                if (empty($data)) {
                    throw new InvalidArgumentException('Data for PUT http request cannot be empty.');
                }
                $data = json_encode($data);
                $headers[] = 'Content-Type: application/json';
                $headers[] = 'Content-Length: ' . strlen($data);

                curl_setopt($this->session, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($this->session, CURLOPT_POSTFIELDS, $data);
                break;
        }

        if (! empty($headers)) {
            curl_setopt($this->session, CURLOPT_HTTPHEADER, $headers);
        }
    }

    private function setResponseCode()
    {
        $this->response_code = curl_getinfo($this->session, CURLINFO_HTTP_CODE);
    }
}