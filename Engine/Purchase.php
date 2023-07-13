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

    public function __construct(){
        $this->db = Database::getInstance();
        $this->paymongo = new PayMongo();
        $this->voucher = new Vouchers();
    }


    /**
     * Make a purchase request
     * @param $pricing
     * @return void
     * @throws GuzzleException
     */
    public function makePurchase($pricing){

        switch ($pricing) {
            case 'starter': 
               $voucher = $this->voucher->fetchVoucher(1);
               $this->paymongo->makeCheckout($voucher['price'], 1, "Starter");

                break;
            case 'basic':
                $voucher = $this->voucher->fetchVoucher(2);
                $this->paymongo->makeCheckout($voucher['price'], 1, "Basic");
                break;
            default:
                echo 'Invalid request';
                break;
        }


    }


    private function processPurchase($voucher) {

    }

}