<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Subscription;

use Newscoop\Entity\User,
    Newscoop\Entity\Publication,
    Newscoop\Entity\Issue,
    Newscoop\Entity\Section;

/**
 */
class IpFacadeTest extends \TestCase
{
    public function setUp() {
        $this->orm = $this->setUpOrm('Newscoop\Entity\User', 'Newscoop\Entity\User\Ip', 'Newscoop\Entity\Acl\Role');
        $this->facade = new IpFacade($this->orm);

        $this->user = new User('test');
        $this->orm->persist($this->user);
        $this->orm->flush();
    }

    public function tearDown()
    {
        $this->tearDownOrm($this->orm);
    }

    public function testInstance()
    {
        $this->assertInstanceOf('Newscoop\Subscription\IpFacade', $this->facade);
    }

    public function testWorkflow()
    {
        $this->assertEquals(0, count($this->facade->findByUser(1)));

        $ip = $this->facade->save(array(
            'user' => 1,
            'ip' => '1.1.1.1',
            'number' => 5,
        ));

        $this->assertInstanceOf('Newscoop\Entity\User\Ip', $ip);
        $this->assertEquals('1.1.1.1', $ip->getIp());
        $this->assertEquals(5, $ip->getNumber());

        $this->assertEquals(1, count($this->facade->findByUser(1)));

        $this->facade->delete(array(
            'user' => 1,
            'ip' => '1.1.1.1',
        ));

        $this->assertEquals(0, count($this->facade->findByUser(1)));
    }
}
