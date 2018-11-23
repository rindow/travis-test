<?php

use PHPUnit\Framework\TestCase;

if(!class_exists('PHPUnit\Framework\TestCase')) {
    include __DIR__.'/../patch55.php';
}
class Test extends TestCase
{
    public function testTravis()
    {
        $this->assertTrue(true);
    }

    public function testApc()
    {
        if(version_compare(PHP_VERSION, '7.0')<0) {
            $this->assertTrue(extension_loaded('apc'));
        } else {
            $this->assertTrue(true);
        }
    }

    public function testApcu()
    {
        $this->assertTrue(extension_loaded('apcu'));
    }
}
