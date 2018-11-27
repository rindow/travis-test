<?php

use PHPUnit\Framework\TestCase;
use Rindow\Stdlib\FileUtil\FileLocator;
use Rindow\Database\Pdo\Transaction\Xa\DataSource as XaDataSource;
use Rindow\Transaction\Distributed\Xid;
use Interop\Lenient\Transaction\Xa\XAResource as XAResourceInterface;
use Rindow\Database\Pdo\Connection;

class Test extends TestCase
{
    static $RINDOW_TEST_RESOURCES;
    public static function setUpBeforeClass()
    {
        self::$RINDOW_TEST_RESOURCES = __DIR__.'/../resources';
    }

    public static function getPDOClientStaticPgsql()
    {
        $dsn = "pgsql:host=127.0.0.1;dbname=".RINDOW_TEST_PGSQL_DBNAME;
        $username = RINDOW_TEST_PGSQL_USER;
        $password = RINDOW_TEST_PGSQL_PASSWORD;
        $options  = array();
        $client = new \PDO($dsn, $username, $password, $options);
        return $client;
    }
    public function getPDOClientPgsql()
    {
        return self::getPDOClientStaticPgsql();
    }

    public function rollbackAllPgsql()
    {
        $config = array(
            'dsn' => 'pgsql:host=127.0.0.1;dbname='.RINDOW_TEST_PGSQL_DBNAME,
            'user'     => RINDOW_TEST_PGSQL_USER,
            'password' => RINDOW_TEST_PGSQL_PASSWORD,
        );
        $dataSource = new XaDataSource($config);
        $connection = $dataSource->getConnection();
        $xaResource = $connection->getXAResource();
        $xids = $xaResource->recover(0);
        foreach ($xids as $xid) {
            $xaResource->rollback($xid);
        }
        $connection->close();
    }
    public function countRowPgsql()
    {
        $client = $this->getPDOClientPgsql();
        $stmt = $client->query("SELECT * FROM testdb");
        $c = 0;
        foreach ($stmt as $row) {
            $c += 1;
        }
        return $c;
    }
    public function setUpPgsql()
    {
        usleep( RINDOW_TEST_CLEAR_CACHE_INTERVAL );
        \Rindow\Stdlib\Cache\CacheFactory::clearCache();
        usleep( RINDOW_TEST_CLEAR_CACHE_INTERVAL );
        $this->rollbackAllPgsql();
        $client = $this->getPDOClientPgsql();
        $client->exec("DROP TABLE IF EXISTS testdb");
        $client->exec("CREATE TABLE testdb ( id SERIAL PRIMARY KEY , name TEXT , day DATE, ser INTEGER UNIQUE)");
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
        $this->assertContains('Foo\Bar\ClassA',$classNames);
        $this->assertContains('Foo\Bar\Sub\ClassB',$classNames);
        $this->assertNotContains('Foo\Bar\Sub\GlobalBaseName',$classNames);
    }

    public function testXAResourcePgsqlCommitNormal()
    {
        $this->setUpPgsql();

        $config = array(
            'dsn' => 'pgsql:host=127.0.0.1;dbname='.RINDOW_TEST_PGSQL_DBNAME,
            'user'     => RINDOW_TEST_PGSQL_USER,
            'password' => RINDOW_TEST_PGSQL_PASSWORD,
        );
        $dataSource = new XaDataSource($config);
        $connection = $dataSource->getConnection();
        try {
            $xaResource = $connection->getXAResource();
    
            $xid = new Xid('foo');
            $xaResource->start($xid,XAResourceInterface::TMNOFLAGS);
            $connection->exec("INSERT INTO testdb (name) VALUES ('aaa') ");
            $xaResource->end($xid,XAResourceInterface::TMSUCCESS);
            $this->assertEquals(XAResourceInterface::XA_OK,$xaResource->prepare($xid));
            $result = $xaResource->recover(0);
            $this->assertEquals(1,count($result));
            $this->assertEquals('foo',$result[0]->getGlobalTransactionId());
            $this->assertEquals(0,$this->countRowPgsql());
            $xaResource->commit($xid,false);
            $this->assertEquals(1,$this->countRowPgsql());
        } catch(\Exception $e) {
            echo get_class($e).':'.$e->getMessage()."\n";
            $xaResource->rollback($xid);
            $connection->close();
            echo 'close';
            throw $e;
        }
    }

    /**
     * @expectedException        Rindow\Database\Dao\Exception\RuntimeException
     * @expectedExceptionMessage SQLSTATE[08006]
     * @expectedExceptionCode    -29
     */
    public function testPgsqlLoginFailedError()
    {
        if(getenv('TRAVIS_PHP_VERSION')) {
            $this->markTestSkipped('It can not test pgsql login failure in travis.');
            return;
        }
        $config = array(
            'dsn' => "pgsql:host=127.0.0.1;dbname=".RINDOW_TEST_PGSQL_DBNAME,
            'user'     => RINDOW_TEST_PGSQL_USER,
            'password' => 'wrongpassword',
        );
        try {
            $connection = new Connection($config);
            // 7
            $connection->exec("INSERT INTO testdb (id,name) VALUES ( 1,'boo' )");
        } catch(\Exception $e) {
            $connection->close();
            throw $e;
        }
    }
}
