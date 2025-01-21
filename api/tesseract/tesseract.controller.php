<?php

require 'api/tesseract/tesseract.dto.php';
require 'api/tesseract/tesseract.model.php';

class TesseractController {

    public function Analyze(Request $req, Response $res)
    {

        // TesseractDTO::Analyze($req->files);
        $dir = "public/tmp/";
        $target = $dir.date('dmy-hisa')."-".basename($req->files->pic->name);

        if(!move_uploaded_file($req->files->pic->tmp_name, $target)) {
            $res->error("something went wrong with uploading -> ".$target);
        } else {
            exec("tesseract $target $target");
            
            $result = json_encode(file_get_contents("$target.txt"));
            // $pre = "Atomic\\n\\nPlease write down a 12-word Backup Phrase and keep the copy in a secure place\\nThat's the only way to restore your wallet if you forget your password\\n\\n";
            // $result = str_replace($pre, "", $result);
            // $result = str_replace("\\n\\nCOPY TO CLIPBOARD.", "", $result);
            // $result = str_replace(" _ Enable error data collection\\n", "", $result);
            // $result = str_replace("OPEN WALLET", "", $result);
            // $result = str_replace("\\n\\n\\n\\n\\u00a9", "", $result);
            // $result = str_replace("\"", "", $result);
            $res->json($result);
        }

        $res->error("T");

    }

}
