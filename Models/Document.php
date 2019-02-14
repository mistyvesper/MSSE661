<?php


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
}
