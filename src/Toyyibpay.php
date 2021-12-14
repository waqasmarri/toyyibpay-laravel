<?php

namespace Waqasmarri\Toyyibpay;

class Toyyibpay
{
    private $dev_toyyibpay_uri = 'https://dev.toyyibpay.com';
    private $prod_toyyibpay_uri = 'https://toyyibpay.com';
    private $toyyibpay_uri;

    private $client;
    private $user_secret_key;
    private $enterprise_secret_key;
    private $redirect_uri;

    public function __construct($SANDBOX, $CLIENT_SECRET, $ENTERPRISE_SECRET, $REDIRECT_URI, $GUZZLE_CLIENT)
    {
        $this->client = $GUZZLE_CLIENT; # Guzzle Client
        $this->user_secret_key = $CLIENT_SECRET; # Toyyibpay User Secret Key
        $this->enterprise_secret_key = $ENTERPRISE_SECRET; # Toyyibpay User Secret Key
        $this->redirect_uri = $REDIRECT_URI; # Toyyibpay Redirect URI
        $this->toyyibpay_uri = ($SANDBOX) ? $this->dev_toyyibpay_uri : $this->prod_toyyibpay_uri;
    }

    #
    # Toyyibpay POST
    #
    public function post($url, $config)
    {
        $res = $this->client->post($url, $config);
        $result = json_decode($res->getBody()->getContents());
        return $result;
    }

    #
    # Toyyibpay GET
    #
    public function get($url, $config)
    {
        $res = $this->client->get($url, $config);
        $result = json_decode($res->getBody()->getContents());
        return $result;
    }

    #
    # Toyyibpay Get Banks
    #
    public function getBanks()
    {
        $url = $this->toyyibpay_uri . '/index.php/api/getBank';

        $res = $this->get($url, []);
        return $res;
    }

    #
    # Toyyibpay Get Banks FPX
    #
    public function getBanksFPX()
    {
        $url = $this->toyyibpay_uri . '/index.php/api/getBankFPX';

        $res = $this->get($url, []);
        return $res;
    }

    #
    # Toyyibpay Get Packages
    #
    public function getPackages()
    {
        $url = $this->toyyibpay_uri . '/index.php/api/getPackage';

        $res = $this->get($url, []);
        return $res;
    }

    #
    # Toyyibpay Create Category
    #
    public function createCategory($name, $description, $user_secret_key = null)
    {
        if (is_null($user_secret_key)) {
            $user_secret_key = $this->enterprise_secret_key;
        }

        $url = $this->toyyibpay_uri . '/index.php/api/createCategory';

        $data = [
            'form_params' => [
                'catname' => $name,
                'catdescription' => $description,
                'userSecretKey' => $user_secret_key
            ]
        ];

        $res = $this->post($url, $data);
        return $res;
    }

    #
    # Toyyibpay Get Category
    #
    public function getCategory($code, $user_secret_key = null)
    {
        if (is_null($user_secret_key)) {
            $user_secret_key = $this->enterprise_secret_key;
        }

        $url = $this->toyyibpay_uri . '/index.php/api/getCategoryDetails';

        $data = [
            'form_params' => [
                'categoryCode' => $code,
                'userSecretKey' => $user_secret_key,
            ]
        ];

        $res = $this->post($url, $data);
        return $res;
    }

    #
    # Toyyibpay Create Bill
    #
    public function createBill($code, $bill_object, $user_secret_key = null)
    {
        if (is_null($user_secret_key)) {
            $user_secret_key = $this->enterprise_secret_key;
        }

        $url = $this->toyyibpay_uri . '/index.php/api/createBill';

        $data = [
            'form_params' => [
                'categoryCode' => $code,
                'userSecretKey' => $user_secret_key,
                'billName' => $bill_object['billName'],
                'billDescription' => $bill_object['billDescription'],
                'billPriceSetting' => $bill_object['billPriceSetting'],
                'billPayorInfo' => $bill_object['billPayorInfo'],
                'billAmount' => $bill_object['billAmount'],
                'billReturnUrl' => $bill_object['billReturnUrl'] ?? $this->redirect_uri,
                'billCallbackUrl' => $bill_object['billCallbackUrl'] ?? $this->redirect_uri,
                'billExternalReferenceNo' => $bill_object['billExternalReferenceNo'],
                'billTo' => $bill_object['billTo'],
                'billEmail' => $bill_object['billEmail'],
                'billPhone' => $bill_object['billPhone'],
                'billSplitPayment' => $bill_object['billSplitPayment'] ?? 0,
                'billSplitPaymentArgs' => $bill_object['billSplitPaymentArgs'] ?? '',
                'billPaymentChannel' => $bill_object['billPaymentChannel'] ?? '0',
                'billContentEmail' => $bill_object['billContentEmail'] ?? '',
                'billChargeToCustomer' => $bill_object['billChargeToCustomer'] ?? 1,
            ]
        ];

        $res = $this->post($url, $data);
        return $res;
    }

    #
    # Toyyibpay Run Bill
    #
    public function runBill($code, $bill_object, $user_secret_key = null)
    {
        if (is_null($user_secret_key)) {
            $user_secret_key = $this->enterprise_secret_key;
        }

        $url = $this->toyyibpay_uri . '/index.php/api/runBill';

        $data = [
            'form_params' => [
                'userSecretKey' => $user_secret_key,
                'billBankID' => $bill_object->billBankID,
                'billCode' => $bill_object->billCode,
                'billpaymentAmount' => $bill_object->billpaymentAmount ?? '',
                'billpaymentPayorName' => $bill_object->billpaymentPayorName ?? '',
                'billpaymentPayorPhone' => $bill_object->billpaymentPayorPhone ?? '',
                'billpaymentPayorEmail' => $bill_object->billpaymentPayorEmail ?? '',
            ]
        ];

        $res = $this->post($url, $data);
        return $res;
    }

    #
    # Toyyibpay Get Payment Link
    #
    public function billPaymentLink($bill_code)
    {
        return $this->toyyibpay_uri . '/' . $bill_code;
    }

    #
    # Toyyibpay Create User
    #
    public function createUser($user_object)
    {
        $url = $this->toyyibpay_uri . '/index.php/api/createAccount';

        $data = [
            'form_params' => [
                'enterpriseUserSecretKey' =>  $this->enterprise_secret_key,
                'fullname' => $user_object['fullname'],
                'username' => $user_object['username'],
                'email' => $user_object['email'],
                'password' => $user_object['password'],
                'phone' => $user_object['phone'],
                'bank' => $user_object['bank'],
                'accountNo' => $user_object['accountNo'],
                'accountHolderName' => $user_object['accountHolderName'],
                'registrationNo' => $user_object['registrationNo'],
                'package' => $user_object['package'] ?? 2
            ]
        ];

        $res = $this->post($url, $data);
        return $res;
    }

    #
    # Toyyibpay Get User Status
    #
    public function getUserStatus($user_object)
    {
        $url = $this->toyyibpay_uri . '/index.php/api/getUserStatus';

        $data = [
            'form_params' => [
                'enterpriseUserSecretKey' =>  $this->enterprise_secret_key,
                'username' => $user_object['username']
            ]
        ];

        $res = $this->post($url, $data);
        return $res;
    }

    #
    # Toyyibpay Get Settlement Summary
    #
    public function getSettlementSummary($user_object)
    {
        $url = $this->toyyibpay_uri . '/index.php/api/getUserStatus';

        $data = [
            'form_params' => [
                'userSecretKey' =>  $user_object['userSecretKey'],
                'userName' => $user_object['username']
            ]
        ];

        $res = $this->post($url, $data);
        return $res;
    }
}
