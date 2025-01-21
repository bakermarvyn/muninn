<?php

class TesseractDTO extends DTO {

    static public function Analyze($Files)
    {

        DTO::validate((array) $Files, [
            'pic'   => 'required',
        ]);

    }

}
