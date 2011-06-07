<?php

/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Service\Implementation;

use Newscoop\Utils\Validation;
use Newscoop\Service\IOutputSettingIssueService;
use Newscoop\Service\Implementation\AEntityBaseServiceDoctrine;
use Newscoop\Entity\Output\OutputSettingsIssue;
use Newscoop\Entity\Issue;

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
     * Provides the Output Settings Issue for the provided issue
     *
     * @param Issue|int $issue
     * 		The issue to be searched, not null, not empty.
     *
     * @return array Newscoop\Entity\Output\OutputSettingsIssue
     * 		The Output Setting, empty array if no Output Setting could be found for the provided issue.
     */
    public function findByIssue($issue)
    {
        if ($issue instanceof Issue) {
            $issue = $issue->getId();
        }
        $em = $this->getEntityManager();
        $repository = $em->getRepository($this->entityClassName);
        $resources = $repository->findByIssue($issue);
        if (isset($resources) && count($resources) > 0) {
            return $resources;
        }
        return array();
    }

    /**
     * Inserts an ouput setting issue
     *
     * @param OutputSettingsIssue $outputSettingsIssue
     */
    public function insert(OutputSettingsIssue $outputSettingsIssue)
    {
        $em = $this->getEntityManager();
        $outputSettingsIssue->setId(null);
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