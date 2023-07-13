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
     * Fetch voucher from the database
     * @param $category
     * @return false|mixed
     */
    public function fetchVoucher($category) {

        $sql = "SELECT * FROM `vouchers` LEFT JOIN `voucher_category` ON `voucher_category`.`category` = `vouchers`.`category` WHERE `vouchers`.`status` = 'available' AND `voucher_category`.`category` = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $category);
        if ($stmt->execute()) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            return false;
        }

    }



}