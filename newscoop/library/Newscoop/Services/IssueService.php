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
     * Construct
     */
    public function __construct(EntityManager $em, PublicationService $publicationService)
    {
        $this->em = $em;
        $this->publicationService = $publicationService;
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
        if ($uriPartsCount >= 2 && $uriPartsCount <= 5) {
            $publication = $this->publicationService->getPublication();
            $issue = $this->em->getRepository('Newscoop\Entity\Issue')->findOneBy(array(
                'publication' => $publication,
                'shortName' => $uriParts[2]
            ));

            if ($issue) {
                $this->issueMetadata = array(
                    'id' => $issue->getId(),
                    'number' => $issue->getNumber(),
                    'name' => $issue->getName(),
                    'shortName' => $issue->getShortName()
                );

                $request->attributes->set('_newscoop_issue_metadata', $this->issueMetadata);
                $this->setIssue($issue);

                return $issue;
            }
        }

        return $this->getLatestPublishedIssue();
    }

    /**
     * {@inheritDoc}
     */
    public function getLatestPublishedIssue()
    {
        $issues = $this->publicationService->getPublication()->getIssues()->toArray();
        usort($issues, function ($x, $y) {
            return $y->getId() - $x->getId();
        });

        $latestPublished = false;
        $latestPublishedIssue = null;
        foreach ($issues as $key => $issue) {
            if ($issue->getWorkflowStatus() === 'Y' && !$latestPublished) {
                $latestPublishedIssue = $issue;
                $latestPublished = true;
            }
        }

        if (null !== $latestPublishedIssue) {
            $this->setIssue($latestPublishedIssue);
        }

        return $latestPublishedIssue;
    }
}
