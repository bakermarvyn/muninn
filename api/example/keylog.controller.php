<?php

require 'api/customers/customers.dto.php';
require 'api/customers/customers.model.php';

class CustomersController {

    public function Create($req, $res)
    {

        CustomersDTO::Create($req->body);

        $Model = new CustomersModel;

        if($Model->EmailExists($req->body->email))
            $res->error('Email\'s already in use');

        $res->json($Model->Create($req->body));

    }
    
    public function Update($req, $res)
    {
        
        CustomersDTO::Update($req->body, $req->params->email);

        $Model = new CustomersModel;

        if(!$Model->EmailExists($req->params->email))
            $res->error('Email\'s not in use');

        $res->json($Model->Update($req->body, $req->params->email));

    }

    public function Deposit($req, $res)
    {

        CustomersDTO::Deposit($req->body, $req->params->ref);

        $Model = new CustomersModel;

        $Bonus = $Model->Bonus($req->body->amount, $req->params->ref);
        
        $req->body->bonus_amount = $Bonus;

        $res->json($Model->Deposit($req->body, $req->params->ref), "Deposited " . $req->body->amount);

    }

    public function Withdraw($req, $res)
    {
        
        CustomersDTO::Withdraw($req->body, $req->params->ref);

        $Model = new CustomersModel;

        $state = $Model->CheckWithdraw($req->body->amount, $req->params->ref);

        $req->body->rejected = $state->reject ? 1 : 0;

        $result = $Model->Withdraw($req->body, $req->params->ref);

        if($state->reject)
            $res->error("Not enough on your account, current: " . $state->balance . 
                        "(Bonus: " . $state->bonus . "), asked: " . $req->body->amount);
        
        $res->json($result, "Withdrawed " . $req->body->amount);

    }

    public function Report($req, $res)
    {

        $ValidDates = CustomersDTO::Report($req->body);

        if(!@$ValidDates->end) @$req->body->end = date('Y-m-d');

        if(!@$ValidDates->start)
            @$req->body->start = date('Y-m-d', strtotime('-7 days', strtotime($req->body->end)));

        $Model = new CustomersModel;

        $res->json($Model->Report($req->body->start, $req->body->end));

    }

}