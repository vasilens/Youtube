<?php
namespace controller;
use exceptions\InvalidFileException;

function uploadVideo ($file, $username){
    if (is_uploaded_file($_FILES[$file]["tmp_name"])) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $_FILES[$file]["tmp_name"]);
        if ($mime != 'video/mp4') {
            throw new InvalidFileException ("File is not in MP4 video format.");
        }
        $file_name_parts = explode(".", $_FILES[$file]["name"]);
        $extension = $file_name_parts[count($file_name_parts) - 1];
        $filename = $username . "-" . time() . "." . $extension;
        $file_url = "uploads" . DIRECTORY_SEPARATOR . $filename;
        if (move_uploaded_file($_FILES[$file]["tmp_name"], $file_url)){
            return $file_url;
        }
        else {
            throw new InvalidFileException("File handling error.");
        }
    }
    return false;
}

function uploadImage($file, $username){
    if (is_uploaded_file($_FILES[$file]["tmp_name"])) {$finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $_FILES[$file]["tmp_name"]);
        if (!(in_array($mime, array ('image/bmp', 'image/jpeg', 'image/png')))){
            throw new InvalidFileException ("File is not in supported format.");
        }
        $file_name_parts = explode(".", $_FILES[$file]["name"]);
        $extension = $file_name_parts[count($file_name_parts) - 1];
        $filename = $username . "-" . time() . "." . $extension;
        $file_url = "uploads" . DIRECTORY_SEPARATOR . $filename;
        if (move_uploaded_file($_FILES[$file]["tmp_name"], $file_url)){
            return $file_url;
        }
    }
    return false;
}