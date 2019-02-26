<?php

/**
 * Description of Database
 * 
 * Provides methods for opening, closing, and retrieving MySQL database connections.
 * 
 * @author misty
 */

class Database {
    
    // encapsulate Document properties by declaring private
    
    private $host;
    private $user;
    private $password;
    private $database;
    public $con;
    
    // constructor
    
    public function __construct($dbHost, $dbUser, $dbPassword, $dbDatabase) {
        $this->host = $dbHost;
        $this->user = $dbUser;
        $this->password = $dbPassword;
        $this->database = $dbDatabase;
    }
    
    // function to connect to database
    
    public function getDBConnection() {
        $this->con = new mysqli($this->host, $this->user, $this->password, $this->database)
                or die ('Could not connect to the database server' . mysqli_connect_error());
        return $this->con;
    }
    
    // function to close MySQL database connection
    
    public function closeDBConnection() {
        $this->con->close();
    }
}
