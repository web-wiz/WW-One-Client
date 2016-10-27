<?php

namespace Interfaces;

/**
 * Interface ApiRequestInterface
 */
interface ApiRequestInterface
{
    /**
     * Set api access username
     *
     * @param string $username api username
     * @return $this
     */
    public function setUsername($username);

    /**
     * Set api access password
     *
     * @param string $password api password
     * @return $this
     */
    public function setPassword($password);

    /**
     * Set uri part after domain and version
     *
     * @param string $uri uri
     * @return $this
     */
    public function setUri($uri);

    /**
     * Set request http method
     *
     * @param string $http_method HTTP method name
     * @return $this
     */
    public function setHttpMethod($http_method);

    /**
     * Add data to request
     *
     * @param array $data request data, assoc
     * @return $this
     */
    public function data(array $data);

    /**
     * Add headers to request
     *
     * @param array $headers request headers, assoc
     * @return $this
     */
    public function withHeaders(array $headers);

    /**
     * Add filters to request
     *
     * @param array $filters request filters, assoc
     * @return $this
     */
    public function withFilters(array $filters);

    /**
     * Set request page limit
     *
     * @param int $limit limit of records on one page
     * @return $this
     */
    public function limit($limit);

    /**
     * Set request page
     *
     * @param int $page request page
     * @return $this
     */
    public function page($page);

    /**
     * Perform request to api
     *
     * @return \Api\ApiResponse
     */
    public function make();

    /**
     * Check whether request is made or not
     *
     * @return bool
     */
    public function isMade();
}