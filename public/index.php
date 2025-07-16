<?php
    define('ECLO', true);
    ob_start();
    session_start();
    require __DIR__ . '/../vendor/autoload.php';
    require_once __DIR__ . '/../config/bootstrap.php';
    $app->run();