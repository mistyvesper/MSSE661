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
 * Description of ErrorMessage
 *
 * @author misty
 */
class Message {
    
    public function dbConnectError() {
        echo "<div>Error connecting to database. Please contact the system administrator.<br></div>";
    }
    
    public function dbNoRecords() {
        echo "<div>No records available.</echo>";
    }
    
    public function dbInsertError() {
        echo "<div>Unable to insert record. Please try again or else contact the system administrator.<br></div>";
    }
    
    public function dbDeleteError() {
        echo "<div>Unable to delete record. Please try again or else contact the system administrator.<br></div>";
    }
    
    public function fileUploadSuccessful() {
        echo "<div>File upload successful.</div>";
    }
    
    public function fileUploadFailed() {
        echo "<div>File upload failed. Please try again or else contact the system administrator.</div>";
    }
    
    public function fileDuplicate() {
        echo "<div>File has already been uploaded. Please try a different file.</div>";
    }
    
    public function fileUnsupported() {
        echo "<div>File type not supported. Please try a different file.</div>";
    }
}
