<?php

/*
 * Copyright (C) 2019 misty
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Description of DBConnection
 *
 * @author misty
 */
class DBConnection {
    
    // encapsulate Document properties by declaring private
    
    private $host;
    private $user;
    private $password;
    private $database;
    private $con;
    
    // constructor
    
    public function __construct($dbHost, $dbUser, $dbPassword, $dbDatabase) {
        $this->host = $dbHost;
        $this->user = $dbUser;
        $this->password = $dbPassword;
        $this->database = $dbDatabase;
    }
    
    // function to connect to MySQL database
    
    public function openDBConnection() {
        $this->con = new mysqli($this->host, $this->user, $this->password, $this->database)
                or die ('Could not connect to the database server' . mysqli_connect_error());
    }
    
    // function to return connection
    
    public function getDBConnection() {
        return $this->con;
    }
    
    // function to close MySQL database connection
    
    public function closeDBConnection() {
        $this->con->close();
    }
}
