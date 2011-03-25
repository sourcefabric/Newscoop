<?php
/**
 * @package Campsite
 *
 * @author Petr Jasek <petr.jasek@sourcefabric.org>
 * @copyright 2010 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */

require_once dirname(__FILE__) . '/../BaseList/BaseList.php';
require_once WWW_DIR . '/classes/GeoMap.php';

/**
 * Article list component
 */
class ArticleList extends BaseList
{
    /** @var int */
    protected $publication = 0;

    /** @var int */
    protected $issue = 0;

    /** @var int */
    protected $section = 0;

    /** @var int */
    protected $language = 1;

    /** @var array */
    protected $filters = array();

    /** @var array */
    protected $orderBy = array();

    /** @var bool */
    protected static $renderFilters = FALSE;

    /** @var bool */
    protected static $renderActions = FALSE;

    /** @var string */
    protected static $lastId = NULL;

    /**
     * @param bool $randomId
     */
    public function __construct($randomId = FALSE)
    {
        parent::__construct();

        // generate id - unique per page instance
        if (empty(self::$lastId)) {
            self::$lastId = __FILE__;
            if ($randomId) {
                self::$lastId = uniqid();
            }
        }
        $this->id = substr(sha1(self::$lastId), -6);
        self::$lastId = $this->id;

        // column titles
        $this->cols = array(
            'Number' => NULL,
            'Language' => getGS('Language'),
            'Order' => getGS('Order'),
            'Name' => getGS('Name'),
            'Type' => getGS('Type'),
            'Created' => getGS('Created by'),
            'Author' => getGS('Author'),
            'Status' => getGS('Status'),
            'OnFronPage' => getGS('On Front Page'),
            'OnSectionPage' => getGS('On Section Page'),
            'Images' => getGS('Images'),
            'Topics' => getGS('Topics'),
            'Comments' => getGS('Comments'),
            'Reads' => getGS('Reads'),
            'UseMap' => getGS('Use Map'),
            'Locations' => getGS('Locations'),
            'CreateDate' => getGS('Create Date'),
            'PublishDate' => getGS('Publish Date'),
            'LastModified' => getGS('Last Modified'),
        );
    }

    /**
     * Set publication.
     * @param int $publication
     * @return ArticleList
     */
    public function setPublication($publication)
    {
        $this->publication = empty($publication) ? NULL : (int) $publication;
        return $this;
    }

    /**
     * Set issue.
     * @param int $issue
     * @return ArticleList
     */
    public function setIssue($issue)
    {
        $this->issue = empty($issue) ? NULL : (int) $issue;
        return $this;
    }

    /**
     * Set section.
     * @param int $section
     * @return ArticleList
     */
    public function setSection($section)
    {
        $this->section = empty($section) ? NULL : (int) $section;
        return $this;
    }

    /**
     * Set language.
     * @param int $language
     * @return ArticleList
     */
    public function setLanguage($language)
    {
        $this->language = empty($language) ? 1 : (int) $language;
        return $this;
    }

    /**
     * Set filter.
     * @param string $name
     * @param mixed $value
     * @return ArticleList
     */
    public function setFilter($name, $value)
    {
        $this->filters[$name] = $value;
        return $this;
    }

    /**
     * Set column to order by.
     *
     * @param string $column
     * @param string $direction
     * @return ArticleList
     */
    public function setOrderBy($column, $direction = 'asc')
    {
        if (!isset($this->cols[$column])) {
            return $this;
        }

        $columnNo = array_search($column, array_keys($this->cols));
        $this->orderBy[$columnNo] = strtolower($direction) == 'desc' ? 'desc' : 'asc';

        return $this;
    }

    /**
     * Render filters.
     * @return ArticleList
     */
    public function renderFilters()
    {
        $this->beforeRender();
        
        include dirname(__FILE__) . '/filters.php';
        self::$renderFilters = TRUE;
        return $this;
    }

    /**
     * Render actions.
     * @return ArticleList
     */
    public function renderActions()
    {
        $this->beforeRender();
        
        include dirname(__FILE__) . '/actions.php';
        self::$renderActions = TRUE;
        return $this;
    }

