<?php
/**
 * @package Newscoop
 * @copyright 2014 Sourcefabric z.ú.
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Newscoop\ThemesServiceInterface;
use Newscoop\IssueServiceInterface;
use Newscoop\Entity\Issue;
use Doctrine\ORM\EntityManager;
use Newscoop\Entity\Output;

/**
 * Themes service
 */
class ThemesService implements ThemesServiceInterface
{
    /**
     * Issue service
     *
     * @var IssueServiceInterface
     */
    protected $issueService;

    /**
     * Cache service
     *
     * @var CacheService
     */
    protected $cacheService;

    /**
     * Publication service
     *
     * @var PublicationService
     */
    protected $publicationService;

    /**
     * Entity Manager
     *
     * @var EntityManager
     */
    protected $em;

    /**
     * Construct
     */
    public function __construct(
        IssueServiceInterface $issueService,
        CacheService $cacheService,
        PublicationService $publicationService,
        EntityManager $em
    )
    {
        $this->issueService = $issueService;
        $this->cacheService = $cacheService;
        $this->publicationService = $publicationService;
        $this->em = $em;
    }

    /**
     * {@inheritDoc}
     */
    public function getThemePath()
    {
        $issue = $this->issueService->getIssue();
        if (!$issue) {
            return;
        }

        $languageId = $issue->getLanguageId();
        $publication = $this->publicationService->getPublication();
        $cacheKeyThemePath = $this->cacheService->getCacheKey(array('getThemePath', $languageId, $publication->getId(), $issue->getNumber()), 'issue');

        $themePath = null;
        $webOutput = null;
        $outSetIssues = null;
        if ($this->cacheService->contains($cacheKeyThemePath)) {
            $themePath = $this->cacheService->fetch($cacheKeyThemePath);
        } else {
            $cacheKeyWebOutput = $this->cacheService->getCacheKey(array('OutputService', 'Web'), 'outputservice');
            if ($this->cacheService->contains($cacheKeyWebOutput)) {
                $webOutput = $this->cacheService->fetch($cacheKeyWebOutput);
            } else {
                $webOutput = $this->findByName('Web');
                $this->cacheService->save($cacheKeyWebOutput, $webOutput);
            }

            $cacheKeyOutSetIssues = $this->cacheService->getCacheKey(array('outSetIssues', $issue->getId(), 'webOutput'));
            if ($this->cacheService->contains($cacheKeyOutSetIssues)) {
                $outSetIssues = $this->cacheService->fetch($cacheKeyOutSetIssues);
            } else {
                $outSetIssues = $this->findByIssueAndOutput($issue->getId(), $webOutput);
                $this->cacheService->save($cacheKeyOutSetIssues, $outSetIssues);
            }

            if (!is_null($outSetIssues)) {
                $themePath = $outSetIssues->getThemePath()->getPath();
            }

            $this->cacheService->save($cacheKeyThemePath, $themePath);

        }

       return $themePath;
    }

    /**
     * Finds output by name
     *
     * @param string $name Output name ('Web' in this case)
     *
     * @return string|null
     * @throws Exception   when wrong parameter supplied
     */
    public function findByName($name)
    {
        if (is_null($name)) {
            throw new \Exception("Please provide a value for the parameter 'name'");
        } elseif (is_string($name) && trim($name) == '') {
            throw new \Exception("Please provide a none empty value for the parameter 'name'.");
        }

        $outputs = $this->em->getRepository('Newscoop\Entity\Output')->findBy(array('name' => $name));
        if (isset($outputs) && count($outputs) > 0) {
            return $outputs[0];
        }

        return null;
    }

    /**
     * Finds output for issue by issue and output
     *
     * @param Issue  $issue  Issue object
     * @param Output $output Output object
     *
     * @return string|null
     */
    public function findByIssueAndOutput($issue, $output)
    {
        $outputId = $output;
        if ($output instanceof Output) {
            $outputId = $output->getId();
        }

        $issueId = $issue;
        if ($issue instanceof Issue) {
            $issueId = $issue->getId();
        }

        $resources = $this->em->getRepository('Newscoop\Entity\Output\OutputSettingsIssue')->findBy(array(
            'issue' => $issueId,
            'output' => $outputId
        ));

        if (!empty($resources)) {
            return $resources[0];
        }

        return null;
    }
}
