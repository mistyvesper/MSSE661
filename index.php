<!DOCTYPE html>
<!--
Copyright (C) 2019 misty

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title>Nano-site</title>
    </head>
    <body>
        <h1>Documents</h1>
        <div>
            <table>
                <td style="align:left"><input type="text"><button>Search</button></td>
                <td style="align:right"><a href="uploadDocument.php">Upload Document</a></td>
            </table>
        </div>
        <br>
        <?php
        
        require_once 'Document.php';
        require_once 'Collection.php';
        require_once 'DBConnection.php';
        
        // create database connection
        
        $dbConnection = new DBConnection('localhost', 'Regis', 'regis', 'collection');

        // create Collection
        
        $collection = new Collection($dbConnection);
        
        // show Collection
        
        $collection->showCollection();

        ?>
    </body>
</html>
