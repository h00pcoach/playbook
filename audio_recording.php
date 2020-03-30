<?php

require_once('mydb_pdo.php');

/*
*
* This class handles the management of a plays audio recordings 
*
*/

class Audio_recording {

    private $conn;
    private $user_id;

    public function __construct($user_id) {
        $this->user_id = $user_id;
        $this->conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
        $this->conn->exec("set names utf8");
    }

    // adds a new recording for a user.
    public function add() {

    }

    // Loads all recordings in a play for a user.
    public function load($play_id) {

    }

    // If the user is the creator of the recording, delete it.
    public function delete($recording_id) {

    }

    // Returns true if the user is a premium user. 
    // Otherwise, returns false.
    private function is_premium_user() {
        $stmt = $this->conn->prepare("SELECT paid FROM users WHERE id = :id");
        $stmt->bindValue(":id", $this->user_id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row === 1;
    }

    // returns true id the user is the creator of the recording. 
    // Otherwise, returns false.
    private function is_creator() {

    }

}

?>