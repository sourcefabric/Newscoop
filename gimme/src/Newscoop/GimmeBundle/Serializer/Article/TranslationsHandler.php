<?php
/**
 * @package Newscoop\Gimme
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\GimmeBundle\Serializer\Article;  

use JMS\SerializerBundle\Serializer\VisitorInterface;
use JMS\SerializerBundle\Serializer\Handler\SerializationHandlerInterface;

/**
 * Create Article translations array.
 */
class TranslationsHandler implements SerializationHandlerInterface
{
    private $em;
    private $router;

    public function __construct($em, $router)
    {
        $this->em = $em;
        $this->router = $router;
    }

    public function serialize(VisitorInterface $visitor, $data, $type, &$visited)
    {   
        if ($type != 'Newscoop\\Entity\\Article') {
            return;
        }

        $articleTranslations = $this->em->getRepository('Newscoop\Entity\Article')
            ->getArticleTranslations($data->getNumber(), $data->getLanguageId())
            ->getResult();

        $articleTranslationsArray = array();
        foreach ($articleTranslations as $article) {
            $articleTranslationsArray[$article->getLanguageCode()] = $this->router->generate('newscoop_gimme_articles_getarticle', array('number' => $article->getNumber(), 'language' => $article->getLanguageCode()), true);
        }

        $data->setTranslations($articleTranslationsArray);
    }
}