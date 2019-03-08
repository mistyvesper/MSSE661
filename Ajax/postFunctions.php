<?php

$rootDirectory = dirname(dirname(__FILE__));

require_once($rootDirectory . '/header.php');

// check for search values

if (isset($_POST['search'])) {
    
    $searchValue = strtolower(sanitizeString($_POST['search']));

    if ($_POST['search'] != '') {
        unset($_SESSION['searchCollection']);
        $_SESSION['searchCollection'] = $_SESSION['collection'];
        $result = $collection->searchCollection($searchValue);
        $collection->showCollection();
        echo $result;
    } else {
        $_SESSION['searchCollection'] = $_SESSION['collection'];
        $collection->showCollection();
        echo true;
    } 
}