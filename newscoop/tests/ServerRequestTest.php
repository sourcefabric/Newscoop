<?php
/**
 * @package Campsite
 *
 * @author Petr Jasek <petr.jasek@sourcefabric.org>
 * @copyright 2010 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */
 
require_once dirname(__FILE__) . '/bootstrap.php';
require_once WWW_DIR . '/classes/ServerRequest.php';

class ServerRequestTest extends PHPUnit_Framework_TestCase
{
    protected $args = array(4, 5, 3);
    protected $object;

    public function setUp()
    {
        $this->object = new ServerRequest('min', $this->args);
    }

    public function testConstruct()
    {
        $this->assertType('ServerRequest', $this->object);
        $this->assertType('ServerRequest', new ServerRequest(array('ServerRequest', 'execute')));
        $this->assertType('ServerRequest', new ServerRequest('ServerRequest::execute'));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testConstructExceptionArray()
    {
        new ServerRequest(array('min'));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testConstructExceptionString()
    {
        new ServerRequest(sha1(uniqid()));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testConstructExceptionNULL()
    {
        new ServerRequest(NULL);
    }

    public function testAllow()
    {
        $this->assertType('ServerRequest', $this->object->allow('sizeof'));
        $this->assertType('ServerRequest', $this->object->allow('sizeof', 'Edit'));
    }

    public function testCheckPermission()
    {
        $this->assertFalse($this->object->checkPermission());

        $this->object->allow('min');
        $this->assertTrue($this->object->checkPermission());
    }

    public function testCheckToken()
    {
        $this->assertFalse($this->object->checkToken());

        $this->setToken();
        $this->assertTrue($this->object->checkToken());
    }

    public function testExecute()
    {
        // test func
        $this->setToken();
        $this->object->allow('min');
        $this->assertEquals(min($this->args), $this->object->execute());

        // test static method
        $sr = new ServerRequest(array('NonConstructorTest', 'staticTic'));
        $sr->allow('NonConstructorTest::staticTic');
        $this->assertEquals('toc', $sr->execute());

        $num = mt_rand();
        $add = mt_rand();
        $sr = new ServerRequest(array('ConstructorTest', 'getNum'), array($num, $add));
        $sr->allow('ConstructorTest::getNum');
        $this->assertEquals($num + $add, $sr->execute());
    }

    private function setToken()
    {
        $_REQUEST[SecurityToken::SECURITY_TOKEN] = SecurityToken::GetToken();
    }
}

class NonConstructorTest
{
    public $msg = 'toc';

    public static function staticTic()
    {
        return 'toc';
    }


    public function getTic()
    {
        return $this->msg;
    }
}

class ConstructorTest
{
    private $num = NULL;

    public function __construct($num = NULL)
    {
        $this->num = $num;
    }

    public function getNum($add)
    {
        return $this->num + $add;
    }
}
