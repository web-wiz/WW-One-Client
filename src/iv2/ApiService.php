<?php

namespace Api\iv2;

require_once 'application/libraries/Api/ApiServiceCommon.php';
require_once 'application/libraries/Api/ApiRequestFactory.php';

use Api\ApiRequest;
use Api\ApiRequestFactory;
use Api\ApiService as ApiServiceCommon;

/**
 * Class ApiService
 * @package Api\iv2
 */
class ApiService extends ApiServiceCommon
{
    /**
     * Gdm accounts /accounts GET
     *
     * @return ApiService
     */
    public function getAccounts()
    {
        $this->request()
            ->setHttpMethod(ApiRequest::HTTP_GET)
            ->setUri("accounts");

        return $this;
    }

    /**
     * Gdm account /account/:account GET
     *
     * @param int $id gdm id
     * @return ApiService
     */
    public function getAccountsId($id)
    {
        $this->request()
            ->setHttpMethod(ApiRequest::HTTP_GET)
            ->setUri("accounts/$id");

        return $this;
    }

    /**
     * Ibs /ibs GET
     *
     * @return ApiService
     */
    public function getIbs()
    {
        $this->request()
            ->setHttpMethod(ApiRequest::HTTP_GET)
            ->setUri("ibs");

        return $this;
    }

    /**
     * Check ib password /ibs/:ib/check-password PUT
     *
     * @param string $id ib id
     * @param array $data post data
     * @return ApiService
     */
    public function putIbsIdCheckPassword($id, array $data)
    {
        $this->request()
            ->setHttpMethod(ApiRequest::HTTP_PUT)
            ->setUri("ibs/$id/check-password")
            ->data($data);

        return $this;
    }

    /**
     * Ib deposits ibs/:ib/deposits GET
     *
     * @param string $id ib id
     * @return ApiService
     */
    public function getIbsIdDeposits($id){
        $this->request()
            ->setHttpMethod(ApiRequest::HTTP_GET)
            ->setUri("ibs/$id/deposits");

        return $this;
    }

    /**
     * Transfer from ib account /ibs/:ib/transfers POST
     *
     * @param string $id ib id
     * @param array $data post data
     * @return ApiService
     */
    public function postIbsIdTransfer($id, array $data)
    {
        $this->request()
            ->setHttpMethod(ApiRequest::HTTP_POST)
            ->setUri("ibs/$id/transfers")
            ->data($data);

        return $this;
    }

    /**
     * Ib withdrawals ibs/:ib/withdrawals GET
     *
     * @param string $id ib id
     * @return ApiService
     */
    public function getIbsIdWithdrawals($id){
        $this->request()
            ->setHttpMethod(ApiRequest::HTTP_GET)
            ->setUri("ibs/$id/withdrawals");

        return $this;
    }

    /**
     * Withdraw from ibs ibs/:ib/withdrawals POST
     *
     * @param int $id ib id
     * @param array $data post data
     * @return ApiService
     */
    public function postIbsIdWithdrawals($id, array $data){
        $this->request()
            ->setHttpMethod(ApiRequest::HTTP_POST)
            ->setUri("ibs/$id/withdrawals")
            ->data($data);

        return $this;
    }

    /**
     * I-withdraw from trader ibs/:ib/i-withdrawals POST
     *
     * @param int $id ib id
     * @param array $data post data
     * @return ApiService
     */
    public function postIbsIdIWithdrawals($id, array $data){
        $this->request()
            ->setHttpMethod(ApiRequest::HTTP_POST)
            ->setUri("ibs/$id/i-withdrawals")
            ->data($data);

        return $this;
    }

    /**
     * Traders /traders GET
     *
     * @return ApiService
     */
    public function getTraders()
    {
        $this->request()
            ->setHttpMethod(ApiRequest::HTTP_GET)
            ->setUri("traders");

        return $this;
    }

    /**
     * Tradologic traders /accounts/:account/traders/trado GET
     *
     * @param int $id gdm account id
     * @return ApiService
     */
    public function getAccountsIdTradersTrado($id)
    {
        $this->request()
            ->setHttpMethod(ApiRequest::HTTP_GET)
            ->setUri("accounts/$id/traders/trado");

        return $this;
    }

    /**
     * Create tradologic trader /accounts/:account/traders/trado POST
     *
     * @param int $id gdm account id
     * @param array $data post data
     * @return ApiService
     */
    public function postAccountsIdTradersTrado($id, array $data)
    {
        $this->request()
            ->setHttpMethod(ApiRequest::HTTP_POST)
            ->setUri("accounts/$id/traders/trado")
            ->data($data);

        return $this;
    }

