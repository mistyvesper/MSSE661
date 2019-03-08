<?php


$rootDirectory = dirname(dirname(__FILE__));

require_once($rootDirectory . '/App/login.php');
require_once($rootDirectory . '/Models/Database.php');
require_once($rootDirectory . '/Resources/accountMaintenance.php');
require_once($rootDirectory . '/Resources/sanitize.php');

$database = new Database($dbHost, $dbUser, $dbPassword, $dbDBName);

if(isset($_GET['userName'])) {
    $result = checkForExistingAccount($_GET['userName'], $database);
    echo $result;
}

if(isset($_GET['email'])) {
    $result = checkForExistingEmail($_GET['email'], $database);
    echo $result;
}

?>