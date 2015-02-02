<?php
/**
 * @package Newscoop
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Newscoop\EditorInterface;
use Newscoop\EventDispatcher\Events\GenericEvent;
use Newscoop\Entity\Article;

/**
 * Editor service responsible for choosing Article Edit Screen
 */
class EditorService implements EditorInterface
{
    const DEFAULT_EDITOR_LINK = "/admin/articles/edit.php";

    protected $dispatcher;
    protected $em;

    public function __construct($dispatcher, $em)
    {
        $this->dispatcher = $dispatcher;
        $this->em = $em;
    }

    /**
     * {@inheritDoc}
     */
    public function getLink($article)
    {
        $articleInfo = $this->getArticleDetails($article);
        $language = $this->em->getReference('Newscoop\Entity\Language', $articleInfo['languageId']);
        $arguments = $this->dispatcher->dispatch('newscoop_admin.editor', new GenericEvent(null, array(
            'articleNumber' => $articleInfo['number'],
            'articleLanguage' => $language->getCode()
        )));

        if ($arguments->hasArgument('link') && $arguments->getArgument('link')) {
            return $arguments->getArgument('link');
        }

        return self::DEFAULT_EDITOR_LINK . $this->getLinkParameters($article);
    }

    /**
     * {@inheritDoc}
     */
    public function getLinkParameters($article)
    {
        $article = $this->getArticleDetails($article);

        return '?f_publication_id=' . $article['publicationId']
            . '&f_issue_number=' . $article['issueId'] . '&f_section_number=' . $article['sectionId']
            . '&f_article_number=' . $article['number'] . '&f_language_id=' . $article['languageId']
            . '&f_language_selected=' . $article['languageId'];
    }

    private function getArticleDetails($article)
    {
        $articleInfo = array(
            'publicationId' => $article->getPublicationId(),
            'languageId' => $article->getLanguageId(),
        );

        if ($article instanceof Article) {
            $articleInfo['issueId'] = $article->getIssueId();
            $articleInfo['sectionId'] = $article->getSectionId();
            $articleInfo['number'] = $article->getNumber();

            return $articleInfo;
        }

        $articleInfo['issueId'] = $article->getIssueNumber();
        $articleInfo['sectionId'] = $article->getSectionNumber();
        $articleInfo['number'] = $article->getArticleNumber();

        return $articleInfo;
    }
}
