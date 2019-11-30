<?php

class db {
    const DB_USER = "test";
    const DB_PASSWORD = "test";
    const DB_DB = "test";
    const DB_HOST = "localhost";

    protected $conn;

    public function __construct() {
        $this->conn = new mysqli(self::DB_HOST,self::DB_USER, self::DB_PASSWORD, self::DB_DB);
    }
    public function query($sql) {
        $result = $this->conn->query($sql);

        if ($result) {
            return $result->fetch_array();
        } else {
            return null;
        }
    }
    public function insert($sql) {
        if ($this->conn->query($sql)) {
            return $this->conn->insert_id;
        } else {
            return null;
        }
    }
    public function __destruct()
    {
        $this->conn->close();
    }
}

function validateUser($username, $password) {
    $db = new db();
    $sql = "SELECT * FROM test WHERE username = '$username'";

    $userArray = $db->query($sql);

    if(is_null($userArray)) {
        return null;
    } else {
        if ($userArray[2] == $password) {
            return $userArray;
        } else {
            return null;
        }
    }

}

function addUser($username, $password, $displayName, $email) {
    $db = new db();
    $sql = "SELECT * FROM test WHERE userName = '$username'";

    if (!$db->query($sql)) {
        $sql = "INSERT INTO test (userName, userPassword, displayName, emailAddress) VALUES ('$username','$password','$displayName','$email')";
        $newUserId = $db->insert($sql);
        return $newUserId;
    } else {
        return 0;
    }
}