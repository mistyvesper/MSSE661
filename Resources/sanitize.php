<?php

/**
 * Description
 * 
 * Provides functions for sanitizing user input.
 * 
 * @author misty
 */

// function to sanitize strings

function sanitizeString($var) {
    $var = stripslashes($var);
    $var = strip_tags($var);
    $var = htmlentities($var);
    return $var;
}

// function to sanitize MySQL queries

function sanitizeMySQL($connection, $var) {
    $var = $connection->real_escape_string($var);
    $var = sanitizeString($var);
    return $var;
}
