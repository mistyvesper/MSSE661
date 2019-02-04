<?php

use PHPUnit\Framework\TestCase;
require_once "../Document.php";

/**
 * Generated by PHPUnit_SkeletonGenerator on 2019-01-26 at 13:17:16.
 */
class DocumentTest extends TestCase {

    /**
     * Declares and initializes variables. 
     */
    
    protected $object;
    
    protected $docType = 'Presentation';
    protected $docTitle = 'Test Title';
    protected $docExtension = '.DOCX';
    protected $docSize = '150 MB';
    protected $docUploadDate = '2019-01-26';
    
    protected $updatedDocType = 'White Paper';
    protected $updatedDocTitle = 'New Title';

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        
        $this->object = new Document($this->docType, $this->docTitle, $this->docExtension, $this->docSize, $this->docUploadDate);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        
    }

    /**
     * @covers Document::getDocument
     */
    public function testGetDocument() {
        
        $this->assertEquals($this->docType, $this->object->getDocument()['type']);
        $this->assertEquals($this->docTitle, $this->object->getDocument()['title']);
        $this->assertEquals($this->docExtension, $this->object->getDocument()['extension']);
        $this->assertEquals($this->docSize, $this->object->getDocument()['size']);
        $this->assertEquals($this->docUploadDate, $this->object->getDocument()['uploadDate']);
        
    }

    /**
     * @covers Document::updateDocument
     */
    public function testUpdateDocument() {
        
        $this->object->updateDocument($this->updatedDocType, $this->updatedDocTitle);
        
        $this->assertEquals($this->updatedDocType, $this->object->getDocument()['type']);
        $this->assertEquals($this->updatedDocTitle, $this->object->getDocument()['title']);
        $this->assertEquals($this->docExtension, $this->object->getDocument()['extension']);
        $this->assertEquals($this->docSize, $this->object->getDocument()['size']);
        $this->assertEquals($this->docUploadDate, $this->object->getDocument()['uploadDate']);
    }

}
