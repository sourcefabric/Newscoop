<?php
/**
 * @package Newscoop
 * @copyright 2014 Sourcefabric z.ú.
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */
namespace Newscoop\Services;

use Newscoop\IssueServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;
use Newscoop\Entity\Issue;

/**
 * Issue service
 */
class IssueService implements IssueServiceInterface
{
    /**
     * Entity Manager
     *
     * @var EntityManager
     */
    protected $em;

    /**
     * Issue meta data array
     *
     * @var array
     */
    protected $issueMetadata = array();

    /**
     * Publication service
     *
     * @var PublicationService
     */
    protected $publicationService;

    /**
     * Issue object
     *
     * @var Issue
     */
    protected $issue;

    /**
     * Cache Service
     *
     * @var CacheService
     */
    protected $cacheService;

    /**
     * Construct
     */
    public function __construct(EntityManager $em, PublicationService $publicationService, CacheService $cacheService)
    {
        $this->em = $em;
        $this->publicationService = $publicationService;
        $this->cacheService = $cacheService;
    }

    /**
     * {@inheritDoc}
     */
    public function getIssueMetadata()
    {
        return $this->issueMetadata;
    }

    /**
     * {@inheritDoc}
     */
    public function getIssue()
    {
        if (!$this->issue) {
            return $this->getLatestPublishedIssue();
        }

        return $this->issue;
    }

    /**
     * {@inheritDoc}
     */
    public function setIssue(Issue $issue)
    {
        $this->issue = $issue;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function issueResolver(Request $request)
    {
        $uriParts = explode('/', $request->getRequestUri());
        $uriPartsCount = count(array_filter($uriParts));
        $issue = null;
        $publication = $this->publicationService->getPublication();
        if ($publication && $uriPartsCount >= 2 && $uriPartsCount <= 5) {
            $cacheKey = $this->cacheService->getCacheKey(array(
                'resolver',
                $publication->getId(),
                $uriParts[1],
                $uriParts[2],
            ), 'issue');

            if ($this->cacheService->contains($cacheKey)) {
                $issue = $this->cacheService->fetch($cacheKey);
            } else {
                $issue = $this->em->getRepository('Newscoop\Entity\Issue')
                    ->getIssue($uriParts[1], $publication, $uriParts[2])
                    ->getOneOrNullResult();

                $this->cacheService->save($cacheKey, $issue);
            }

            if ($issue) {
                $this->issueMetadata = array(
                    'id' => $issue->getId(),
                    'number' => $issue->getNumber(),
                    'name' => $issue->getName(),
                    'shortName' => $issue->getShortName(),
                    'code_default_language' => $issue->getLanguage()->getCode(),
                    'id_default_language' => $issue->getLanguageId(),
                );

                $request->attributes->set('_newscoop_issue_metadata', $this->issueMetadata);
                $this->setIssue($issue);

                return $issue;
            }
        }

        // TODO: Check if we should add locale from request here
        return $this->getLatestPublishedIssue($request->getLocale());
    }

    /**
     * {@inheritDoc}
     */
    public function getLatestPublishedIssue($languageCode = null)
    {
        $publication = $this->publicationService->getPublication();
        if (!$publication) {
            return;
        }

        $language = $this->em->getRepository('Newscoop\Entity\Language')
            ->findOneByCode($languageCode);
        if (!($language instanceof \Newscoop\Entity\Language)) {
            $language = $publication->getDefaultLanguage();
        }

        $publicationId = $publication->getId();
        $languageId = $language->getId();
        $cacheKey = $this->cacheService->getCacheKey(array(
            'latest_published',
            $publicationId,
            $languageId,
        ), 'issue');

        if ($this->cacheService->contains($cacheKey)) {
            $issue = $this->cacheService->fetch($cacheKey);
        } else {
            try {
                $issue = $this->em
                    ->getRepository('Newscoop\Entity\Issue')
                    ->getLastPublishedByPublicationAndLanguage($publicationId, $languageId)
                    ->getSingleResult();
            } catch(\Doctrine\ORM\NoResultException $e) {
                return;
            }

            $this->cacheService->save($cacheKey, $issue);
        }

        if (!$issue) {
            return;
        }

        $this->setIssue($issue);

        return $issue;
    }
}
