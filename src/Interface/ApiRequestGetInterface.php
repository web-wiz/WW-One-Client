<?php

namespace Interfaces;

/**
 * Interface ApiRequestGetInterface
 * @package Interfaces
 */
interface ApiRequestGetInterface
{
    /**
     * Return api access username
     *
     * @return string
     */
    public function getUsername();

    /**
     * Return api access password
     *
     * @return string
     */
    public function getPassword();
    /**
     * Return requested url
     *
     * @return string
     */
    public function getUrl();

    /**
     * Return request data
     *
     * @return array
     */
    public function getData();

    /**
     * Return request headers
     *
     * @return array
     */
    public function getHeaders();

    /**
     * Return request http method
     *
     * @return string
     */
    public function getHttpMethod();

    /**
     * Return requested page
     *
     * @return int
     */
    public function getPage();

    /**
     * Return request page limit
     *
     * @return int
     */
    public function getLimit();

    /**
     * Return response string
     *
     * @return string response
     */
    public function getResponseString();

    /**
     * @return int response http code
     */
    public function getResponseHttpCode();

    /**
     * @return ApiResponse response
     */
    public function getResponse();
}