<?php

/**
 * Admin Class
 */
class Admin
{


    /**
     * @var Database
     */
    private Database $db;
    private Authentication $auth;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->auth = new Authentication();
    }

    /**
     * Fetch all the vouchers
     * @return array|false|void
     */
    public function fetchAllVouchers()
    {

        if (!$this->auth->isAdmin()){
            return false;
        }

        $sql = "SELECT * FROM vouchers INNER JOIN voucher_category ON vouchers.category = voucher_category.category";
        $stmt = $this->db->query($sql);
        if ($stmt->execute()){
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

    }

    /**
     * @return array|false|void
     */
    public function fetchAllCategory()
    {
        $sql = "SELECT * FROM voucher_category";
        $stmt = $this->db->query($sql);
        if ($stmt->execute()){
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

    }

}