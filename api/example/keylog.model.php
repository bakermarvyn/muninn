<?php

class CustomersModel extends Database {

    public function EmailExists($Email)
    {

        parent::SET(" SELECT email FROM `customers` WHERE email = :email; ", [ "email" => $Email ]);
        return parent::Exists();

    }
    
    public function Create($Body)
    {

        return  parent::SET("   INSERT INTO `customers` SET 
                                            `email`      = :email,
                                            `first_name` = :first_name,
                                            `last_name`  = :last_name,
                                            `country`    = :country,
                                            `gender`  = :gender,
                                            `bonus_per`  = :bonus_per,
                                            `createdAt`  = NOW(); ",
                                        [
                                            "email"      => $Body->email,
                                            "first_name" => $Body->first_name,
                                            "last_name"  => $Body->last_name,
                                            "country"    => $Body->country,
                                            "gender"  => $Body->gender,
                                            "bonus_per"  => rand(BONUS_RANGE_MIN, BONUS_RANGE_MAX) // from /configs/bonus_range.json
                                        ]);

    }
    
    public function Update($Body, $Email)
    {

        return  parent::SET("   UPDATE  `customers`  SET 
                                        `first_name` = :first_name,
                                        `last_name`  = :last_name,
                                        `country`    = :country,
                                        `gender`  = :gender
                                WHERE   `email`      = :email; ",
                                    [
                                        "first_name" => $Body->first_name,
                                        "last_name"  => $Body->last_name,
                                        "country"    => $Body->country,
                                        "gender"  => $Body->gender,
                                        "email"      => $Email,
                                    ]);

    }

    public function Bonus($Amount, $Ref)
    {

        $state = parent::GET("  SELECT      COUNT(`deposits`.id) AS 'times', `customers`.bonus_per
                                FROM        `customers`
                                LEFT JOIN   `deposits` ON `deposits`.customer_id = `customers`.id
                                WHERE       `customers`.id = :customer_id 
                                GROUP BY    `customers`.id ", 
                                [ "customer_id" => $Ref ])[0];
                                
        return ($state["times"] + 1) % 3 == 0 ? ($Amount * ($state["bonus_per"] / 100)) : 0;

    }

    public function Deposit($Body, $Ref)
    {

        return  parent::SET("   INSERT INTO `deposits` SET
                                            `customer_id`   = :customer_id,
                                            `amount`        = :amount,
                                            `bonus_amount`  = :bonus_amount,
                                            `createdAt`     = NOW(); ",
                                        [
                                            "customer_id"   => $Ref,
                                            "amount"        => $Body->amount,
                                            "bonus_amount"  => $Body->bonus_amount,
                                        ]);

    }

    public function CheckWithdraw($Amount, $Ref)
    {
        
        $state = parent::GET(" SELECT balance, bonus_balance FROM customers WHERE id = :id ", [ "id" => $Ref ])[0];

        return (object) [
            "balance"       => $state["balance"],
            "bonus"         => $state["bonus_balance"],
            "reject"        => $Amount > $state["balance"],
            "total_balance" => $state["balance"] + $state["bonus_balance"],
        ];

    }

    public function Withdraw($Body, $Ref)
    {

        return  parent::SET("   INSERT INTO `withdraws` SET
                                            `customer_id`   = :customer_id,
                                            `amount`        = :amount,
                                            `rejected`      = :rejected,
                                            `createdAt`     = NOW(); ",
                                        [
                                            "customer_id"   => $Ref,
                                            "amount"        => $Body->amount,
                                            "rejected"      => $Body->rejected,
                                        ]);
                                                    
    }

    public function Report($Start, $End)
    {

        return  parent::GET("   SELECT		IFNULL(DATE(`deposits`.`createdAt`), DATE(`withdraws`.`createdAt`)) AS 'date',
                                            `customers`.`country` AS 'country',
                                            IFNULL(COUNT(DISTINCT `customers`.`id`),0) AS 'unique customers',
                                            IFNULL(COUNT(DISTINCT `deposits`.`id`),0)  AS 'no of deposits',
                                            IFNULL(SUM(`deposits`.`amount`),0) AS 'total deposit amount',
                                            IFNULL(COUNT(DISTINCT `withdraws`.`id`),0) AS 'no of withdrawals',
                                            IFNULL(SUM(`withdraws`.`amount`),0) AS 'total withdrawal amount'
                                    
                                FROM        `customers`
                                LEFT JOIN   `deposits` on `deposits`.`customer_id` = `customers`.`id`
                                LEFT JOIN   `withdraws` on `withdraws`.`customer_id` = `customers`.`id` AND `withdraws`.rejected = 0
                                WHERE       (DATE(`deposits`.`createdAt`) BETWEEN DATE(:startD) AND DATE(:endD)) OR 
                                            (DATE(`withdraws`.`createdAt`) BETWEEN DATE(:startW) AND DATE(:endW))
                                GROUP BY 	`customers`.country, DATE(`deposits`.`createdAt`), DATE(`withdraws`.`createdAt`); ",
                                            [ "startD" => $Start, "endD" => $End, "startW" => $Start, "endW" => $End ]);

    }

}