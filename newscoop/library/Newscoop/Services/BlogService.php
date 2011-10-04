<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Newscoop\Entity\User;

/**
 * Blog service
 */
class BlogService
{
    const SEPARATOR = ',';

    /** @var array */
    private $config = array();

    /**
     * @param array $config
     */
    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * Test if user is blogger
     *
     * @param Newscoop\Entity\User $user
     * @return bool
     */
    public function isBlogger(User $user)
    {
        foreach ($user->getGroups() as $group) {
            if ($this->config['role'] == $group->getId()) {
                if (count($user->getGroups()) == 1) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Get blog section for given user
     *
     * @param Newscoop\Entity\User $user
     * @return Section|null
     */
    public function getSection(User $user)
    {
        $articles = \Article::GetArticles($this->config['publication'], $this->config['issue'], null, null, null, false, array(
            "Type = '" . $this->config['type'] . "'",
        ));

        foreach ($articles as $article) {
            $data = $article->getArticleData();
            $authors = explode(self::SEPARATOR, $data->getFieldValue('loginname'));
            if (in_array($user->getUsername(), $authors)) {
                return $article->getSection();
            }
        }

        return null;
    }

    /**
     * Create blog article
     *
     * @param string $title
     * @param Section $section
     * @return Article
     */
    public function createBlog($title, \Section $section)
    {
        $article = new \Article($section->getLanguageId());
        $article->create('news', $title, $section->getPublicationId(), $section->getIssueNumber(), $section->getSectionId());
        return $article;
    }
}
