<?php

class DB_Functions {

    // constructor
    function __construct() {
        
    }

    // destructor
    function __destruct() {
        
    }

    public function connectdb() {
    	require_once 'DB_Connect.php';
        // connecting to database
        $db = new DB_Connect();
        $con = $db->connect();
        return $con;
    }

    public function addUser($fname, $lname, $email, $uname, $password) {
    	$con = $this->connectdb();

    	$uuid = uniqid('', true);
        $hash = $this->hashSSHA($password);
        $encrypted_password = $hash["encrypted"]; // encrypted password
        $salt = $hash["salt"]; // salt
        $result = mysqli_query($con, "INSERT INTO users(id, firstname, lastname, email, username, encrypted_password, salt, created_at) VALUES('$uuid', '$fname', '$lname', '$email', '$uname', '$encrypted_password', '$salt', NOW())");
    	// check for successful store
        if ($result) {
            // get user details 
            $uid = mysql_insert_id(); // last inserted id
            $result = mysqli_query($con, "SELECT * FROM users WHERE id = $uid");
            // return user details
            return mysql_fetch_array($result);
            return true;
        } else {
            return false;
        }
    }


    //Check if user exists or not
    public function userExist($email) {
        $result = mysql_query("SELECT email from users WHERE email = '$email'");
        $no_of_rows = mysql_num_rows($result);
        if ($no_of_rows > 0) {
            // user exists 
            return true;
        } else {
            // user doesn't exist
            return false;
        }
    }


    //returns salt and encrypted password.
    public function hashSSHA($password) {

        $salt = sha1(rand());
        $salt = substr($salt, 0, 10);
        $encrypted = base64_encode(sha1($password . $salt, true) . $salt);
        $hash = array("salt" => $salt, "encrypted" => $encrypted);
        return $hash;
    }

}

?>
