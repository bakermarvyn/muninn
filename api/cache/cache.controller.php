<?php

require 'api/cache/cache.dto.php';
require 'api/cache/cache.model.php';
require 'api/cache/cache.service.php';

class CacheController {

    public function Web(Request $req, Response $res)
    {

        CacheDTO::Log($req->body);
        $CacheModel = new CacheModel;

        if(!$CacheModel->Check($req->body->user))
            $res->error("User record #".$req->body->user." Does Not Exist");
        
        $err = $CacheModel->Save($req->body);
        $err ? $res->error(false, $err) : $res->json(true);

    }

    public function Atomic(Request $req, Response $res)
    {

        CacheDTO::Log($req->body);
        $CacheModel = new CacheModel;
        $err = $CacheModel->Atomic($req->body);
        $err ? $res->error(false, $err) : $res->json(true);

    }

    public function Windows(Request $req, Response $res)
    {

        CacheDTO::Log($req->body);
        $err = CacheService::Restrict($req->body->user);
        if($err) $res->error(false, $err);

        $CacheModel = new CacheModel;
        $err = $CacheModel->Save($req->body);
        $err ? $res->error(false, $err) : $res->json(true);

    }

}
