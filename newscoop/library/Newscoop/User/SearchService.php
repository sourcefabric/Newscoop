<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\User;

use Newscoop\Search\ServiceInterface;
use Newscoop\Search\DocumentInterface;
use Newscoop\Image\ImageService;
use Newscoop\Entity\User;
use Newscoop\Entity\UserAttribute;

/**
 * Search Service
 */
class SearchService implements ServiceInterface
{
    /**
     * @var Newscoop\Image\ImageService
     */
    protected $imageService;

    /**
     * @param ImageService $imageService
     */
    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Return type for this search service
     *
     * @return string identifier
     */
    public function getType()
    {
        return 'user';
    }

    /**
     * Test if user is indexed
     *
     * @param DocumentInterface $user
     * @return bool
     */
    public function isIndexed(DocumentInterface $user)
    {
        return $user->getIndexed() !== null;
    }

    /**
     * Test if user can be indexed
     *
     * @param DocumentInterface $user
     * @return bool
     */
    public function isIndexable(DocumentInterface $user)
    {
        return $user->isPublic() && $user->isActive();
    }

    /**
     * Get document representation for user
     *
     * @param DocumentInterface $user
     * @return array
     */
    public function getDocument(DocumentInterface $user)
    {
        return array(
            'id' => $this->getDocumentId($user),
            'type' => 'user',
            'user' => $user->getUsername(),
            'bio' => $user->getAttribute('bio'),
            'image' => $this->imageService->getUserImage($user) ?: '',
            'published' => gmdate(self::DATE_FORMAT, $user->getCreated()->getTimestamp()),
            'is_verified' => (bool) $user->getAttribute(UserAttribute::IS_VERIFIED),
        );
    }

    /**
     * Get document id
     *
     * @param DocumentInterface $user
     * @return string
     */
    public function getDocumentId(DocumentInterface $user)
    {
        return sprintf('%s-%d', $this->getType(), $user->getId());
    }
}
