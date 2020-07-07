<?php

namespace Dpsoft\Pay;

class Pay
{
    private $restEndpoint = 'https://pay.ir/pg';
    private $apiKey;
    private $token;
    private $transport;

    /**
     * Pay constructor.
     *
     * @param $apiKey string Pay.ir api key. For test use 'test'
     */
    public function __construct($apiKey = 'test')
    {
        $this->apiKey = $apiKey;
    }

    /**
     * Create new request, and save the token.
     *
     * @param $callbackUrl string Callback url from payment page
     * @param $amount int Amount in Rial
     * @return array The array contains 2 keys: token & invoice_id , save theme with amount for further use.
     * @throws \Exception
     */
    public function request($callbackUrl, $amount)
    {
        $invoiceId = $this->getInvoiceId();
        $callbackUrl = $this->appendInvoiceIdToUrl($callbackUrl, $invoiceId);

        $response = $this->httpRequest(
            \Requests::POST,
            '/send',
            [
                'api' => $this->apiKey,
                'amount' => $amount,
                'redirect' => "$callbackUrl",
                'factorNumber' => $invoiceId
            ]
        );
        $result = json_decode($response->body, true);
        if ($response->success and $result['status']) {
            $this->token = $result['token'];
            return [
                'token' => $this->token,
                'invoice_id' => $invoiceId
            ];
        }
        throw new \Exception($result['errorMessage'], $result['errorCode']);
    }

    /**
     * Verify Transaction And Return The Result
     *
     * @param $amount
     * @param $token
     * @return array The array contain 3 key: card_number, invoice_id & rrn. The rrn is reference number in banking network.
     * @throws \Exception
     */
    public function verify($amount, $invoiceId)
    {
        if (
            empty($status = $_GET['status']) or
            empty($token = $_GET['token']) or
            empty($_GET['invoice_id']) or
            ($invoiceId != $_GET['invoice_id']) or
            ($status != 1)
        ) {
            throw new \Exception($status==0?'تراکنش توسط کاربر لغو شده است.':'Invalid Response From Gateway!', intval($status));
        }

        $response = $this->httpRequest(\Requests::POST, '/verify', ['api' => $this->apiKey, 'token' => $token]);
        $result = json_decode($response->body,true);
        if (
            $response->success and
            ($result['status'] == 1) and
            ($result['amount'] == $amount) and
            ($result['factorNumber']==$invoiceId)
        ) {
            return [
                'card_number' => $result['cardNumber'],
                'transaction_id' => $result['transId'],
                'token'=>$token
            ];
        }
        throw new \Exception($result['errorMessage']??'Unknown Error!',$result['errorCode']??-1);
    }

    public function redirectUrl()
    {
        return sprintf($this->restEndpoint . '/%s', $this->token);
    }

    public function redirectToBank()
    {
        header(sprintf("Location: %s", $this->redirectUrl()));
    }

    /**
     * Generate nearly unique integer ( hopefully! )
     *
     * @return int
     */
    private function getInvoiceId()
    {
        return hexdec(uniqid());
    }

    /**
     * Append invoiceId to callback url to use in verify transaction
     *
     * @param $url
     * @param $invoiceId
     * @return string
     */
    private function appendInvoiceIdToUrl($url, $invoiceId)
    {
        $parsedUrl = parse_url($url);
        if ($parsedUrl['path'] == null) {
            $url .= '/';
        }
        $separator = empty($parsedUrl['query']) ? '?' : '&';
        $query = "invoice_id=$invoiceId";
        $url .= $separator . $query;
        return $url;
    }

    /**
     * @param string $method
     * @param string $endpoint
     * @param array $data
     * @return \Requests_Response
     * @throws \Requests_Exception
     */
    public function httpRequest($method = \Requests::GET, $endpoint = '', $data = [])
    {
        $options = !empty($this->transport) ? ['transport' => $this->transport] : [];
        return \Requests::request(
            $this->restEndpoint . $endpoint,
            [
                'accept' => 'application/json',
                'Content-Type' => 'application/json'
            ],
            empty($data) ? null : json_encode($data),
            $method,
            $options
        );
    }

    public function setTransport($transport)
    {
        $this->transport = $transport;
    }

}
