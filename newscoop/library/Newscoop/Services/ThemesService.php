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
    public function getThemePath($language = null)
    {
        $publicationMetadata = $this->publicationService->getPublicationMetadata();
        $frontpage = $this->isHomepage($publicationMetadata['request']['uri'], true);
        $languageId = null;
        $issue = $this->issueService->getIssue();
        if (!$issue) {
            return;
        }

        if (!($language instanceof \Newscoop\Entity\Language)) {
            $languageCode = $language;
            $language = $this->em->getRepository('Newscoop\Entity\Language')
                ->findOneByCode($language);
        }
        
        if (!is_null($languageId)) {
            $languageId = $language->getId();
        } else if (is_null($languageId)) {
            $languageId = $issue->getLanguageId();
        }
        $publication = $this->publicationService->getPublication();
        $cacheKeyThemePath = $this->cacheService->getCacheKey(array('getThemePath', $frontpage, $languageId, $publication->getId(), $issue->getNumber()), 'issue');

        $themePath = null;
        if ($this->cacheService->contains($cacheKeyThemePath)) {
            $themePath = $this->cacheService->fetch($cacheKeyThemePath);
        } else {
            $webOutput = null;
            $outSetIssues = null;
            $cacheKeyWebOutput = $this->cacheService->getCacheKey(array('OutputService', 'Web'), 'outputservice');
            if ($this->cacheService->contains($cacheKeyWebOutput)) {
                $webOutput = $this->cacheService->fetch($cacheKeyWebOutput);
            } else {
                $webOutput = $this->findByName('Web');
                $this->cacheService->save($cacheKeyWebOutput, $webOutput);
            }

            if ($frontpage) {
                $outSetPublication = $this->findByPublicationAndLanguageAndOuput($publication, $languageId, $webOutput);
                if (!is_null($outSetPublication)) {
                    $themePath = $outSetPublication->getThemePath()->getPath();
                }
            }
            if (!$frontpage || ($frontpage && $themePath === null)) {
                $outSetIssues = $this->findByIssueAndOutput($issue->getId(), $webOutput);
                if (!is_null($outSetIssues)) {
                    $themePath = $outSetIssues->getThemePath()->getPath();
                }
            }

            $this->cacheService->save($cacheKeyThemePath, $themePath);
        }

        return $themePath;
    }

    /**
     * Returns whether the url is the home page or not. Support for
     * language homepages (/cz, /en, etc.) is also built in via a parameters.
     *
     * @param  boolean $withLanguageHomepage Whether to include language homepages
     *                                       in the resulsts.
     *
     * @return boolean
     */
    public function isHomepage($path, $withLanguageHomepage = false)
    {
        $matches = array();
        if ($path === '/') {
            return true;
        } elseif ($withLanguageHomepage && preg_match('@^/[a-z]{2}(?:-[A-Z]{2})?$@', $path, $matches)) {
            try {
                $language = $this->em->getRepository('Newscoop\Entity\Language')->findOneBy(array('code' => ltrim($matches[0], '/')));
                if ($language) {
                    return true;
                }
            } catch (\Exception $e) {
                return false;
            }
        }

        return false;
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

    /**
     * Finds output for publication by publication, language and output.
     *
     * @param  Newscoop\Entity\Publication $publication
     * @param  Newscoop\Entity\Language    $language
     * @param  Newscoop\Entity\Output      $ouput
     *
     * @return Newscoop\Entity\Resource|null
     */
    public function findByPublicationAndLanguageAndOuput(
        $publication,
        $language,
        $output
    ) {

        $outputId = $output;
        if ($output instanceof Output) {
            $outputId = $output->getId();
        }

        $languageId = $language;
        if ($language instanceof Language) {
            $languageId = $language->getId();
        }

        $publicationId = $publication;
        if ($publication instanceof Publication) {
            $publicationId = $publication->getId();
        }

        $resources = $this->em
            ->getRepository('Newscoop\Entity\Output\OutputSettingsPublication')
            ->findBy(array(
                'output' => $outputId,
                'publication' => $publicationId,
                'language' => $languageId,
            )
        );

        if (!empty($resources)) {
            return $resources[0];
        }

        return null;
    }
}