    /**
     * Render table.
     * @return ArticleList
     */
    public function render()
    {
        $this->beforeRender();
        
        include dirname(__FILE__) . '/table.php';
        self::$renderTable = TRUE;
        echo '</div><!-- /#list-' . $this->id . ' -->';
        return $this;
    }

    /**
     * Process item
     * @param Article $article
     * @return array
     */
    public function processItem(Article $article)
    {
        global $g_user, $Campsite;

        $articleLinkParams = '?f_publication_id=' . $article->getPublicationId()
        . '&amp;f_issue_number=' . $article->getIssueNumber() . '&amp;f_section_number=' . $article->getSectionNumber()
        . '&amp;f_article_number=' . $article->getArticleNumber() . '&amp;f_language_id=' . $article->getLanguageId()
        . '&amp;f_language_selected=' . $article->getLanguageId();
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
    $tmpArticleType = new ArticleType($article->getType());

    $tmpAuthor = new Author();
    $articleAuthors = ArticleAuthor::GetAuthorsByArticle($article->getArticleNumber(), $article->getLanguageId());
    foreach((array) $articleAuthors as $author) {
        if (strtolower($author->getAuthorType()->getName()) == 'author') {
            $tmpAuthor = $author;
            break;
        }
    }
    if (!$tmpAuthor->exists() && isset($articleAuthors[0])) {
        $tmpAuthor = $articleAuthors[0];
    }

    $onFrontPage = $article->onFrontPage() ? getGS('Yes') : getGS('No');
    $onSectionPage = $article->onSectionPage() ? getGS('Yes') : getGS('No');

    $imagesNo = (int) ArticleImage::GetImagesByArticleNumber($article->getArticleNumber(), true);
    $topicsNo = (int) ArticleTopic::GetArticleTopics($article->getArticleNumber(), true);
    $commentsNo = '';
    if ($article->commentsEnabled()) {
        $commentsNo = (int) ArticleComment::GetArticleComments($article->getArticleNumber(), $article->getLanguageId(), null, true);
    } else {
        $commentsNo = 'No';
    }

    // get language code
    $language = new Language($article->getLanguageId());

    return array(
        $article->getArticleNumber(),
        $article->getLanguageId(),
        $article->getOrder(),
        sprintf('%s <a href="%s" title="%s %s">%s</a>',
            $article->isLocked() ? '<span class="ui-icon ui-icon-locked' . (!$lockHighlight ? ' current-user' : '' ) . '" title="' . $lockInfo . '"></span>' : '',
            $articleLink,
            getGS('Edit'), $article->getName() . " ({$article->getLanguageName()})",
            $article->getName() . (empty($_REQUEST['language']) ? " ({$language->getCode()})" : '')),
        $tmpArticleType->getDisplayName(),
        $tmpUser->getRealName(),
        $tmpAuthor->getName(),
        $article->getWorkflowStatus(),
        $onFrontPage,
        $onSectionPage,
        $imagesNo,
        $topicsNo,
        $commentsNo,
        (int) $article->getReads(),
        Geo_Map::GetArticleMapId($article) != NULL ? getGS('Yes') : getGS('No'),
        (int) sizeof(Geo_Map::GetLocationsByArticle($article)),
        $article->getCreationDate(),
        $article->getPublishDate(),
        $article->getLastModified(),
    );
    }

    /**
     * Handle data
     * @param array $f_request
     */
    public function doData($f_request)
    {
        global $ADMIN_DIR, $g_user;
        foreach ($_REQUEST['args'] as $arg) {
            $_REQUEST[$arg['name']] = $arg['value'];
        }
        return require_once dirname(__FILE__) . '/do_data.php';
    }

    /**
     * Handle action
     * @param string $f_action
     * @param array $f_items
     * @param array $f_params
     * @return void
     */
    public static function doAction($f_action, $f_items, $f_params = array())
    {
        global $ADMIN_DIR, $g_user, $Campsite, $ADMIN;
        return require_once dirname(__FILE__) . '/do_action.php';
    }

    /**
     * Handle order
     * @param array $f_order
     * @param int $f_language
     * @return void
     */
    public static function doOrder($f_order, $f_language)
    {
        global $ADMIN_DIR;
        return require_once dirname(__FILE__) . '/do_order.php';
    }
}
