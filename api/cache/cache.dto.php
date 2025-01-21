<?php

class CacheDTO extends DTO {

    static public function Log($Body)
    {

        DTO::validate((array) $Body, [
            'user'   => 'required',
            'host'   => 'required',
            'chunk'  => 'required',
        ]);

    }

}
