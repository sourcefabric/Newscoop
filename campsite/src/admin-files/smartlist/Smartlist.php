<?php
/**
 * @package Campsite
 *
 * @author Petr Jasek <petr.jasek@sourcefabric.org>
 * @copyright 2010 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */
class Smartlist
{
    /** @var string */
    private $id = '';

    /** @var string */
    private $web = '';

    /** @var string */
    private $admin = '';

    /** @var int */
    private $publication = NULL;

    /** @var int */
    private $issue = NULL;

    /** @var int */
    private $section = NULL;

    /** @var int */
    private $language = NULL;

    /** @var array */
    private $filters = array();

    /** @var array */
    private $items = NULL;

    /** @var bool */
    private $search = FALSE;

    /** @var bool */
    private $colVis = FALSE;

    /** @var bool */
    private static $renderTable = FALSE;

    /** @var bool */
    private static $renderFilters = FALSE;

    /** @var bool */
    private static $renderActions = FALSE;

    /**
     * @param string $web
     * @param string $admin
     */
    public function __construct()
    {
        global $Campsite, $ADMIN;

        // set paths
        $this->web = $Campsite['WEBSITE_URL'];
        $this->admin = $ADMIN;

        camp_load_translation_strings('articles');
        camp_load_translation_strings('universal_list');

        $this->id = substr(sha1((string) mt_rand()), -6);

        echo '<div id="smartlist-' . $this->id . '" class="smartlist">';
    }

    /**
     * Set publication.
     * @param int $publication
     * @return Smartlist
     */
    public function setPublication($publication)
    {
        $this->publication = empty($publication) ? NULL : (int) $publication;
        return $this;
    }

    /**
     * Set issue.
     * @param int $issue
     * @return Smartlist
     */
    public function setIssue($issue)
    {
        $this->issue = empty($issue) ? NULL : (int) $issue;
        return $this;
    }

    /**
     * Set section.
     * @param int $section
     * @return Smartlist
     */
    public function setSection($section)
    {
        $this->section = empty($section) ? NULL : (int) $section;
        return $this;
    }

    /**
     * Set language.
     * @param int $language
     * @return Smartlist
     */
    public function setLanguage($language)
    {
        $this->language = empty($language) ? NULL : (int) $language;
        return $this;
    }

    /**
     * Set filter.
     * @param string $name
     * @param mixed $value
     * @return Smartlist
     */
    public function setFilter($name, $value)
    {
        $this->filters[$name] = $value;
        return $this;
    }

    /**
     * Set items.
     * @param array $items
     * @return Smartlist
     */
    public function setItems($items)
    {
        if (is_array($items[0])) {
            $items = $items[0];
        }
        $this->items = array();
        foreach ((array) $items as $item) {
            $this->items[] = self::ProcessArticle($item);
        }
        return $this;
    }

    /**
     * Get sDom property.
     * @return string
     */
    public function getSDom()
    {
        return sprintf('<"H"%s%srip>t<"F"ipl>',
            $this->colVis ? 'C' : '',
            $this->search ? 'f' : ''
        );
    }

    /**
     * Set search.
     * @param bool $search
     * @return Smartlist
     */
    public function setSearch($search = FALSE)
    {
        $this->search = (bool) $search;
        return $this;
    }

    /**
     * Set ColVis.
     * @param bool $colVis
     * @return Smartlist
     */
    public function setColVis($colVis = FALSE)
    {
        $this->colVis = (bool) $colVis;
        return $this;
    }

    /**
     * Render filters.
     * @return Smartlist
     */
    public function renderFilters()
    {
        include dirname(__FILE__) . '/filters.php';
        self::$renderFilters = TRUE;
        return $this;
    }

    /**
     * Render actions.
     * @return Smartlist
     */
    public function renderActions()
    {
        include dirname(__FILE__) . '/actions.php';
        self::$renderActions = TRUE;
        return $this;
    }

    /**
     * Render table.
     * @return Smartlist
     */
    public function render()
    {
        include dirname(__FILE__) . '/table.php';
        self::$renderTable = TRUE;
        echo '</div><!-- /#list-' . $this->id . ' -->';
        return $this;
    }

    /**
     * Process article for rendering.
     * @param Article $article
     * @return array
     */
    public static function ProcessArticle(Article $article)
    {
        $articleLinkParams = '?f_publication_id=' . $article->getPublicationId()
        . '&f_issue_number=' . $article->getIssueNumber() . '&f_section_number=' . $article->getSectionNumber()
        . '&f_article_number=' . $article->getArticleNumber() . '&f_language_id=' . $article->getLanguageId()
        . '&f_language_selected=' . $article->getLanguageId();
    $articleLink = $Campsite['WEBSITE_URL'].'/admin/articles/edit.php' . $articleLinkParams;
    $previewLink = $Campsite['WEBSITE_URL'].'/admin/articles/preview.php' . $articleLinkParams;

    $lockInfo = '';
    $lockHighlight = false;
    $timeDiff = camp_time_diff_str($article->getLockTime());
    if ($article->isLocked() && ($timeDiff['days'] <= 0)) {
        $lockUser = new User($article->getLockedByUser());
        if ($timeDiff['hours'] > 0) {
            $lockInfo = getGS('The article has been locked by $1 ($2) $3 hour(s) and $4 minute(s) ago.',
                htmlspecialchars($lockUser->getRealName()),
                htmlspecialchars($lockUser->getUserName()),
                $timeDiff['hours'], $timeDiff['minutes']);
        } else {
            $lockInfo = getGS('The article has been locked by $1 ($2) $3 minute(s) ago.',
                htmlspecialchars($lockUser->getRealName()),
                htmlspecialchars($lockUser->getUserName()),
                $timeDiff['minutes']);
        }
        if ($article->getLockedByUser() != $g_user->getUserId()) {
            $lockHighlight = true;
        }
    }

    $tmpUser = new User($article->getCreatorId());
    $tmpAuthor = new Author($article->getAuthorId());
    $tmpArticleType = new ArticleType($article->getType());

    $onFrontPage = $article->onFrontPage() ? getGS('Yes') : getGS('No');
    $onSectionPage = $article->onSectionPage() ? getGS('Yes') : getGS('No');

    $imagesNo = ArticleImage::GetImagesByArticleNumber($article->getArticleNumber(), true);
    $topicsNo = ArticleTopic::GetArticleTopics($article->getArticleNumber(), true);
    $commentsNo = '';
    if ($article->commentsEnabled()) {
        $commentsNo = ArticleComment::GetArticleComments($article->getArticleNumber(), $article->getLanguageId(), null, true);
    } else {
        $commentsNo = 'No';
    }

    return array(
        $article->getArticleNumber(),
        $article->getLanguageId(),
        sprintf('<a href="%s%s" title="%s %s">%s</a>',
            $articleLink, $articleLinkParams,
            getGS('Edit'), $article->getName(),
            $article->getName()),
        $tmpArticleType->getDisplayName(),
        $tmpUser->getRealName(),
        $tmpAuthor->getFirstName(),
        $article->getWorkflowStatus(),
        $onFrontPage,
        $onSectionPage,
        $imagesNo,
        $topicsNo,
        $commentsNo,
        $article->getReads(),
        $article->getCreationDate(),
        $article->getPublishDate(),
        $article->getLastModified(),
    );
    }
}
