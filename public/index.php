<?php
/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
chdir(dirname(__DIR__));

// Decline static file requests back to the PHP built-in webserver
if (php_sapi_name() === 'cli-server') {
    $path = realpath(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    if (__FILE__ !== $path && is_file($path)) {
        return false;
    }
    unset($path);
}

// Setup autoloading
require 'init_autoloader.php';

// Run the application!
if (getenv('APPLICATION_ENV') == 'development') {
    //run development
    Zend\Mvc\Application::init(require 'config/application.config.dev.php')->run();

}else{
    // Desactivar toda notificación de error
    //error_reporting(0);
    //run production
    Zend\Mvc\Application::init(require 'config/application.config.pro.php')->run();
}
