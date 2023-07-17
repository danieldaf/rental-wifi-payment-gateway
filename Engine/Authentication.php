<?php


class Authentication
{

    /**
     * @var Database
     */
    private $db;
    private CSRF $csrf;

    public function __construct(){
        $this->db = Database::getInstance();
        $this->csrf = new CSRF();
    }

    public  function isLoggedIn(){

        $loginString = Generate::_generateLoginString();
        $currentString = Session::getSession("login_fingerprint");
        if(Session::getSession('user_id') == null ){
            return false;
        }
        if (Session::getSession("isLoggedIn")){
            return true;
        }
        if($currentString != null && $currentString == $loginString){
            return true;
        } else  {
            Session::destroySession();
            return false;
        }

    }

    public function isGuest(): bool
    {
        if (Session::getSession('isGuest')) {
            return true;
        } else {
            return false;
        }
    }

    public function isAdmin()
    {
      if ($this->isLoggedIn()){
          if (Session::getSession('isAdmin')){
              return true;
          }
      }
    }


    public function loginAsGuest() {

        /** Log in the user */
        Session::setSession("user_id", -1);
        Session::setSession("isLoggedIn", true);
        Session::setSession("isGuest", true);
        Session::setSession("login_fingerprint", Generate::_generateLoginString());

        echo "true";

    }

    /**
     * User login function
     * @param string $username User's username
     * @param string $password User's password
     * @return void TRUE if okay, FALSE otherwise
     **/
    public function userLogin(string $username, string $password, string $csrf)
    {

        $username = trim($username);
        $password = trim($password);



        try {

            $sql = "SELECT * FROM `users` WHERE `username` = :username";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":username", $username, PDO::PARAM_STR);

            if ($stmt->execute()){
                if($stmt->rowCount() > 0) {

                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    /** @var string $hashed_password **/
                    $hashed_password = $row['password'];


                    if (!$this->csrf->validateToken($csrf)) {
                        echo "Login not valid";
                        return;
                    }

                    if(password_verify($password, $hashed_password)){

                        if ($row['role'] === "admin"){
                            Session::setSession('isAdmin', true);
                        }

                        /** Log in the user */
                        Session::setSession("user_id", $row['user_id']);
                        Session::setSession("isLoggedIn", true);
                        Session::setSession("login_fingerprint", Generate::_generateLoginString());
                        Session::setSession("isGuest", false);


                        echo "true";

                    } else {
                        echo "Password is incorrect";
                    }

                } else {
                    echo "No username found";
                }
            } else {
                echo "Error occurred while logging in.";
            }

        } catch (Exception $e){
            echo "Error: " . $e;
        }

    }


    /**
     * Register user
     * @param string $username User's username
     * @param string $email Email of the user
     * @param string $password Password of the user
     * @param string $confirm_password Retyped password of the user
     * @return bool|void
     */
    public function userRegister(string $username, string $email, string $password, string $confirm_password){

        $username = trim($username);
        $email = trim($email);
        $password = trim($password);
        $confirm_password = trim($confirm_password);


        if($this->checkUsername($username)){
            echo "Username is already exists";
            return;
        }

        if($this->checkEmail($email)){
            echo "Email is already exists";
            return;
        }

        if ($password !== $confirm_password){
            echo "Password doesnt match";
            return;
        }


        try {

            $hashed_password = password_hash($confirm_password, PASSWORD_DEFAULT);


            $sql = "INSERT INTO `users` (`username`, `email`, `password`) VALUES (:username, :email, :password)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":username", $username);
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":password", $hashed_password);
            if($stmt->execute()){

                $uid = $this->db->lastInsertId();
                if ($this->setUserData($uid)){
                    return true;

                }

            }

        } catch(Exception $e){
            echo "Error: ". $e;
        }


    }


    /**
     * Insert other user info to database
     * @param $uid User id
     * @return bool|void
     */
    private function setUserData($uid){

        $date_time = date("Y-m-d h:i:s");

        try {

            $sql = "INSERT INTO `user_details` (`user_id`) VALUES (:uid)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":uid", $uid, PDO::PARAM_INT);
            if($stmt->execute()){

                    return true;

            }

        } catch (Exception $e){
            echo "Error: ". $e;
        }
    }



    /**
     * Check username if exists
     * @param $username string username of the user
     * @return boolean TRUE if it exists, FALSE otherwise
     */
    public function checkUsername($username){

        $sql = "SELECT user_id FROM users WHERE username = :u";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':u', $username, PDO::PARAM_STR);
        if($stmt->execute()){
            if($stmt->rowCount() == 1){
                return true;
            }
        }
    }

    /**
     * Check email if exists
     * @param $email string email of the user
     * @return boolean TRUE if it exists, FALSE otherwise
     */
    public function checkEmail(string $email){

        $sql = "SELECT `user_id` FROM `users` WHERE `email` = :e";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':e', $email, PDO::PARAM_STR);
        if($stmt->execute()){
            if($stmt->rowCount() == 1){
                return true;
            }
        }
    }


}