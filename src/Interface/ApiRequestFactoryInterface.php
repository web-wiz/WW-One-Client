<?php

namespace Interfaces;

/**
 * Interface ApiRequestFactoryInterface
 * @package Interfaces
 */
interface ApiRequestFactoryInterface
{
    /**
     * Return api request instance.
     *
     * @param string $version
     * @return mixed
     */
    public static function getInstance($version);
}