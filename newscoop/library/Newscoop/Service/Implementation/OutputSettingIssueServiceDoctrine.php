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
use Newscoop\Entity\Output;
use Newscoop\Entity\Issue;

/**
 * Provides the services implementation for the Outputs.
 */
class OutputSettingIssueServiceDoctrine extends AEntityBaseServiceDoctrine
implements IOutputSettingIssueService
{

    /** @var Newscoop\Service\IOutputService */
    private $outputService = NULL;

    /**
     * Provides the ouput service.
     *
     * @return Newscoop\Service\IOutputService
     * 		The service service to be used by this controller.
     */
    public function getOutputService()
    {
        if ($this->outputService === NULL) {
            $this->outputService = $this->getResourceId()->getService(IOutputService::NAME);
        }
        return $this->outputService;
    }

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
     * Provides the Output Settings Issue for the provided issue and output
     *
     * @param Issue|int $issue
     * 		The issue to be searched, not null, not empty.

     * @param Output|int|string $output
     * 		The output to be searched, not null, not empty.
     *
     * @return array Newscoop\Entity\Output\OutputSettingsIssue
     * 		The Output Setting, NULL if no Output Setting could be found for the provided issue.
     */
    public function findByIssueAndOutput($issue, $output)
    {
        /** Get the id if an Output object is supplied */
        /* @var $output Output */
        $outputId = $output;
        if ($output instanceof Output) {
            $outputId = $output->getId();
        }
        /** Get the id if an Issue object is supplied */
        /* @var $issue Issue */
        $issueId = $issue;
        if ($issue instanceof Issue) {
            $issueId = $issue->getId();
        }

        $em = $this->getEntityManager();
        $repository = $em->getRepository($this->entityClassName);
        $resources = $repository->findBy(array('issue' => $issueId, 'output' => $outputId));
        if (!empty($resources)) {
            return $resources[0];
        }
        return NULL;
    }

    function isThemeUsed($theme)
    {
        Validation::notEmpty($theme, 'theme');
        if($theme instanceof Theme){
            $themePath = $theme->getPath();
        } else {
            $themePath = $theme;
        }


        $em = $this->getEntityManager();
        // we need to find if the theme is used by anyoane.
        $q = $em->createQueryBuilder();
        $q->select('count(osi)')
        ->from(OutputSettingsIssue::NAME_1, 'osi')
        ->join('osi.themePath', 'th')
        ->where('th.path = :themePath');

        $q->setParameter('themePath', $themePath);
        return $q->getQuery()->getSingleScalarResult() > 0;
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