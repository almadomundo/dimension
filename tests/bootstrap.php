<?php
spl_autoload_register(function($name)
{
    $name = explode('\\', $name);
    $name = __DIR__.'/../'.join('/', $name).'.php';
    if(file_exists($name))
    {
        return require_once($name);
    }
});