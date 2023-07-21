<?php

use GuzzleHttp\Exception\GuzzleException;

class Purchase
{

    /**
     * @var Database
     */
    private $db;
    /**
     * @var PayMongo
     */
    private $paymongo;
    /**
     * @var Vouchers
     */
    protected $voucher;
    /**
     * @var mixed|null
     */
    private $loggedInUser;

    public function __construct(){
        $this->db = Database::getInstance();
        $this->paymongo = new PayMongo();
        $this->voucher = new Vouchers();
        $this->loggedInUser = Session::getSession('user_id');

    }

    /**
     * Make a purchase request
     * @param $pricing
     * @return void
     * @throws GuzzleException
     */
    public function makePurchase($pricing)
    {
        $pricingDetails = [
            'starter' => ['voucher_id' => 1, 'duration' => 1, 'name' => 'Starter'],
            'basic' => ['voucher_id' => 2, 'duration' => 1, 'name' => 'Basic'],
            '5dayplan' => ['voucher_id' => 3, 'duration' => 5, 'name' => '5 Day Plan'],
            'pro' => ['voucher_id' => 4, 'duration' => 1, 'name' => 'Pro']
        ];
        if (array_key_exists($pricing, $pricingDetails)) {
            $details = $pricingDetails[$pricing];
            $voucher = $this->voucher->fetchVoucher($details['voucher_id']);
            $this->paymongo->makeCheckout($voucher['price'], $details['duration'], $details['name'], $voucher['voucher_id']);
        } else {
            echo 'Invalid request';
        }
    }

}