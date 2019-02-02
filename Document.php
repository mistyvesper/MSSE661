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
 * Description of Document
 *
 * @author misty
 */
class Document
{
    // encapsulate Document properties by declaring private

    /**
    * @var array
    */
    private $document;

    // constructor to initialize Document properties

    /**
    * Class constructor
    *
    * @return void
    */
    public function __construct($docType, $docTitle, $docExtension, $docSize, $docUploadDate) {

        $this->document['type'] = $docType;
        $this->document['title'] = $docTitle;
        $this->document['extension'] = $docExtension;
        $this->document['size'] = $docSize;
        $this->document['uploadDate'] = $docUploadDate;
    }

    // function to return document array

    /**
    *
    * @return var
    */
    public function getDocument() 
    {
        return $this->document;
    }

    // function to update document properties (using optional parameters)

    public function updateDocument($docType = '', $docTitle = '') {

        // check if document type is empty string or changed and update

        if ($docType != '' && $docType != $this->document['type']) {
            $this->document['type'] = $docType;
        } 

        // check if document title is empty string or changed and update

        if ($docTitle != '' && $docTitle != $this->document['title']) {
            $this->document['title'] = $docTitle;
        }
    }
}
