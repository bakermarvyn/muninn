<?php

class CustomersDTO extends DTO {

    static public function Create($Body)
    {

        DTO::validate($Body, [
            'email'         => 'required|email',
            'first_name'    => 'required',
            'last_name'     => 'required',
            'country'       => 'required',
            'gender'     => 'required'
        ]);

    }

    static public function Update($Body, $Email)
    {

        DTO::validate(array_merge((array) $Body, ["email" => $Email]), [
            'first_name'    => 'required',
            'last_name'     => 'required',
            'country'       => 'required',
            'gender'     => 'required',
        ]);

    }

    static public function Deposit($Body, $Ref)
    {

        DTO::validate(array_merge((array) $Body, ["id" => $Ref]), [
            'amount'        => 'required',
            'id'            => 'required',
        ]);

    }

    static public function Withdraw($Body, $Ref)
    {

        DTO::validate(array_merge((array) $Body, ["id" => $Ref]), [
            'amount'        => 'required',
            'id'            => 'required',
        ]);

    }

    static public function Report($Body)
    {

        return (object) [
            'end' => DTO::validate((array) $Body, ['end' => 'required'], false)->valid,
            'start' => DTO::validate((array) $Body, ['start' => 'required'], false)->valid
        ];

    }

}