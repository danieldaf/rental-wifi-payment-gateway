<?php

class Vouchers
{

    /**
     * @var mixed|null
     */
    private $loggedInUser;
    /**
     * @var Database
     */
    private $db;

    public function __construct(){
        $this->db = Database::getInstance();
        $this->loggedInUser = Session::getSession('user_id');
    }

    /**
     * Fetch all the purchased voucher of the logged-in user
     * @return array|false
     */
    public function purchasedVoucher(){
        $sql = "SELECT * FROM `purchased_voucher` WHERE `user_id` = :uid";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":uid", $this->loggedInUser);
        if ($stmt->execute()){
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            return false;
        }
    }

    /**
     * Make a purchase request
     * @param $pricing
     * @return void
     */
    public function makePurchase($pricing){

        switch ($pricing) {
            case 'starter':
               $pricing = $this->fetchVoucherCategory(1);

                break;
            case 'basic':
                $pricing = $this->fetchVoucherCategory(2);

                break;
            default:
                echo 'Invalid request';
                break;
        }


    }


    private function processPurchase() {

    }

    private function fetchVoucherCategory($category) {

        $sql = "SELECT * FROM `voucher_category` WHERE `category_id` = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $category);
        if ($stmt->execute()) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            return false;
        }

    }



}