<?php

function Learn($dir, $ext, $callback){
    $DirMembers = scandir($dir);
    unset($DirMembers[array_search('.', $DirMembers, true)]);
    unset($DirMembers[array_search('..', $DirMembers, true)]);

    if (count($DirMembers) < 1) return;
    
    foreach($DirMembers as $filename){
        if(is_dir($dir.'/'.$filename)) Learn($dir.'/'.$filename, $ext, $callback);
        else if(strpos($filename,".$ext")) $callback($dir, $filename);
    }
}
