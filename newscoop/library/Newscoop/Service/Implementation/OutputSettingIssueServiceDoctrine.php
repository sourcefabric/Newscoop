<?php

/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Service\Implementation;

use Newscoop\Utils\Validation;
use Newscoop\Service\IOutputService;
use Newscoop\Service\IOutputSettingSectionService;
use Newscoop\Service\IOutputSettingIssueService;
use Newscoop\Entity\Output\OutputSettingsIssue;
use Newscoop\Entity\Issue;
use Newscoop\Service\Model\Search\Search;
use Newscoop\Service\Model\Search\Column;
use Newscoop\Service\Model\SearchOutput;

/**
 * Provides the services implementation for the Outputs.
 */
class OutputSettingIssueServiceDoctrine extends AEntityBaseServiceDoctrine
        implements IOutputSettingIssueService
{

    protected function _init_()
    {
        $this->entityClassName = OutputSettingsIssue::NAME_1;
    }

    /* --------------------------------------------------------------- */

    /**
     * Get the output setting issue only for the issue specified
     *      can be given integer or Issue
     *
     * @param Issue|int $issue
     * @return array
     */
    public function findByIssue($issue)
    {
        if (method_exists($issue, 'getId')) {
            $issue = $issue->getId();
        }
        $em = $this->getEntityManager();
        $repository = $em->getRepository($this->entityClassName);
        $resources = $repository->findByIssue($issue);
        if (isset($resources) && count($resources) > 0) {
            return $resources;
        }
        return NULL;
    }

    /**
     * Inserts an ouput setting issue
     *
     * @param OutputSettingsIssue $outputSettingsIssue
     */
    public function insert(OutputSettingsIssue $outputSettingsIssue)
    {
        $em = $this->getEntityManager();
        $em->persist($outputSettingsIssue);
        $em->flush();
    }

    /**
     * Update an ouput setting issue
     *
     * @param OutputSettingsIssue $outputSettingsIssue
     */
    public function update(OutputSettingsIssue $outputSettingsIssue)
    {
        $em = $this->getEntityManager();
        $em->persist($outputSettingsIssue);
        $em->flush();
    }

    /**
     * Delete an ouput setting issue
     *
     * @param OutputSettingsIssue $outputSettingsIssue
     */
    public function delete(OutputSettingsIssue $outputSettingsIssue)
    {
        $em = $this->getEntityManager();
        $em->remove($outputSettingsIssue);
        $em->flush();
    }

    /* --------------------------------------------------------------- */

    protected function map(Search $search, Column $column)
    {
        return $this->_map($search, $column);
    }

}