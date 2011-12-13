<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\News;

/**
 */
abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * Set up document manager
     *
     * @return Doctrine\ODM\MongoDB\DocumentManager
     */
    protected function setUpOdm()
    {
        global $application;

        $odm = $application->getBootstrap()->getResource('odm');

        if ($odm === null) {
            $this->markTestSkipped('Mongo extension not available.');
        }

        $odm->getConfiguration()->setDefaultDB('phpunit');
        $this->tearDownOdm($odm);

        return $odm;
    }

    /**
     * Tear down document manager
     *
     * @param Doctrine\ODM\MongoDB\DocumentManager $odm
     * @return void
     */
    protected function tearDownOdm(\Doctrine\ODM\MongoDB\DocumentManager $odm)
    {
        $odm->getSchemaManager()->dropDatabases();
        $odm->clear();
    }
}
