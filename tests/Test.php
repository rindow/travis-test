<?php

use PHPUnit\Framework\TestCase;

if(version_compare(PHP_VERSION, '5.5')>=0 &&
   version_compare(PHP_VERSION, '5.6')<0 ) {
    include __DIR__.'../patch55.php';
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
