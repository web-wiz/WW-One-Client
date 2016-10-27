<?php

namespace Api;

require_once 'application/libraries/Api/ApiRequest.php';
require_once 'application/libraries/Interface/ApiRequestFactoryInterface.php';
require_once 'application/libraries/Interface/ApiRequestInterface.php';

use BadMethodCallException;
use Interfaces\ApiRequestFactoryInterface;
use Interfaces\ApiRequestInterface;

/**
 * Class ApiRequestFactory
 * @package Api
 */
class ApiRequestFactory implements ApiRequestFactoryInterface
{
    /**
     * internal api v2
     */
    const IV2 = 'iv2';
    /**
     * trado internal api v2
     */
    const TEST_IV2 = 'test_iv2';

    /**
     * @var array supported api versions
     */
    protected static $api_versions = [
        self::IV2,
        self::TEST_IV2,
    ];

    /**
     * ApiRequest constructor. Private, use getInstance.
     */
    private function __construct(){}

    /**
     * Factory, return ApiRequest instance.
     *
     * @param string $version api version
     * @return ApiRequestInterface
     */
    public static function getInstance($version)
    {
        switch ($version) {
            case self::IV2:
                $domain = Config::get('gdm_api_url');
                $username = Config::get('gdm_api_username');
                $password = Config::get('gdm_api_password');

                $request_instance = new ApiRequest($domain, self::IV2);
                $request_instance->setUsername($username)
                    ->setPassword($password)
                    ->withHeaders(['X-Broker: GDM']);
                break;
            case self::TEST_IV2:
                $domain = Config::get('gdm_api_url_v2');
                $username = Config::get('gdm_api_username');
                $password = Config::get('gdm_api_password');

                $request_instance = new ApiRequest($domain, self::IV2);
                $request_instance->setUsername($username)
                    ->setPassword($password)
                    ->withHeaders(['X-Broker: GDM']);
                break;
            default:
                throw new BadMethodCallException("Version $version does not exists. Use one of " .
                    implode(', ', self::$api_versions)
                );
        }

        return $request_instance;
    }
}