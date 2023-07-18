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

        $fingerprint = Session::getSession('login_fingerprint');

        if (Session::getSession('isGuest')){
            $sql = "SELECT * FROM `purchased_voucher` INNER JOIN purchase_history ph on purchased_voucher.purchase_id = ph.purchased_id INNER JOIN vouchers v on purchased_voucher.voucher_id = v.voucher_id INNER JOIN voucher_category vc on v.category = vc.category WHERE `user_id` = :uid AND `fingerprint` = :fp";
        } else {
            $sql = "SELECT * FROM `purchased_voucher` INNER JOIN purchase_history ph on purchased_voucher.purchase_id = ph.purchased_id INNER JOIN vouchers v on purchased_voucher.voucher_id = v.voucher_id INNER JOIN voucher_category vc on v.category = vc.category WHERE `user_id` = :uid";
        }

        $stmt = $this->db->prepare($sql);
        if (Session::getSession('isGuest')){
            $stmt->bindParam(":fp", $fingerprint);
        }
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
    public function fetchVoucher($category)
    {
        $sql = "SELECT * FROM `vouchers`
            LEFT JOIN `voucher_category` ON `voucher_category`.`category` = `vouchers`.`category`
            WHERE `vouchers`.`status` = 'available' AND `voucher_category`.`category` = :id
            LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $category);

        if ($stmt->execute()) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            return false;
        }
    }


    /**
     * Check if checkout is paid
     * @param $purchased_id
     * @param $checkout_session_id
     * @return bool|void
     */
    public function isPaid($purchased_id, $checkout_session_id)
    {
        $sql = "SELECT purchase_status FROM `purchase_history` WHERE `checkout_session_id` = :csi AND purchased_id = :purchased_id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':csi', $checkout_session_id);
        $stmt->bindParam(':purchased_id', $purchased_id);
        if ($stmt->execute()) {
            if ($stmt->rowCount() > 0) {
                $res = $stmt->fetch();
                if ($res['purchase_status'] === 'paid') {
                    return true;
                } else {
                    return false;
                }
            }
        }
    }


    /**
     * Make spoiler to the text
     */
   public function makeSpoiler(): string
   {
        return "********";
    }

    public function deleteCode($voucher_id)
    {

        $sql = "
            DELETE FROM `purchase_history` 
            WHERE `purchased_id` IN (
                SELECT `purchase_id` 
                FROM `purchased_voucher` 
                WHERE `voucher_id` = :voucher_id 
            );
            DELETE FROM `purchased_voucher` 
            WHERE `voucher_id` = :voucher_id;
            DELETE FROM `vouchers` WHERE `voucher_id` = :voucher_id;
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":voucher_id", $voucher_id);
        if ($stmt->execute()){
            echo "true";
        }

    }


}