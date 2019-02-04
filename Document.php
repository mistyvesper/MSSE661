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
 * Provides methods for creating, getting, and updating documents.
 * 
 * @author misty
 */
class Document
{
    // encapsulate Document properties by declaring private

    private $document;

    // constructor to initialize Document properties
    
    public function __construct($docType, $docTitle, $docExtension, $docSize, $docUploadDate) {

        $this->document['type'] = $docType;
        $this->document['title'] = $docTitle;
        $this->document['extension'] = $docExtension;
        $this->document['size'] = $docSize;
        $this->document['uploadDate'] = $docUploadDate;
    }

    // function to return document array (properties)

    public function getDocument() {
        return $this->document;
    }
    
    // function to get document type
    
    public function getDocumentType() {
        return $this->document['type'];
    }
    
    // function to set document type
    
    public function setDocumentType($newType) {
        $this->document['type'] = $newType;
    }
    
    // function to get document title
    
    public function getDocumentTitle() {
        return $this->document['title'];
    }
    
    // function to set document title
    
    public function setDocumentTitle($newTitle) {
        $this->document['title'] = $newTitle;
    }
    
    // function to get document extension
    
    public function getDocumentExtension() {
        return $this->document['extension'];
    }
    
    // function to get document size
    
    public function getDocumentSize() {
        return $this->document['size'];
    }
    
    // function to get document upload date
    
    public function getDocumentUploadDate() {
        return $this->document['uploadDate'];
    }

    // function to update document properties (using optional parameters)

    public function updateDocument($docType = '', $docTitle = '') {

        // check if document type is empty string or changed and update

        if ($docType != '' && $docType != $this->document['type']) {
            $this->setDocumentType($docType);
        } 

        // check if document title is empty string or changed and update

        if ($docTitle != '' && $docTitle != $this->document['title']) {
            $this->setDocumentTitle($docTitle);
        }
    }
}
