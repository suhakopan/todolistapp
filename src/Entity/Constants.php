<?php

namespace App\Entity;

class Constants
{
    public function getProviders()
    {
        $providers = [
            "http://www.mocky.io/v2/5d47f235330000623fa3ebf7",
            "http://www.mocky.io/v2/5d47f24c330000623fa3ebfa"
        ];
        return $providers;
    }

    public static $weeklyHour = 45;
}