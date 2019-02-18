<?php

// https://stackoverflow.com/questions/614671/commands-out-of-sync-you-cant-run-this-command-now

function clearStoredResults() {
    global $mysqli;

    do {
         if ($res = $mysqli->store_result()) {
           $res->free();
         }
        } while ($mysqli->more_results() && $mysqli->next_result());        
}
