<?php

namespace Interfaces;

interface ApiResponseInterface
{
    /**
     * Get first row of response data.
     *
     * @return stdClass response first row
     */
    public function row();

    /**
     * Return first row as associative array
     *
     * @return array response first row
     */
    public function rowArray();

    /**
     * Return collection as array
     *
     * @return array
     */
    public function toArray();

    /**
     * Return collection as assoc
     *
     * @return mixed
     */
    public function toAssoc();

    /**
     * Check that collection empty or not
     *
     * @return bool true in case of empty
     */
    public function isEmpty();

    /**
     * Return number of items in collection
     *
     * @return int
     */
    public function count();

    /**
     * Return response http code.
     *
     * @return int
     */
    public function getHttpCode();

    /**
     * Return pagination
     *
     * @return array
     */
    public function getPagination();

    /**
     * Return request made this response
     *
     * @return ApiRequest
     */
    public function getRequest();
}