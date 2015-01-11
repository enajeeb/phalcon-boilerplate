<?php

use \Phalcon\DI\FactoryDefault as PhDi;

error_reporting(E_ALL);

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(__FILE__)));
}

try {

    /**
     * Read services (bootstrap)
     */
    include __DIR__ . "/../app/config/bootstrap.php";

    /**
     * Handle the request
     */
    $di = new PhDi();
    $app = new Bootstrap($di);

    echo $app->run(array());

} catch (\Phalcon\Exception $e) {
    echo $e->getMessage();
} catch (PDOException $e){
    echo $e->getMessage();
}
