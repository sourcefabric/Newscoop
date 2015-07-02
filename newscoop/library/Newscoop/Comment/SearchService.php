<?php
/**
 * @package   Newscoop
 * @author    Mischa Gorinskat <mischa.gorinskat@sourcefabric.org>
 * @copyright 2014 Sourcefabric o.p.s.
 * @license   http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Comment;

use Doctrine\ORM\EntityManager;
use Newscoop\Search\ServiceInterface;
use Newscoop\Search\DocumentInterface;
use Newscoop\Article\LinkService;

/**
 * Search Service
 */
class SearchService implements ServiceInterface
{
    /**
     * @var Newscoop\Article\LinkService
     */
    protected $articleLinkService;

    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @param Newscoop\Article\LinkService $articleLinkService
     * @param Doctrine\ORM\EntityManager $em
     */
    public function __construct(LinkService $articleLinkService, EntityManager $em)
    {
        $this->articleLinkService = $articleLinkService;
        $this->em = $em;
    }

    /**
     * Return type for this search service
     *
     * @return string identifier
     */
    public function getType()
    {
        return 'comment';
    }

    /**
     * Return type for this search service
     *
     * @return string identifier
     */
    public function getSubType(DocumentInterface $comment)
    {
        return 'comment';
    }

    /**
     * Test if comment is indexed
     *
     * @param Newscoop\Entity\Comment $comment
     * @return bool
     */
    public function isIndexed(DocumentInterface $comment)
    {
        return $comment->getIndexed() !== null;
    }

    /**
     * Test if comment can be indexed
     *
     * @param Newscoop\Entity\Comment $comment
     * @return bool
     */
    public function isIndexable(DocumentInterface $comment)
    {
        return $comment->getStatus() === 'approved';
    }

    /**
     * Get document for comment
     *
     * @param Newscoop\Entity\Comment $comment
     * @return array
     */
    public function getDocument(DocumentInterface $comment)
    {
        $articleNumber = $comment->getThread();
        $language = $comment->getLanguage();
        $article = $this->em->getRepository('Newscoop\Entity\Article')
            ->find(array('number' => $articleNumber, 'language' => $language->getId()));

        return array(
            'id' => $this->getDocumentId($comment),
            'number' => $comment->getId(),
            'type' => 'comment',
            'subject' => $comment->getSubject(),
            'message' => $comment->getMessage(),
            'published' => gmdate(self::DATE_FORMAT, $comment->getTimeCreated()->getTimestamp()),
            'link' => sprintf('%s#comment_%d', $this->articleLinkService->getLink($article), $comment->getId()),
        );
    }

    /**
     * Get document id
     *
     * @param Newscoop\Entity\Comment $comment
     * @return string
     */
    public function getDocumentId(DocumentInterface $comment)
    {
        return sprintf('%s-%d', $this->getType(), $comment->getId());
    }
}
