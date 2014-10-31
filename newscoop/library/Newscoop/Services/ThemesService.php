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
     * Construct
     */
    public function __construct(IssueServiceInterface $issueService, CacheService $cacheService, PublicationService $publicationService)
    {
        $this->issueService = $issueService;
        $this->cacheService = $cacheService;
        $this->publicationService = $publicationService;
    }

    /**
     * {@inheritDoc}
     */
    public function getThemePath()
    {
       $issue = $this->issueService->getIssue();
       $language = $issue->getLanguageId();
       $publication = $this->publicationService->getPublication();
       $cacheKeyThemePath = $this->cacheService->getCacheKey(array('getThemePath', $language, $publication->getId(), $issue->getNumber()), 'issue');

       if ($this->cacheService->contains($cacheKeyThemePath)) {
            $themePath = $this->cacheService->fetch($cacheKeyThemePath);
        } else {
            $cacheKey = $this->cacheService->getCacheKey(array('issue', $publication->getId(), $language, $issue->getNumber()), 'issue');
            if ($this->cacheService->contains($cacheKey)) {
                $issue = $this->cacheService->fetch($cacheKey);
            } else {
                $this->cacheService->save($cacheKey, $issue);
            }

            //$resourceId = new ResourceId('template_engine/classes/CampSystem');
            //$outputService = $resourceId->getService(IOutputService::NAME);
        }

       return '';
    }
}
