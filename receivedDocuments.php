<?php

/**
 * Description
 * 
 * Displays received documents page.  
 * 
 * @author misty
 */

    require_once 'header.php';    
    
    // display web page

    echo "<h1>Received Documents</h1>";
    
    // show Collection

    $collection->showReceivedCollection();
    
    // end of web page
    
    echo "</body>
        </html>";