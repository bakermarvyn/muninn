<?php

use Rakit\Validation\Validator;

class DTO {

    static public function validate($subject = [], $validators = [], $response = true) {
        $validator = new Validator;
        $validation = $validator->make((array) $subject, (array) $validators);
        $validation->validate();
        if($validation->fails() && $response) (new Response())->error(self::getAsMessage($validation->errors()));
        else return (object) ['valid' => !$validation->fails(), 'error' => $validation->errors()];
    }

    static public function getAsMessage($errors)
    {
        return implode(',',$errors->firstOfAll());
    }

}