    /**
     * Trader /traders/:trader GET
     *
     * @param int $id trader id
     * @return ApiService
     */
    public function getTradersId($id)
    {
        $this->request()
            ->setHttpMethod(ApiRequest::HTTP_GET)
            ->setUri("traders/$id");

        return $this;
    }

    /**
     * traders/:trader/auth POST
     *
     * @param int $id
     * @param array $data post data
     * @return ApiService
     */
    public function postTradersIdAuth($id, array $data)
    {
        $this->request()
            ->setHttpMethod(ApiRequest::HTTP_POST)
            ->setUri("traders/$id/auth")
            ->data($data);

        return $this;
    }

    /**
     * Check trader password traders/:trader/check-password PUT
     *
     * @param int $id trader id
     * @param array $data post data
     * @return ApiService
     */
    public function putTradersIdCheckPassword($id, array $data)
    {
        $this->request()
            ->setHttpMethod(ApiRequest::HTTP_PUT)
            ->setUri("traders/$id/check-password")
            ->data($data);

        return $this;
    }

    /**
     * Trader deposits traders/:trader/deposits GET
     *
     * @param int $id trader id
     * @return ApiService
     */
    public function getTradersIdDeposits($id){
        $this->request()
            ->setHttpMethod(ApiRequest::HTTP_GET)
            ->setUri("traders/$id/deposits");

        return $this;
    }

    /**
     * Trader deposits traders/:trader/deposits POST
     *
     * @param int $id trader id
     * @param array $data
     * @return ApiService
     */
    public function postTradersIdDeposits($id, $data)
    {
        $this->request()
            ->setHttpMethod(ApiRequest::HTTP_POST)
            ->setUri("traders/$id/deposits")
            ->data($data);

        return $this;
    }

    /**
     * Trader mt4 closed orders traders/:trader/orders/mt4/closed GET
     *
     * @param int $id trader id
     * @return ApiService
     */
    public function getTradersIdOrdersMt4Closed($id){
        $this->request()
            ->setHttpMethod(ApiRequest::HTTP_GET)
            ->setUri("traders/$id/orders/mt4/closed");

        return $this;
    }

    /**
     * Trader tradologic closed orders traders/:trader/orders/trado/closed GET
     *
     * @param int $id trader id
     * @return ApiService
     */
    public function getTradersIdOrdersTradoClosed($id){
        $this->request()
            ->setHttpMethod(ApiRequest::HTTP_GET)
            ->setUri("traders/$id/orders/trado/closed");

        return $this;
    }

    /**
     * Transfer from trader traders/:trader/transfers POST
     *
     * @param int $id trader id
     * @param array $data post data
     * @return ApiService
     */
    public function postTradersIdTransfer($id, array $data)
    {
        $this->request()
            ->setHttpMethod(ApiRequest::HTTP_POST)
            ->setUri("traders/$id/transfers")
            ->data($data);

        return $this;
    }

    /**
     * Trader withdrawals traders/:trader/withdrawals GET
     *
     * @param int $id trader id
     * @return ApiService
     */
    public function getTradersIdWithdrawals($id){
        $this->request()
            ->setHttpMethod(ApiRequest::HTTP_GET)
            ->setUri("traders/$id/withdrawals");

        return $this;
    }

    /**
     * Withdraw from trader traders/:trader/withdrawals POST
     *
     * @param int $id trader id
     * @param array $data post data
     * @return ApiService
     */
    public function postTradersIdWithdrawals($id, array $data){
        $this->request()
            ->setHttpMethod(ApiRequest::HTTP_POST)
            ->setUri("traders/$id/withdrawals")
            ->data($data);

        return $this;
    }

    /**
     * I-withdraw from trader traders/:trader/i-withdrawals POST
     *
     * @param int $id trader id
     * @param array $data post data
     * @return ApiService
     */
    public function postTradersIdIWithdrawals($id, array $data){
        $this->request()
            ->setHttpMethod(ApiRequest::HTTP_POST)
            ->setUri("traders/$id/i-withdrawals")
            ->data($data);

        return $this;
    }

    /**
     * Get request instance
     *
     * @return ApiRequest
     */
    protected function request()
    {
        return parent::request(ApiRequestFactory::TEST_IV2);
    }
}