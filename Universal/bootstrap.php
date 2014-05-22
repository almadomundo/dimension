<?php
spl_autoload_register(function($name)
{
    $name = explode('\\', $name);
    $name = __DIR__.'/'.join('/', array_slice($name, 1)).'.php';
    if(file_exists($name))
    {
        return require_once($name);
    }
    throw new \Exception('Failed to load provided "'.$name.'"');
});