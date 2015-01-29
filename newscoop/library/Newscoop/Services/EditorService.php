<?php
/**
 * @package Newscoop
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Newscoop\EditorInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
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

        $editorLink = $arguments->getArgument('link');
        if ($editorLink) {
            return $editorLink;
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
	        . '&amp;f_issue_number=' . $article['issueNumber'] . '&amp;f_section_number=' . $article['sectionNumber']
	        . '&amp;f_article_number=' . $article['number'] . '&amp;f_language_id=' . $article['languageId']
	        . '&amp;f_language_selected=' . $article['languageId'];
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
