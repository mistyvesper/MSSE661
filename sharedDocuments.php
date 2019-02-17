<?php

/**
 * Description
 * 
 * Displays shared documents page.  
 * 
 * @author misty
 */

    require_once 'header.php';    
    
    // display web page

    echo "<h1>Shared Documents</h1>";
    
    // show Collection

    $collection->showSharedCollection();
    
    // end of web page
    
    echo "</body>
        </html>";