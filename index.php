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
                <td style="align:right"><button>Upload Document</button></td>
            </table>
        </div>
        <br>
        <?php
        
        require_once 'Document.php';
        require_once 'Collection.php';
        
        // create sample documents
        
        $document1 = new Document('Presentation',
                                  'Nano-site Presentation',
                                  'PPTX',
                                  '4 MB',
                                  '2019-01-18');
        
        $document2 = new Document('CV',
                                  'Software Engineer CV', 
                                  'PDF',
                                  '120 KB',
                                  '2019-01-05');
        
        $document3 = new Document('Thesis',
                                  'Experimental Web Development Framework',
                                  'DOCX',
                                  '200 KB',
                                  '2018-12-23');
        
        $document4 = new Document('Research Paper',
                                  'Effects of Cubicles on Employee Morale',
                                  'PDF',
                                  '1 MB',
                                  '2018-12-10');
        
        // create database connection
        
        $dbConnection = new DBConnection('localhost', 'Regis', 'regis', 'collection');

        // create Collection
        
        $collection = new Collection($dbConnection);
        
        // add Documents to Collection
        
        $collection->addDocument($document1);
        $collection->addDocument($document2);
        $collection->addDocument($document3);
        $collection->addDocument($document4);
        
        // show Collection
        
        $collection->showCollection();

        ?>
    </body>
</html>
