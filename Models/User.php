<?php

/**
 * Description of User
 *
 * Provides CRUD methods for user.
 * 
 * @author misty
 */

class User {
    
    // encapsulate Message properties by declaring private

    private $user = [];
    private $db; // database
    
    // constructor to initialize Message properties
    
    public function __construct($userName, $firstName, $lastName, $email, $database) {

        $this->user['userName'] = $userName;
        $this->user['firstName'] = $firstName;
        $this->user['lastName'] = $lastName;
        $this->user['email'] = $email;
        $this->db = $database;
    }
    
    // function to get user name
    
    public function getUserName() {
        return $this->user['userName'];
    }
    
    // function to set user name
    
    public function setUserName($userName) {
        $this->user['userName'] = $userName;
    }
    
    // function to get first name
    
    public function getFirstName() {
        return $this->user['firstName'];
    }
    
    // function to set first name
    
    public function setFirstName($firstName) {
        $this->user['firstName'] = $firstName;
    }
    
    // function to get last name
    
    public function getLastName() {
        return $this->user['lastName'];
    }
    
    // function to set last name
    
    public function setLastName($lastName) {
        $this->user['lastName'] = $lastName;
    }
    
    // function to get email
    
    public function getEmail() {
        return $this->user['email'];
    }
    
    // function to set email
    
    public function setEmail($email) {
        $this->user['email'] = $email;
    }
    
    // function to get user
    
    public function getUser() {
        return $this->user;
    }
    
}
