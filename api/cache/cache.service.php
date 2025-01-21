<?php

class CacheService {

    static public function Restrict($User)
    {

        foreach ([
        ]   as $restricted) 
                if($restricted == $User) 
                    return "User has been restricted";
        

        return null;

    }

}

