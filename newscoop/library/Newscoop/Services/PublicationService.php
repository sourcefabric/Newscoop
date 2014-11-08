<?php
/**
 * @package Newscoop\NewscoopBundle
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Doctrine\ORM\EntityManager;
use Newscoop\Entity\Publication;
use Newscoop\Entity\Aliases;
use Symfony\Component\HttpFoundation\Request;

/**
 * Manage requested publication
 */
class PublicationService
{
    /**
     * Entity Manager
     * @var EntityManager
     */
    protected $em;

    /**
     * Publication object
     * @var Publication
     */
    protected $publication;

    /**
     * Alias object
     * @var Aliases
     */
    protected $publicationAlias;

    /**
     * Publication metadata.
     * @var array
     */
    protected $publicationMetadata = array();

    /**
     * Cache Service
     * @var CacheService
     */
    protected $cacheService;

    /**
     * Construct Publication Service
     * @param EntityManager $em           Entity Manager
     * @param CacheService  $cacheService Cache Service
     */
    public function __construct(EntityManager $em, CacheService $cacheService)
    {
        $this->em = $em;
        $this->cacheService = $cacheService;
    }

    /**
     * Get Publication object
     * @return Publication Publication entity object
     */
    public function getPublication()
    {
        return $this->publication;
    }

    /**
     * Get used Aliases object - connected with choosen publication
     * @return Aliases Aliases entity object
     */
    public function getPublicationAlias()
    {
        return $this->publicationAlias;
    }

    /**
     * Set used Aliases object - connected with choosen publication
     * @param Aliases $alias Aliases entity object
     */
    public function setPublicationAlias(Aliases $alias)
    {
        $this->publicationAlias = $alias;

        return $this;
    }

    /**
     * Set Publication object
     * @param Publication $publication Publication entity object
     */
    public function setPublication(Publication $publication)
    {
        $this->publication = $publication;

        return $this;
    }

    /**
     * Get publication metadata
     * @return array publication metadata
     */
    public function getPublicationMetadata()
    {
        return $this->publicationMetadata;
    }

    /**
     * Resolve publication from provided data
     *
     * @param Request $request Request object
     *
     * @return Publication $publication Publication entity object
     */
    public function publicationResolver(Request $request)
    {
        /**
         * By default try get publication alias from requested http_host
         */
        $publication = $request->server->get('HTTP_HOST');
        $this->publicationMetadata['source'] = 'request_http_host';

        /**
         * If in request GET or POST params exists 'publication' use it.
         */
        if ($request->query->has('__publication_alias_name') || $request->request->has('__publication_alias_name')) {
            $publication = $request->get('__publication_alias_name');
            $this->publicationMetadata['source'] = 'optional_parameter';
        }

        $cacheKey = $this->cacheService->getCacheKey(array(
            'resolver',
            $publication,
            $this->publicationMetadata['source']
        ), 'publication');

        $alias = array();
        if ($this->cacheService->contains($cacheKey)) {
            $alias = $this->cacheService->fetch($cacheKey);
        } else {
            $qb = $this->em->getRepository('Newscoop\Entity\Aliases')
                ->createQueryBuilder('a');

            $qb->select('a.id as aliasId', 'p.id as publicationId', 'p.name as publicationName', 'l.id as languageId')
                ->leftJoin('a.publication', 'p')
                ->leftJoin('p.language', 'l')
                ->where('a.name = :name')
                ->setParameter('name', $publication);

            $alias = $qb->getQuery()->getArrayResult();
            $this->cacheService->save($cacheKey, $alias);
        }

        if (empty($alias)) {
            return null;
        }

        $this->publicationMetadata['alias'] = array(
            'name' => $alias[0]['publicationName'],
            'publication_id' => $alias[0]['publicationId']
        );

        $this->publicationMetadata['publication'] = array(
            'name' => $alias[0]['publicationName'],
            'id_default_language' => $alias[0]['languageId']
        );

        /**
         * Save publication metadata to into Request attributes.
         */
        $request->attributes->set('_newscoop_publication_metadata', $this->publicationMetadata);
        $aliasObject = $this->em->getReference('Newscoop\Entity\Aliases', $alias[0]['aliasId']);
        $publicationObject = $this->em->getReference('Newscoop\Entity\Publication', $alias[0]['publicationId']);
        $this->setPublicationAlias($aliasObject);
        $this->setPublication($publicationObject);

        return $publicationObject;
    }
}
