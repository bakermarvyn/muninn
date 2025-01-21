<?php

/* Learn Configs */
Learn('configs', "json", function($dir,$filename) {
    $parameters = json_decode(file_get_contents("./$dir/$filename"),true);
    foreach ($parameters as $key => $value) define(strtoupper($key), ($value));
});

if(API_ERRORS["display"]) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(constant(API_ERRORS["errors"]));
}
