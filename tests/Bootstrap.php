<?php
if(file_exists(__DIR__.'/../vendor/autoload.php')) {
	include __DIR__.'/../vendor/autoload.php';
}
define('RINDOW_TEST_CLEAR_CACHE_INTERVAL',100000);

define('RINDOW_TEST_PGSQL_USER','postgres');
define('RINDOW_TEST_PGSQL_PASSWORD','password');
define('RINDOW_TEST_PGSQL_DBNAME','postgres');

if(!class_exists('PHPUnit\Framework\TestCase')) {
    include __DIR__.'/travis/patch55.php';
}
