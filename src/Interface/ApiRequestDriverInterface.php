<?php

namespace Interfaces;

require_once 'application/libraries/Api/ApiRequest.php';

use Api\ApiRequest;

/**
 * Interface ApiRequestDriverInterface
 * @package Interfaces
 */
interface ApiRequestDriverInterface
{
    /**
     * @param ApiRequest $request
     * @return mixed response
     */
    public function make(ApiRequest $request);

    /**
     * Return response http code
     *
     * @return int
     */
    public function getResponseCode();
}