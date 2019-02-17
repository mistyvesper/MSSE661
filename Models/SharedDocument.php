<?php


class SharedDocument {
    
    // encapsulate Document properties by declaring private

    private $sharedDocument;

    // constructor to initialize SharedDocument properties
    
    public function __construct($docType, $docTitle, $docExtension, $docSize, $docShareDate, $docSharedBy, $docSharedWith) {

        $this->sharedDocument['type'] = $docType;
        $this->sharedDocument['title'] = $docTitle;
        $this->sharedDocument['extension'] = $docExtension;
        $this->sharedDocument['size'] = $docSize;
        $this->sharedDocument['sharedDate'] = $docShareDate;
        $this->sharedDocument['sharedBy'] = $docSharedBy;
        $this->sharedDocument['sharedWith'] = $docSharedWith;
    }

    // function to return document array (properties)

    public function getSharedDocument() {
        return $this->sharedDocument;
    }
    
    // function to get document type
    
    public function getSharedDocumentType() {
        return $this->sharedDocument['type'];
    }
    
    // function to get document title
    
    public function getSharedDocumentTitle() {
        return $this->sharedDocument['title'];
    }
    
    // function to get document extension
    
    public function getSharedDocumentExtension() {
        return $this->sharedDocument['extension'];
    }
    
    // function to get document size
    
    public function getSharedDocumentSize() {
        return $this->sharedDocument['size'];
    }
    
    // function to get document shared date
    
    public function getSharedDocumentSharedDate() {
        return $this->sharedDocument['sharedDate'];
    }
    
    // function to get document shared by
    
    public function getSharedDocumentSharedBy() {
        return $this->sharedDocument['sharedBy'];
    }
    
    // function to get document upload date
    
    public function getSharedDocumentSharedWith() {
        return $this->sharedDocument['sharedWith'];
    }
}
