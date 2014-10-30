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
    public function issueResolver(Request $request)
    {
        $uriParts = explode('/', $request->getRequestUri());
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

            return $issue;
        }
    }
}
