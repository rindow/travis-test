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
        if(version_compare(PHP_VERSION, '7.0')<0 &&
           version_compare(PHP_VERSION, '5.6')>=0    ) {
            $this->assertTrue(extension_loaded('apc'));
        } else {
            $this->assertTrue(true);
        }
    }

    public function testApcu()
    {
        if(version_compare(PHP_VERSION, '5.6')>=0) {
            $this->assertTrue(extension_loaded('apcu'));
        } else {
            $this->assertTrue(true);
        }
    }

    public function testComposer()
    {
        $this->assertTrue(interface_exists('Interop\\Lenient\\Annotation\\AnnotationReader'));
    }
}
