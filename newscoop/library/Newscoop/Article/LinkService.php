<?php
/**
 * @package   Newscoop
 * @author    Mischa Gorinskat <mischa.gorinskat@sourcefabric.org>
 * @copyright 2014 Sourcefabric o.p.s.
 * @license   http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Article;

use Doctrine\ORM\EntityManager;
use Newscoop\Router\RouterFactory;
use Newscoop\Entity\Article;

/**
 * Link Service
 */
class LinkService
{
    /** @var array */
    static $urlMap = array(
        'ä' => 'ae',
        'Ä' => 'ae',
        'á' => 'a',
        'à' => 'a',
        'â' => 'a',
        'æ' => 'a',
        'é' => 'e',
        'é' => 'e',
        'è' => 'e',
        'è' => 'e',
        'ü' => 'ue',
        'Ü' => 'ue',
        'ö' => 'oe',
        'Ö' => 'oe',
        'ß' => 'ss',
        'ç' => 'c',
        'ê' => 'e',
        'ê' => 'e',
        'ì' => 'i',
        'ì' => 'i',
        'í' => 'i',
        'í' => 'i',
        'ô' => 'o',
        'ô' => 'o',
        'œ' => 'o',
        'ò' => 'o',
        'ò' => 'o',
        'ó' => 'o',
        'ó' => 'o',
        'ù' => 'u',
        'ù' => 'u',
        'û' => 'u',
        'û' => 'u',
        'ú' => 'u',
        'ú' => 'u',
        'ÿ' => 'y',
        'Ÿ' => 'y',
    );

    /**
     * @var array
     */
    protected $sectionTypes = array(
        'bloginfo',
    );

    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var Newscoop\Router
     */
    protected $router;

    /**
     * @param Doctrine\ORM\EntityManager $em
     * @param Newscoop\Router\RouterFactory $router
     */
    public function __construct(EntityManager $em, \Zend_Controller_Router_Rewrite $router)
    {
        $this->em = $em;
        $this->router = $router;
    }

    /**
     * Get link
     *
     * @param Newscoop\Entity\Article $article
     * @return string
     */
    public function getLink(Article $article)
    {
        $link = array(
            $this->getPublicationAliasName($article),
            ($article->getLanguage()) ? $article->getLanguage()->getCode() : null,
            $this->getIssueShortName($article),
            $this->getSectionShortName($article),
        );

        if (!in_array($article->getType(), $this->sectionTypes)) {
            $link[] = $article->getNumber();
            $link[] = $this->getSeo($article, ($article->getPublication()) ? $article->getPublication()->getSeo() : array());
        }

        $link = array_map(function ($part) {
            return trim($part, '/');
        }, $link);

        $link = implode('/', $link);
        return strpos($link, 'http') === 0 ? $link : 'http://' . $link;
    }

    /**
     * Get canonical link
     *
     * @param Newscoop\Entity\Article $article
     * @return string
     */
    public function getLinkCanonical(Article $article)
    {
        $link = array(
            trim($this->getPublicationAliasName($article), '/'),
            ($article->getLanguage()) ? $article->getLanguage()->getCode() : null,
            $this->getIssueShortName($article),
            $this->getSectionShortName($article),
        );

        if (!in_array($article->getType(), $this->sectionTypes)) {
            $link[] = $article->getNumber();
        }

        $link = implode('/', $link);
        return strpos($link, 'http') === 0 ? $link : 'http://' . $link;
    }

    /**
     * Get publication alias
     *
     * @param  Article $article
     *
     * @return string           Name of the publication alias
     */
    public function getPublicationAliasName(Article $article)
    {
        $alias = $this->em->getRepository('Newscoop\Entity\Aliases')->findOneBy(array(
            'id' => ($article->getPublication()) ? $article->getPublication()->getDefaultAliasId() : null,
            'publication' => $article->getPublication(),
        ));

        return $alias ? $alias->getName() : null;
    }

    /**
     * Get issue short name
     *
     * @param Newscoop\Entity\Article $article
     * @return string
     */
    public function getIssueShortName(Article $article)
    {
        $issue = $this->em->getRepository('Newscoop\Entity\Issue')->findOneBy(array(
            'number' => $article->getIssueId(),
            'publication' => $article->getPublicationId(),
            'language' => $article->getLanguageId(),
        ));

        return $issue ? $issue->getShortName() : null;
    }

    /**
     * Get section short name
     *
     * @param Newscoop\Entity\Article $article
     * @return string
     */
    public function getSectionShortName(Article $article)
    {
        $issue = $this->em->getRepository('Newscoop\Entity\Issue')
            ->findOneBy(array(
                'number' => $article->getIssueId(),
                'publication' => $article->getPublicationId(),
                'language' => $article->getLanguageId(),
            ));

        $section = $this->em->getRepository('Newscoop\Entity\Section')->findOneBy(array(
            'number' => $article->getSectionId(),
            'publication' => $article->getPublicationId(),
            'language' => $article->getLanguageId(),
            'issue' => $issue->getId(),
        ));

        return $section ? $section->getShortName() : null;
    }

    /**
     * Get seo string
     *
     * @param object $article
     * @param array $fields
     * @return string
     */
    public function getSeo($article, array $fields)
    {
        $seo = array();
        foreach ($fields as $field => $value) {
            switch ($field) {
                case 'name':
                    $seo[] = trim($article->getName());
                    break;

                case 'keywords':
                    $seo[] = trim($article->getKeywords());
                    break;

                case 'topics':
                    $topics = \ArticleTopic::GetArticleTopics($article->getNumber());
                    foreach ($articleTopics as $topic) {
                        $seo[] = trim($topic->getName($article->getLanguageId()));
                    }
                    break;
            }
        }

        $seo = trim(implode('-', array_filter($seo)), '-');
        $seo = preg_replace('/[\\\\,\/\.\?"\+&%:#]/', '', $seo);
        $seo = str_replace(' ', '-', $seo) . '.htm';
        return $this->encode($seo);
    }

    /**
     * Encode url
     *
     * @param string $url
     * @return string
     */
    public function encode($url)
    {
        list($url,) = explode('.', $url, 2);
        $url = strtolower($url);
        $url = str_replace(array_keys(self::$urlMap), array_values(self::$urlMap), $url);
        $url = preg_replace('#[^-a-z0-9.]#', '-', $url);
        $url = preg_replace('#[-]{2,}#', '-', $url);
        return trim($url, '-') . '.htm';
    }

    /**
     * Get article topic name
     *
     * @param object $article
     * @return string
     */
    public function getArticleTopicName($article)
    {
        $topics = $article->getTopicNames();
        return empty($topics) ? null : array_shift($topics);
    }
}
