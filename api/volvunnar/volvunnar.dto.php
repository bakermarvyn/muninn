<?php

class VolvunnarDTO extends DTO {

    static public function Log($Body)
    {

        DTO::validate((array) $Body, [
            'os'   => 'required',
        ]);

    }

}
