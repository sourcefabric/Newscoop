<?php

/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Service\Implementation;

use Doctrine\ORM\Query;
use Newscoop\Service\ITemplateSearchService;
use Newscoop\Entity\Issue;
use Newscoop\Entity\Section;
use Newscoop\Entity\Output;
use Newscoop\Entity\Output\OutputSettingsIssue;
use Newscoop\Entity\Output\OutputSettingsSection;
use Newscoop\Entity\Output\OutputSettingsTheme;
use Newscoop\Service\Implementation\AEntityBaseServiceDoctrine;

/**
 * Provides the services for Searching a template.
 */
class TemplateSearchServiceDoctrine extends AEntityBaseServiceDoctrine
        implements ITemplateSearchService
{
    /* --------------------------------------------------------------- */

    /** @var Doctrine\ORM\EntityManager */
    private $em = NULL;
    /** @var Newscoop\Service\IIssueService */
    private $issueService = NULL;
    /** @var Newscoop\Service\IOutputSettingSectionService */
    private $outputSettingSectionService = NULL;
    /** @var Newscoop\Service\IOutputSettingIssueService */
    private $outputSettingIssueService = NULL;

    /* --------------------------------------------------------------- */

    protected function _init_()
    {
        $this->entityClassName = OutputSettingsTheme::NAME;
    }

    /**
     * Provides the Output  setting service.
     *
     * @return Newscoop\Service\IOutputSettingSectionService
     * 		The output setting section service to be used by this controller.
     */
    public function getOutputSettingSectionService()
    {
        if ($this->outputSettingSectionService === NULL) {
            $this->outputSettingSectionService = $this->getResourceId()->getService(IOutputSettingSectionService::NAME);
        }
        return $this->outputSettingSectionService;
    }

    /**
     * Provides the Output setting issue service.
     *
     * @return IOutputSettingIssueService
     * 		The output setting issue service to be used by this controller.
     */
    public function getOutputSettingIssueService()
    {
        if ($this->outputSettingIssueService === NULL) {
            $this->outputSettingIssueService = $this->getResourceId()->getService(IOutputSettingIssueService::NAME);
        }
        return $this->outputSettingIssueService;
    }

    /**
     * Provides the Issue service.
     *
     * @return Newscoop\Service\IIssueService
     * 		The issue service to be used by this controller.
     */
    public function getIssueService()
    {
        if ($this->issueService === NULL) {
            $this->issueService = $this->getResourceId()->getService(IIssueService::NAME);
        }
        return $this->issueService;
    }

    public function getFrontPage($issue, $output)
    {
        /* @var $issue Issue */
        $issueId = $issue;
        if ($issue instanceof Issue) {
            $issueId = $issue->getId();
        } elseif (is_int($issue)) {
            $issue = $this->getIssueService()->getById($issueId);
        }
        $publicationId = $issue->getPublicationId();

        $outputId = $output;
        if ($output instanceof Output) {
            $outputId = $output->getId();
        }
        $em = $this->getEntityManager();

        $q = $em->createQueryBuilder();
        $q->select(array('osi'))
                ->from(OutputSettingsTheme::NAME, 'ost')
                ->from(OutputSettingsIssue::NAME_1,'osi')
                ->where('ost.themePath = osi.themePath')
                ->andWhere('ost.publication = :publication')
                ->andWhere('ost.output = :output')
                ->andWhere('osi.output = :output')
                ->andWhere('osi.issue = :issue')
                ->setParameter('output', $outputId)
                ->setParameter('issue', $issueId)
                ->setParameter('publication', $publicationId);
        $results = $q->getQuery()->getResult();
        each ($results as $result) {
            echo $result->getFrontPage()->getPath();
        }
    }

    /**
     * Get the page for error
     *      to be used as a template.
     *
     * @param Issue|Int $issue
     *      The issue object or the id of the issue for whom the template is needed.
     * @param Output|int|string $output
     *      The object Output, the id or the Name of the Output for whom the template is needed.
     *
     * @return string
     *      The full path of the template.
     */
    public function getErrorPage($issue, $output)
    {

    }

    /**
     * Get the page for section
     *      to be used as a template.
     *
     * @param Section|Int $section
     *      The section object or the id of the issue for whom the template is needed.
     * @param Output|int|string $output
     *      The object Output, the id or the Name of the Output for whom the template is needed.
     *
     *
     * @return string
     *      The full path of the template.
     */
    public function getSectionPage($section, $output)
    {

    }

    /**
     * Get the page for article
     *      to be used as a template.
     *
     * @param Section|Int $section
     *      The section object or the id of the issue for whom the template is needed.
     * @param Output|int|string $output
     *      The object Output, the id or the Name of the Output for whom the template is needed.
     *
     * @return string
     *      The full path of the template.
     */
    public function getArticlePage($section, $output)
    {

    }

}