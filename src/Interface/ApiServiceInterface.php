<?php

namespace Interfaces;

/**
 * Interface ApiServiceInterface
 * @package Interfaces
 */
interface ApiServiceInterface
{
    /**
     * Add headers to request
     *
     * @param array $headers assoc, key is header name, value is header value
     * @return $this
     */
    public function withHeaders(array $headers);

    /**
     * Add filters to request
     *
     * @param array $filters assoc, key is filter field name, value is filter
     * @return $this
     */
    public function withFilters(array $filters);

    /**
     * Set request page
     *
     * @param int $page page number
     * @return $this
     */
    public function page($page);

    /**
     * Set request page limit
     *
     * @param int $limit page limit
     * @return $this
     */
    public function limit($limit);

    /**
     * Perform request
     *
     * @return \Api\ApiResponse
     */
    public function make();
}