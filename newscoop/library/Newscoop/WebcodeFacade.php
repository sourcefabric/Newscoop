<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop;

use Doctrine\ORM\EntityManager,
    Newscoop\Entity;

/**
 * Webcode facade
 */
class WebcodeFacade
{
    /**
     * @Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @Newscoop\Random
     */
    protected $random;

    /**
     * @param Doctrine\ORM\EntityManager $em
     * @param Newscoop\Random $random
     */
    public function __construct(EntityManager $em, Random $random)
    {
        $this->em = $em;
        $this->random = $random;
    }

    /**
     * Set article webcode
     *
     * @param Newscoop\Entity\Article $article
     * @param string $webcode
     * @return void
     */
    public function setArticleWebcode(Entity\Article $article, $webcode = null)
    {
        if (empty($webcode)) {
            $webcode = $this->generateWebcode();
        } else if (!$this->isUnique($webcode)) {
            throw new \InvalidArgumentException("Webcode '$webcode' is in use.");
        }

        $webcode = new Entity\Webcode($webcode, $article);
        $article->setWebcode($webcode);
        $this->em->persist($webcode);
        $this->em->flush();
    }

    /**
     * Get article webcode
     *
     * @param Newscoop\Entity\Article $article
     * @return string
     */
    public function getArticleWebcode(Entity\Article $article)
    {
        if (!$article->hasWebcode()) {
            $this->setArticleWebcode($article);
        }

        return $article->getWebcode();
    }

    /**
     * Find article by webcode
     *
     * @param string $webcode
     * @return Newscoop\Entity\Article
     */
    public function findArticleByWebcode($webcode)
    {
        $webcode = $this->getRepository()->findOneBy(array(
            'webcode' => (string) $webcode,
        ));

        return $webcode !== null ? $webcode->getArticle() : null;
    }

    /**
     * Generate webcode
     *
     * return string
     */
    private function generateWebcode()
    {
        for ($length = 5; $length < 10; $length++) {
            for ($i = 0; $i < 10; $i++) {
                return $this->random->getRandomString(5);
            }
        }
    }

    /**
     * Test if webcode is unique
     *
     * @param string $webcode
     * @return bool
     */
    private function isUnique($webcode)
    {
        return !$this->getRepository()->find($webcode);
    }

    /**
     * Get webcode repository
     *
     * @return Doctrine\ORM\EntityRepository
     */
    private function getRepository()
    {
        return $this->em->getRepository('Newscoop\Entity\Webcode');
    }
}
