<?php
if(file_exists(__DIR__.'/../vendor/autoload.php')) {
	include __DIR__.'/../vendor/autoload.php';
}
if(!class_exists('PHPUnit\Framework\TestCase')) {
    include __DIR__.'/travis/patch55.php';
}
