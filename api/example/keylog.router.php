<?php

// Omitting user ID check, would be handled in authorization so here it is skipped

Router::GET("/customers/", "CustomersController@Report");

Router::Post("/customers/", "CustomersController@Create");

Router::Put("/customers/{email}/", "CustomersController@Update");

Router::Post("/customers/deposit/{ref}/", "CustomersController@Deposit");

Router::Post("/customers/withdraw/{ref}/", "CustomersController@Withdraw");