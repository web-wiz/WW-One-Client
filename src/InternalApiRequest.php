<?php

require_once 'application/libraries/Logger/FileLogger.php';

/**
 * Class InternalApiRequest class for make requests to api
 */
class InternalApiRequest
{
    /**
     * @var FileLogger
     */
    protected $logger;

    public function __construct()
    {
        $this->CI =& get_instance();

        $this->logger = new FileLogger(
            'custom-logs/', FileLogger::DAILY, constant('FileLogger::' . $this->CI->config->item('log_level'))
        );
    }

    /**
     * Send request to api
     * @param $path
     * @param array $params
     * @param bool $http_code
     * @return mixed
     * @throws Exception
     */
    public function make($path, $params = array(), $http_code = false, $headers = [])
    {
        $url = $this->CI->config->item('gdm_api_url');
        $username = $this->CI->config->item('gdm_api_username');
        $password = $this->CI->config->item('gdm_api_password');

        $url = rtrim($url, '/');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . '/' . $path);
        $this->logger->debug("Api request: $url/$path");
        //Set basic auth
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if ($headers) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        if ($params) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

        $rs = curl_exec($ch);
        $this->logger->debug('Api response: ' . $rs);

        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $this->logger->debug('Api reponse code: ' . $code);

        if (curl_errno($ch)) {
            $this->logger->error(
                'Api iv1 error: ' . curl_error($ch),
                ['url' => $url . '/' . $path, 'response_code' => $code, 'response' => $rs]
            );
            throw new Exception(curl_error($ch));
        }

        $ret = json_decode($rs);

        if (!$ret) {
            $this->logger->error(
                'Api iv1 error: bad response',
                ['url' => $url . '/' . $path, 'response_code' => $code, 'response' => $rs]
            );
            throw new Exception($rs);
        }

        if($http_code) {
            $ret->http_code = $code;
        }

        return $ret;
    }

    /**
     * PUT request to internal API.
     *
     * @param string $path uri to api method
     * @param array $params request body
     * @param array $headers request headers (list, non assoc)
     * @param bool $http_code flag to return only response code
     * @return object api response
     * @throws Exception in case of curl or api error
     */
    public function put($path, $params, $headers = array(), $http_code = false)
    {
        $url = $this->CI->config->item('gdm_api_url');
        $username = $this->CI->config->item('gdm_api_username');
        $password = $this->CI->config->item('gdm_api_password');

        $url = rtrim($url, '/');

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url . '/' . $path);

        //Set basic auth
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

        $params = json_encode($params);
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Content-Length: ' . strlen($params);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);

        $rs = curl_exec($ch);

        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            throw new Exception(curl_error($ch));
        }

        $ret = json_decode($rs);

        if (!$ret) {
            throw new Exception($rs);
        }

        if($http_code) {
            $ret->http_code = $code;
        }

        return $ret;
    }
}
