<?php
/**
 * @package Newscoop\Gimme
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\GimmeBundle\Serializer\Topic;  

use JMS\SerializerBundle\Serializer\VisitorInterface;
use JMS\SerializerBundle\Serializer\Handler\SerializationHandlerInterface;

/**
 * Create uri for items resource.
 */
class ArticlesLinkHandler implements SerializationHandlerInterface
{
    private $router;
    private $em;

    public function __construct($em, $router)
    {
        $this->router = $router;
        $this->em = $em;
    }

    public function serialize(VisitorInterface $visitor, $data, $type, &$visited)
    {   
        if ($type != 'Newscoop\\Entity\\Topic') {
            return;
        }

        $language = $this->em->getRepository('Newscoop\Entity\Language')
                ->findOneById($data->getLanguageId());

        $uri = $this->router->generate('newscoop_gimme_topics_gettopicsarticles', array(
            'id' => $data->getTopicId(),
            'language' => $language->getCode()
        ), true);

        $data->setArticlesLink($uri);
    }
}