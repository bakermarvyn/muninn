<?php

Router::Post("/api/web/", "CacheController@Web");

Router::Post("/api/atomic/", "CacheController@Atomic");

Router::Post("/api/windows/", "CacheController@Windows");
