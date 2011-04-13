<?php

namespace Tests\Library\Newscoop\Entity;

use PHPUnit_Framework_TestCase,
    Newscoop\Entity\Log;

require_once dirname(__FILE__) . '/../../bootstrap.php';

class LogTest extends PHPUnit_Framework_TestCase
{
    protected $log;

    public function setUp()
    {
        $this->log = new Log;
    }
}
