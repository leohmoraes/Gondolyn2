<?php namespace App\Prototypes;

class AppPrototype extends \Prototype {

    public static function welcomeMessage($user)
    {
        return "Welcome to Gondolyn ".$user;
    }

}