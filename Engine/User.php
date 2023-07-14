<?php

class User
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

    public function getUserData()
    {

        $sql = "SELECT * FROM users INNER JOIN user_details ud on users.user_id = ud.user_id WHERE users.user_id = :uid LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':uid', $this->loggedInUser);
        if($stmt->execute()){
            if($stmt->rowCount() > 0){
                return $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                return false;
            }
        }

    }

}