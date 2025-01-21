<?php

/**
 * @author  
 * @version 1.0.0
 */

require_once 'vendor/autoload.php';
require 'modules/header.php';       // cors fixer and headers
require 'modules/learn.php';        // directory scanner module
require 'modules/config.php';       // config learner module
require 'modules/response.php';     // http statuses module
require 'modules/router.php';       // router module
require 'modules/database.php';     // mysql db module
require 'modules/dto.php';          // Dto Validator module
require 'api/api.router.php';       // Routing module

require 'app/index.php';            // Load front-end
