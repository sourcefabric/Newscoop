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
use Newscoop\Entity\Resource;
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
    /** @var Newscoop\Service\ISectionService */
    private $sectionService = NULL;
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

    /**
     * Provides the Section service.
     *
     * @return Newscoop\Service\ISectionService
     * 		The section service to be used by this controller.
     */
    public function getSectionService()
    {
        if ($this->sectionService === NULL) {
            $this->sectionService = $this->getResourceId()->getService(ISectionService::NAME);
        }
        return $this->sectionService;
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
        $q->select(array('oi', 'ot'))
                ->from(OutputSettingsTheme::NAME, 'ot')
                ->from(OutputSettingsIssue::NAME_1, 'oi')
                ->where('ot.themePath = oi.themePath')
                ->andWhere('ot.publication = :publication')
                ->andWhere('ot.output = :output')
                ->andWhere('oi.output = :output')
                ->andWhere('oi.issue = :issue')
                ->setParameter('output', $outputId)
                ->setParameter('issue', $issueId)
                ->setParameter('publication', $publicationId);
        $results = $q->getQuery()->getResult();
        if (count($results) < 2)
            return '';

        /* @var $outputSettingTheme OutputSettingsTheme */
        list(, $outputSettingTheme) = each($results);
        /* @var $outputSettingIssue OutputSettingsIssue */
        list(, $outputSettingIssue) = each($results);

        if (!is_null($resource = $outputSettingIssue->getFrontPage()))
            return $this->getResourceFullPath($resource);
        return $this->getResourceFullPath($outputSettingTheme->getFrontPage());
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
        $q->select(array('oi', 'ot'))
                ->from(OutputSettingsTheme::NAME, 'ot')
                ->from(OutputSettingsIssue::NAME_1, 'oi')
                ->where('ot.themePath = oi.themePath')
                ->andWhere('ot.publication = :publication')
                ->andWhere('ot.output = :output')
                ->andWhere('oi.output = :output')
                ->andWhere('oi.issue = :issue')
                ->setParameter('output', $outputId)
                ->setParameter('issue', $issueId)
                ->setParameter('publication', $publicationId);
        $results = $q->getQuery()->getResult();
        if (count($results) < 2)
            return '';

        /* @var $outputSettingTheme OutputSettingsTheme */
        list(, $outputSettingTheme) = each($results);
        /* @var $outputSettingIssue OutputSettingsIssue */
        list(, $outputSettingIssue) = each($results);

        if (!is_null($resource = $outputSettingIssue->getErrorPage())) {
            return $this->getResourceFullPath($resource);
        }
        return $this->getResourceFullPath($outputSettingTheme->getErrorPage());
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

        if (!($outoput instanceof Output)) {
            if (is_int($output)) {
                $output = $this->getOutputService()->findById($output);
            } else {
                $output = $this->getOutputService()->findByName($output);
            }
        }

        /* @var $section Section */
        if (!($section instanceof Section)) {
            $section = $this->getSectionService()->findById($section);
        }

        /* @var $outputSettingSection OutputSettingsSection */
        $outputSettingSection = $this->getOutputSettingSectionService()->findBySectionAndOutput($section,
                        $outoput);

        if (!is_null($resource = $outputSettingSection->getSectionPage())) {
            return $this->getResourceFullPath($resource);
        }

        /* @var $issue Issue */
        $issue = $section->getIssue();

        $em = $this->getEntityManager();
        //


        $publicationId = $issue->getPublicationId();
        $issueId = $issue->getId();
        $outputId = $output->getId();


        $q = $em->createQueryBuilder();
        $q->select(array('oi', 'ot'))
                ->from(OutputSettingsTheme::NAME, 'ot')
                ->from(OutputSettingsIssue::NAME_1, 'oi')
                ->where('ot.themePath = oi.themePath')
                ->andWhere('ot.publication = :publication')
                ->andWhere('ot.publication = :publication')
                ->andWhere('ot.output = :output')
                ->andWhere('oi.output = :output')
                ->andWhere('oi.issue = :issue')
                ->setParameter('output', $outputId)
                ->setParameter('issue', $issueId)
                ->setParameter('publication', $publicationId);
        $results = $q->getQuery()->getResult();
        if (count($results) < 2)
            return '';

        /* @var $outputSettingTheme OutputSettingsTheme */
        list(, $outputSettingTheme) = each($results);
        /* @var $outputSettingIssue OutputSettingsIssue */
        list(, $outputSettingIssue) = each($results);

        if (!is_null($resource = $outputSettingIssue->getSectionPage())) {
            return $this->getResourceFullPath($resource);
        }
        return $this->getResourceFullPath($outputSettingTheme->getSectionPage());
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
        if (!($outoput instanceof Output)) {
            if (is_int($output)) {
                $output = $this->getOutputService()->findById($output);
            } else {
                $output = $this->getOutputService()->findByName($output);
            }
        }

        /* @var $section Section */
        if (!($section instanceof Section)) {
            $section = $this->getSectionService()->findById($section);
        }

        /* @var $outputSettingSection OutputSettingsSection */
        $outputSettingSection = $this->getOutputSettingSectionService()->findBySectionAndOutput($section,
                        $outoput);

        if (!is_null($resource = $outputSettingSection->getArticlePage())) {
            return $this->getResourceFullPath($resource);
        }

        /* @var $issue Issue */
        $issue = $section->getIssue();

        $em = $this->getEntityManager();
        //


        $publicationId = $issue->getPublicationId();
        $issueId = $issue->getId();
        $outputId = $output->getId();


        $q = $em->createQueryBuilder();
        $q->select(array('oi', 'ot'))
                ->from(OutputSettingsTheme::NAME, 'ot')
                ->from(OutputSettingsIssue::NAME_1, 'oi')
                ->where('ot.themePath = oi.themePath')
                ->andWhere('ot.publication = :publication')
                ->andWhere('ot.publication = :publication')
                ->andWhere('ot.output = :output')
                ->andWhere('oi.output = :output')
                ->andWhere('oi.issue = :issue')
                ->setParameter('output', $outputId)
                ->setParameter('issue', $issueId)
                ->setParameter('publication', $publicationId);
        $results = $q->getQuery()->getResult();
        if (count($results) < 2)
            return '';

        /* @var $outputSettingTheme OutputSettingsTheme */
        list(, $outputSettingTheme) = each($results);
        /* @var $outputSettingIssue OutputSettingsIssue */
        list(, $outputSettingIssue) = each($results);

        if (!is_null($resource = $outputSettingIssue->getArticlePage())) {
            return $this->getResourceFullPath($resource);
        }
        return $this->getResourceFullPath($outputSettingTheme->getArticlePage());
    }

    /**
     * Internal method, get the full path from an resource.
     *
     * @param Resource $resource
     * @return string
     */
    protected function getResourceFullPath(Resource $resource)
    {
        return $resource->getPath();
    }

}