<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity\Repository;

use Newscoop\Entity\Template;

/**
 */
class TemplateRepositoryTest extends \RepositoryTestCase
{
    /** @var string */
    protected $entity = 'Newscoop\Entity\Template';

    /** @var Newscoop\Entity\Repository\TemplateRepository */
    protected $repository;

    public function setUp()
    {
        parent::setUp($this->entity);
        $this->repository = $this->em->getRepository($this->entity);
    }

    public function testTemplateRepository()
    {
        $this->assertType('Newscoop\Entity\Repository\TemplateRepository',
            $this->repository);
    }

    public function testGetTemplate()
    {
        $this->assertEmpty($this->repository->findAll());

        $template = $this->repository->getTemplate('key');
        $this->assertGreaterThan(0, $template->getId());
        $this->assertEquals(1, sizeof($this->repository->findAll()));

        $copy = $this->repository->getTemplate('key');
        $this->assertEquals($template->getId(), $copy->getId());
    }

    public function testSave()
    {
        // test empty
        $templates = $this->repository->findAll();
        $this->assertTrue(empty($templates));

        // save
        $template = new Template("test");
        $this->repository->save($template, array(
            'cache_lifetime' => 20,
        ));
        $this->em->flush();

        // fetch
        $template = $this->repository->findOneBy(array(
            'key' => 'test',
        ));

        // test
        $this->assertFalse(empty($template));
        $this->assertEquals(1, $template->getId());
        $this->assertEquals('test', $template->getKey());
        $this->assertEquals(20, $template->getCacheLifetime());
    }

    public function testDelete()
    {
        $templates = $this->repository->findAll();
        $this->assertTrue(empty($templates));

        $this->assertNull($this->repository->delete("xyz"));

        $template = $this->repository->getTemplate("new");
        $templates = $this->repository->findAll();
        $this->assertFalse(empty($templates));

        $this->assertNull($this->repository->delete("new"));
        $this->em->flush();

        $templates = $this->repository->findAll();
        $this->assertTrue(empty($templates));
    }

    public function testUpdateKey()
    {
        $templates = $this->repository->findAll();
        $this->assertTrue(empty($templates));

        $template = $this->repository->getTemplate('new/template');
        $id = $template->getId();

        $this->repository->updateKey('new/template', 'newtemplate');
        $template = $this->repository->findOneBy(array(
            'key' => 'newtemplate',
        ));

        $this->assertEquals($id, $template->getId());
    }
}
