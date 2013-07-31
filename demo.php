<?php
/**
 * Run specified demo from 'demo' subfolder
 */
error_reporting(2047);
define('APPLICATION_PATH', realpath(dirname(__FILE__)));
set_include_path(
        APPLICATION_PATH.'/geometry'.PATH_SEPARATOR.
        APPLICATION_PATH.'/algebra'.PATH_SEPARATOR.
        APPLICATION_PATH.'/routines'.PATH_SEPARATOR.
        get_include_path());
spl_autoload_register(function ($sClass) 
{
    return require_once(str_replace('_', '/', $sClass) . '.php');
});

if($_SERVER['argc']>1)
{
    $sDemo = preg_replace('/\.php$/i', '', $_SERVER['argv'][1]);
    if(@include('demo/'.$sDemo.'.php'))
    {
        exit(PHP_EOL.'Demo "'.$sDemo.'" finished'.PHP_EOL);
    }
    exit(PHP_EOL.'Demo "'.$sDemo.'" not found'.PHP_EOL);
}
exit(PHP_EOL.'Demo is not specified'.PHP_EOL);