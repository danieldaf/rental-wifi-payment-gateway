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
    public function makePurchase($pricing){

        switch ($pricing) {
            case 'starter': 
               $voucher = $this->voucher->fetchVoucher(1);
               $this->paymongo->makeCheckout($voucher['price'], 1, "Starter", $voucher['voucher_id']);

                break;
            case 'basic':
                $voucher = $this->voucher->fetchVoucher(2);
                $this->paymongo->makeCheckout($voucher['price'], 1, "Basic", $voucher['voucher_id']);
                break;
            case '5dayplan':
                $voucher = $this->voucher->fetchVoucher(3);
                 $this->paymongo->makeCheckout($voucher['price'], 5, "5 Day Plan", $voucher['voucher_id']);

                break;
            case 'pro':
                $voucher = $this->voucher->fetchVoucher(4);
                $this->paymongo->makeCheckout($voucher['price'], 1, "Pro", $voucher['voucher_id']);
                break;
            default:
                echo 'Invalid request';
                break;
        }
    }

}