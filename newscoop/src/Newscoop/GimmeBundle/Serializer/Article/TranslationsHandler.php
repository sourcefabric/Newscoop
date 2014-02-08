<?php
/**
 * @package Newscoop\Gimme
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\GimmeBundle\Serializer\Article;

use JMS\Serializer\JsonSerializationVisitor;

/**
 * Create Article translations array.
 */
class TranslationsHandler
{
    private $em;
    private $router;

    public function __construct($em, $router)
    {
        $this->em = $em;
        $this->router = $router;
    }

    public function serializeToJson(JsonSerializationVisitor $visitor, $data, $type)
    {
        $articleTranslations = $this->em->getRepository('Newscoop\Entity\Article')
            ->getArticleTranslations($data->number, $data->languageId)
            ->getResult();

        if (count($articleTranslations) == 0) {
            return null;
        }

        $articleTranslationsArray = array();
        foreach ($articleTranslations as $article) {
            $articleTranslationsArray[$article->getLanguageCode()] = $this->router->generate('newscoop_gimme_articles_getarticle', array('number' => $article->getNumber(), 'language' => $article->getLanguageCode()), true);
        }

        return $articleTranslationsArray;
    }
}
