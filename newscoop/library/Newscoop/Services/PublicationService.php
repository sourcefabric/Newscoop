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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Manage requested publication
 */
class PublicationService
{
    /**
     * Entity Manager
     * @var EntityManager
     */
    private $em;

    /**
     * Publication object
     * @var Publication
     */
    private $publication;

    /**
     * Alias object
     * @var Aliases
     */
    private $publicationAlias;

    /**
     * Publication metadata.
     * @var array
     */
    private $publicationMetadata = array();

    /**
     * Construct Publication Service
     * @param EntityManager $em Entity Manager
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
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
     * @param  Request $request Request object
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

        $alias = $this->em->getRepository('Newscoop\Entity\Aliases')
            ->findOneByName($publication);

        if (!$alias) {
            throw new NotFoundHttpException('Requested publication was not found.');
        }

        if (!$alias->getPublication()) {
            throw new NotFoundHttpException('Requested publication was not found.');
        }

        $this->publicationMetadata['alias'] = array(
            'name' => $alias->getName(),
            'publication_id' => $alias->getPublication()->getId()
        );

        $this->publicationMetadata['publication'] = array(
            'name' => $alias->getPublication()->getName(),
            'id_default_language' => $alias->getPublication()->getLanguage()->getId()
        );

        /**
         * Save publication metadata to into Request attributes.
         */
        $request->attributes->set('_newscoop_publication_metadata', $this->publicationMetadata);

        $this->setPublicationAlias($alias);
        $this->setPublication($alias->getPublication());

        return $publication;
    }
}