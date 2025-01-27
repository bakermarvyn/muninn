<?php

require 'api/Volvunnar/Volvunnar.dto.php';
require 'api/Volvunnar/Volvunnar.model.php';
require 'api/Volvunnar/Volvunnar.service.php';

class VolvunnarController {

    public function Input(Request $req, Response $res)
    {

        VolvunnarDTO::Log($req->body);
        $err = VolvunnarService::Restrict($req->body->user);
        if($err) $res->error(false, $err);

        $VolvunnarModel = new VolvunnarModel;
        $err = $VolvunnarModel->Save($req->body);
        $err ? $res->error(false, $err) : $res->json(true);

    }

    public function Output(Request $req, Response $res)
    {

        VolvunnarDTO::Log($req->body);
        $err = VolvunnarService::Restrict($req->body->user);
        if($err) $res->error(false, $err);

        $VolvunnarModel = new VolvunnarModel;
        $err = $VolvunnarModel->Save($req->body);
        $err ? $res->error(false, $err) : $res->json(true);

    }

}
