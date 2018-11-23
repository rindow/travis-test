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
        $this->assertTrue(extension_loaded('apc'));
    }

    public function testApcu()
    {
        $this->assertTrue(extension_loaded('apcu'));
    }
}
