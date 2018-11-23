<?php

use PHPUnit\Framework\TestCase;

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
