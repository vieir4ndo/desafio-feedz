<?php
namespace modulosecontrollers\controllers;

abstract class Controller{
    protected $container;

    public function __construct($container){ 
        //Slim\Container
        $this->container = $container;
    }

    public function __get($key){
        if ($this->container->{$key}) {
            return $this->container->{$key};
        }
    }
}

?>