<?php

declare(strict_types=1);

define('APP_ROOT', dirname(__DIR__));

require APP_ROOT . '/framework/vendor/autoload.php';

$_ENV['APP_ENV']   = 'testing';
$_ENV['APP_DEBUG'] = 'true';
$_ENV['DB_HOST']   = getenv('DB_HOST') ?: 'localhost';
$_ENV['DB_NAME']   = getenv('DB_NAME') ?: 'whmcs_test';
$_ENV['DB_USER']   = getenv('DB_USER') ?: 'root';
$_ENV['DB_PASS']   = getenv('DB_PASS') ?: '';
