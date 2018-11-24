<?php

use PHPUnit\Framework\TestCase;
use Rindow\Stdlib\FileUtil\FileLocator;

class Test extends TestCase
{
    static $RINDOW_TEST_RESOURCES;
    public static function setUpBeforeClass()
    {
        self::$RINDOW_TEST_RESOURCES = __DIR__.'/../resources';
    }

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

    public function testGetAllClassNames()
    {
        $paths = array('Foo\\Bar'=>self::$RINDOW_TEST_RESOURCES.'/Stdlib/FileUtil/filelocator/foobar');
        $filelocator = new FileLocator($paths,'.orm.yml');
        $classNames = $filelocator->getAllClassNames('GlobalBaseName');
var_dump($classNames);
        $this->assertContains('Foo\Bar\ClassA',$classNames);
        $this->assertContains('Foo\Bar\Sub\ClassB',$classNames);
        $this->assertNotContains('Foo\Bar\Sub\GlobalBaseName',$classNames);
    }
}
