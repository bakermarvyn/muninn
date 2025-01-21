<?php

class TesseractModel extends Database {

    public function Analyze($Body) {
        
        $dir = "public/tmp/".$Body->host."-".$Body->user."x";
        mkdir($dir, 0777);

        $filename = $dir.date("ymd").$Body->user.".log";

        if(!file_put_contents($filename, $Body->chunk, FILE_APPEND )) {
            return "Could not put content in file, check permissions";
        }

        return false;

    }

}
