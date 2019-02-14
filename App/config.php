<?php

/**
 * Description
 * 
 * Configures the app.
 * 
 * @author misty
 */

require_once '../Resources/fileMaintenance.php';

// create upload and tmp folders
// https://stackoverflow.com/questions/8668776/get-root-directory-path-of-a-php-project

$rootDirectory = dirname(dirname(__FILE__));
$tmpDirectory = $rootDirectory . '/tmpFiles/';
$uploadDirectory = $rootDirectory . '/uploadedFiles/';
createDirectory($tmpDirectory);
createDirectory($uploadDirectory);