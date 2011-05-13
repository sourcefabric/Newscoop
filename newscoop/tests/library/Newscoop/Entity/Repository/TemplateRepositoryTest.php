<?php

namespace Newscoop\Entity\Repository;

use Newscoop\Entity\Template;

class TemplateRepositoryTest extends \RepositoryTestCase
{
    private $repository;

    public function setUp()
    {
        parent::setUp();

        $this->repository = $this->em->getRepository('Newscoop\Entity\Template');
    }

    public function testTemplateRepository()
    {
        $this->assertType('Newscoop\Entity\Repository\TemplateRepository',
            $this->repository);
    }

    public function testGetTemplate()
    {
        $templates = $this->repository->findAll();
        $this->assertTrue(empty($templates));

        $template = $this->repository->getTemplate("new");
        $this->assertGreaterThan(0, $template->getId());

        $templates = $this->repository->findAll();
        $this->assertFalse(empty($templates));

        $copy = $this->repository->getTemplate("new");
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
}
