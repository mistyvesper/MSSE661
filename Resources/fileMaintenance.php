<?php

/**
 * Description
 * 
 * Provides functions for web app account maintenance.
 * 
 * @author misty
 */

// function to get file extension by file type

function getFileExtensionByType($type) {
    
    switch($type) {
            case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':     $docExtension = 'DOCX';  break;
            case 'application/pdf':                                                             $docExtension = 'PDF';   break;
            case 'application/msword':                                                          $docExtension = 'DOC';   break;
            case 'application/vnd.openxmlformats-officedocument.presentationml.presentation':   $docExtension = 'PPTX';  break;
            case 'text/plain':                                                                  $docExtension = 'TXT';   break;
            default:                                                                            $docExtension = '';      break;
        }
        
    return $docExtension;
}

// function to get friendly file size by bytes

function getFriendlyFileSize($sizeInBytes) {
    
    if ($sizeInBytes == null) {
        $docSize = 'Unknown';
    } else if ($sizeInBytes > 1073741824) {
        $docSize = round($sizeInBytes / 1073741824, 0) . 'GB';
    } else if ($sizeInBytes > 1048576) {
        $docSize = round($sizeInBytes / 1048576, 0) . 'MB';
    } else if ($sizeInBytes > 1024) {
        $docSize = round($sizeInBytes / 1024, 0) . 'KB';
    } else {
        $docSize = $sizeInBytes . 'B';
    }
    
    return $docSize;
}

// function to create directory

function createDirectory($directory) {
    
    // check if directory exists and create as needed
    
    if (!file_exists($directory)) {
        $old_umask = umask(0);
        mkdir($directory, 0777);
        umask($old_umask);
    }
    
    // return status
    
    return file_exists($directory);
}

// function to download file
// https://secure.php.net/manual/en/function.readfile.php

function downloadFile($file) {
    if (file_exists($file)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename($file).'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        readfile($file);
        exit;
    }
}