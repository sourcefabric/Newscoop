<?php
/**
 * @package   Newscoop
 * @author    Mischa Gorinskat <mischa.gorinskat@sourcefabric.org>
 * @copyright 2014 Sourcefabric o.p.s.
 * @license   http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Comment;

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
     * @param LinkService $articleLinkService
     */
    public function __construct(LinkService $articleLinkService)
    {
        $this->articleLinkService = $articleLinkService;
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
     * Test if comment is indexed
     *
     * @param DocumentInterface $comment
     * @return bool
     */
    public function isIndexed(DocumentInterface $comment)
    {
        return $comment->getIndexed() !== null;
    }

    /**
     * Test if comment can be indexed
     *
     * @param DocumentInterface $comment
     * @return bool
     */
    public function isIndexable(DocumentInterface $comment)
    {
        return $comment->getStatus() === 'approved';
    }

    /**
     * Get document for comment
     *
     * @param DocumentInterface $comment
     * @return array
     */
    public function getDocument(DocumentInterface $comment)
    {
        return array(
            'id' => $this->getDocumentId($comment),
            'type' => 'comment',
            'subject' => $comment->getSubject(),
            'message' => $comment->getMessage(),
            'published' => gmdate(self::DATE_FORMAT, $comment->getTimeCreated()->getTimestamp()),
            'link' => sprintf('%s#comment_%d', $this->articleLinkService->getLink($comment->getArticle()), $comment->getId()),
        );
    }

    /**
     * Get document id
     *
     * @param DocumentInterface $comment
     * @return string
     */
    public function getDocumentId(DocumentInterface $comment)
    {
        return sprintf('%s-%d', $this->getType(), $comment->getId());
    }
}
