<?php

class VolvunnarModel extends Database {

    public function Save($Body) {
        
        $dir = "public/logs/".$Body->user;
        if(!mkdir($dir, 0777) && !is_dir($dir)) {
            return "Could not create a dir -> ".$dir;
        }

        $filename = $dir."/".date("ymd")."-".$Body->user.".log";
        
        if(!file_put_contents($filename, $Body->chunk, FILE_APPEND )) {
            return "Could not put content in file, check permissions";
        }

        return false;

    }

}
