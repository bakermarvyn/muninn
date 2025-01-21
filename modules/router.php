<?php

require_once './modules/response.php';

class Router {

    static function Get($ctrler, $method) 
    {
        if($_SERVER["REQUEST_METHOD"] != 'GET') return;
        self::register($ctrler, $method);
    }

    static function Post($ctrler, $method) 
    {
        if($_SERVER["REQUEST_METHOD"] != 'POST') return;
        self::register($ctrler, $method);
    }

    static function Put($ctrler, $method) 
    {
        if($_SERVER["REQUEST_METHOD"] != 'PUT') return;
        self::register($ctrler, $method);
    }

    static function Delete($ctrler, $method) 
    {
        if($_SERVER["REQUEST_METHOD"] != 'DELETE') return;
        self::register($ctrler, $method);
    }

    static function register($path, $constructor, $file = false) {

        $req = new Request;
        // setup request params
        $req->query = $_GET;
        $req->files = $_FILES;
        $req->params = $_POST;
        $req->body = [];

        $req->body = json_decode(file_get_contents('php://input'), TRUE);
        $RAWJSONREQUESTPARAMETERS = $req->body == null ? [] : $req->body;

        if($_SERVER["REQUEST_METHOD"] == 'GET') $_GET = array_merge($_GET, $RAWJSONREQUESTPARAMETERS);
        
        if($_SERVER["REQUEST_METHOD"] == 'POST') $_POST = array_merge($_POST, $RAWJSONREQUESTPARAMETERS);

        if($_SERVER["REQUEST_METHOD"] == 'PUT') $_POST = array_merge($_POST, $RAWJSONREQUESTPARAMETERS);

        if($_SERVER["REQUEST_METHOD"] == 'DELETE') $_POST = array_merge($_POST, $RAWJSONREQUESTPARAMETERS);
        /* END OF REQ PARAMS */


        // declare query params
        // find if there is any {?} parameter in the $path
        $params = [];
        $paramKey = [];
        preg_match_all("/(?<={).+?(?=})/", $path, $paramMatches);
        
        // if the $path does not contain any {?} start constructing;
        if(empty($paramMatches[0])) {
            $uri = explode('?',str_replace(API_ENTRY, "", $_SERVER['REQUEST_URI']))[0];
            
            //replacing first and last forward slashes
            //$_SERVER['REQUEST_URI'] will be empty if req uri is /
            if(!empty($uri)) {
                $path = preg_replace("/(^\/)|(\/$)/","", $path);
                $reqUri =  preg_replace("/(^\/)|(\/$)/","", $uri);
            }else{
                $reqUri = "/";
            }
            
            if($reqUri == $path) {
                $params = [];
                $req->query = self::convertArrToObj($req->query);
                $req->files = self::convertArrToObj($req->files);
                $req->params = self::convertArrToObj($req->params);
                $req->body = self::convertArrToObj($req->body);
                self::callClass($constructor, $file, $req);
                exit();
            }
            return;
        }
        // setting parameters names
        foreach($paramMatches[0] as $key) $paramKey[] = $key;
        
       
        // replacing first and last forward slashes
        // $_SERVER['REQUEST_URI'] will be empty if req uri is /

        $uri = explode('?', str_replace(API_ENTRY, "", $_SERVER['REQUEST_URI']))[0];
        //replacing first and last forward slashes
        //$_SERVER['REQUEST_URI'] will be empty if req uri is /
        if(!empty($uri)) {
            $path = preg_replace("/(^\/)|(\/$)/","", $path);
            $reqUri =  preg_replace("/(^\/)|(\/$)/","", $uri);
        }else{
            $reqUri = "/";
        }
        //exploding $path address
        $uri = explode("/", $path);

        //will store index number where {?} parameter is required in the $path
        $indexNum = [];

        //storing index number, where {?} parameter is required with the help of regex
        foreach($uri as $index => $param) if(preg_match("/{.*}/", $param)) $indexNum[] = $index;
        

        // exploding request uri string to array to get
        // the exact index number value of parameter from $_SERVER['REQUEST_URI']
        $reqUri = explode("/", $reqUri);

        // running for each loop to set the exact index number with reg expression
        // this will help in matching $path
        foreach($indexNum as $key => $index){

            // in case if req uri with param index is empty then return
            // because url is not valid for this $path
            if(empty($reqUri[$index])) {
                (new Response)->error('Wrong Route', 404);
                return;
            }
            //setting params with params names
            $params[$paramKey[$key]] = $reqUri[$index];

            //this is to create a regex for comparing $path address
            $reqUri[$index] = "{.*}";
        }
        
        if(!$req->params) $req->params = [];
        if(!$params) $params = [];

        $req->params = array_merge($req->params, $params);
        // converting array to sting
        $reqUri = implode("/",$reqUri);

        // replace all / with \/ for reg expression
        // regex to match $path is ready !
        $reqUri = str_replace("/", '\\/', $reqUri);

        // now matching $path with regex
        if(preg_match("/$reqUri/", $path))
        {
            $req->query = self::convertArrToObj($req->query);
            $req->files = self::convertArrToObj($req->files);
            $req->params = self::convertArrToObj($req->params);
            $req->body = self::convertArrToObj($req->body);
            self::callClass($constructor, $file, $req);
            exit();
        }

    }
    
    static public function callClass($constructor, $file, Request $req)
    {

        $res = new Response();
        $parts = explode('@', $constructor);

        if(!$file) {
            $tmp = str_replace('controller','', strtolower($parts[0]));
            $file = 'api/' . $tmp . '/' . $tmp . '.controller.php';
        }
        
        include_once($file);
        if($parts && COUNT($parts) != 2) {
            (new Response)->error('Wrong Route', 404);
            // wrong parameter
            return;
        } else if(!class_exists($parts[0])) {
            (new Response)->error('Wrong Route', 404);
            // wrong class
            return;
        } else if(!method_exists($parts[0], $parts[1])) {
            (new Response)->error('Wrong Route', 404);
            // wrong method
            return;
        }

        // declare object params
        $class = $parts[0];
        $method = $parts[1];

        // $Req = self::convertArrToObj($req);    
        if(method_exists($class, 'constructor') || method_exists($class, '__construct')) $obj = new $class();
        else $obj = new $class;
        $obj->$method($req, $res);
        
    }

    static public function convertArrToObj($array)
    {
        $obj = new stdClass();
        
        foreach ($array as $k => $v) {
            if (strlen($k)) {
                if (is_array($v)) {
                    $obj->{$k} = self::convertArrToObj($v); //RECURSION
                } else {
                    $obj->{$k} = $v;
                }
            }
        }
        
        return $obj;
    }

}
