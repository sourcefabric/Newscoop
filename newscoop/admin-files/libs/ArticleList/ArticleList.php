<?php
/**
 * @package Campsite
 *
 * @author Petr Jasek <petr.jasek@sourcefabric.org>
 * @copyright 2010 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */
require_once dirname(__FILE__).'/../BaseList/BaseList.php';
require_once WWW_DIR.'/classes/GeoMap.php';

/**
 * Article list component
 */
class ArticleList extends BaseList
{
    /** @var int */
    protected $publication = null;

    /** @var int */
    protected $issue = null;

    /** @var int */
    protected $section = null;

    /** @var int */
    protected $language = null;

    /** @var string */
    protected $workflow_status = null;

    /** @var string */
    protected $type = null;

    /** @var array */
    protected $filters = array();

    /** @var array */
    protected $orderBy = array();

    /** @var bool */
    protected static $renderFilters = false;

    /** @var bool */
    protected static $renderActions = false;

    /** @var string */
    protected static $lastId = null;

    /**
     * @param bool $randomId
     */
    public function __construct($randomId = false)
    {
        parent::__construct();

        $translator = \Zend_Registry::get('container')->getService('translator');
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
            'Number' => null,
            'Language' => $translator->trans('Language'),
            'Order' => $translator->trans('Order'),
            'Name' => $translator->trans('Title', array(), 'api'),
            'Section' => $translator->trans('Section'),
            'Webcode' => $translator->trans('Webcode', array(), 'library'),
            'Type' => $translator->trans('Type'),
            'Created' => $translator->trans('Created by'),
            'Author' => $translator->trans('Author'),
            'Status' => $translator->trans('Status'),
            'OnFrontPage' => $translator->trans('On Front Page'),
            'OnSectionPage' => $translator->trans('On Section Page'),
            'Images' => $translator->trans('Images'),
            'Topics' => $translator->trans('Topics'),
            'Comments' => $translator->trans('Comments'),
            'Reads' => $translator->trans('Reads'),
            'UseMap' => $translator->trans('Use Map', array(), 'library'),
            'Locations' => $translator->trans('Locations', array(), 'library'),
            'CreateDate' => $translator->trans('Create Date', array(), 'library'),
            'PublishDate' => $translator->trans('Publish Date', array(), 'library'),
            'LastModified' => $translator->trans('Last Modified', array(), 'articles'),
            'Preview' => $translator->trans('Preview'),
            'Translate' => $translator->trans('Translate'),
        );
    }

    /**
     * Set publication.
     * @param  int         $publication
     * @return ArticleList
     */
    public function setPublication($publication)
    {
        $this->publication = is_null($publication) ? null : (int) $publication;

        return $this;
    }

    /**
     * Set issue.
     * @param  int         $issue
     * @return ArticleList
     */
    public function setIssue($issue)
    {
        $this->issue = is_null($issue) ? null : (int) $issue;

        return $this;
    }

    /**
     * Set section.
     * @param  int         $section
     * @return ArticleList
     */
    public function setSection($section)
    {
        $this->section = is_null($section) ? null : (int) $section;

        return $this;
    }

    /**
     * Set language.
     * @param  int         $language
     * @return ArticleList
     */
    public function setLanguage($language)
    {
        $this->language = empty($language) ? 1 : (int) $language;

        return $this;
    }

    /**
     * Set status.
     * @param  string      $status
     * @return ArticleList
     */
    public function setWorkflowStatus($status)
    {
        if (array_search($status, array('pending', 'new', 'submitted', 'withissue', 'published')) !== false) {
            $this->workflow_status = $status;
        }

        return $this;
    }

    /**
     * Set type
     *
     * @param  string      $type
     * @return ArticleList
     */
    public function setType($type)
    {
        $this->type = (string) $type;

        return $this;
    }

    /**
     * Set filter.
     * @param  string      $name
     * @param  mixed       $value
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
     * @param  string      $column
     * @param  string      $direction
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

        include dirname(__FILE__).'/filters.php';
        self::$renderFilters = true;

        return $this;
    }

    /**
     * Render actions.
     * @return ArticleList
     */
    public function renderActions()
    {
        $this->beforeRender();

        include dirname(__FILE__).'/actions.php';
        self::$renderActions = true;

        return $this;
    }

    /**
     * Render table.
     * @return ArticleList
     */
    public function render()
    {
        $this->beforeRender();

        include dirname(__FILE__).'/table.php';
        self::$renderTable = true;
        echo '</div><!-- /#list-'.$this->id.' -->';

        return $this;
    }

    /**
     * Process item
     * @param  Article $article
     * @return array
     */
    public function processItem($article)
    {
        global $g_user, $Campsite;
        $translator = \Zend_Registry::get('container')->getService('translator');
        $editorService = \Zend_Registry::get('container')->getService('newscoop.editor');
        $articleLink = $editorService->getLink($article);
        $articleLinkParams = $editorService->getLinkParameters($article);
        $articleLinkParamsTranslate = $articleLinkParams.'&amp;f_action=translate&amp;f_action_workflow='.$article->getWorkflowStatus()
        .'&amp;f_article_code='.$article->getArticleNumber().'_'.$article->getLanguageId();
        $previewLink = $Campsite['WEBSITE_URL'].'/admin/articles/preview.php'.$editorService->getLinkParameters($article);
        $htmlPreviewLink = '<a href="'.$previewLink.'" target="_blank" title="'.$translator->trans('Preview').'">'.$translator->trans('Preview').'</a>';
        $translateLink = $Campsite['WEBSITE_URL'].'/admin/articles/translate.php'.$articleLinkParamsTranslate;
        $htmlTranslateLink = '<a href="'.$translateLink.'" target="_blank" title="'.$translator->trans('Translate').'">'.$translator->trans('Translate').'</a>';

        $lockInfo = '';
        $lockHighlight = false;
        $timeDiff = camp_time_diff_str($article->getLockTime());
        if ($article->isLocked() && ($timeDiff['days'] <= 0)) {
            $lockUser = new User($article->getLockedByUser());
            if ($timeDiff['hours'] > 0) {
                $lockInfo = $translator->trans('The article has been locked by $1 ($2) $3 hour(s) and $4 minute(s) ago.', array(
                '$1' => htmlspecialchars($lockUser->getRealName()),
                '$2' => htmlspecialchars($lockUser->getUserName()),
                '$3' => $timeDiff['hours'],
                '$4' => $timeDiff['minutes'], ), 'articles');
            } else {
                $lockInfo = $translator->trans('The article has been locked by $1 ($2) $3 minute(s) ago.', array(
                '$1' => htmlspecialchars($lockUser->getRealName()),
                '$2' => htmlspecialchars($lockUser->getUserName()),
                '$3' => $timeDiff['minutes'], ), 'articles');
            }
            if ($article->getLockedByUser() != $g_user->getUserId()) {
                $lockHighlight = true;
            }
        }

        $tmpUser = new User($article->getCreatorId());
        $tmpArticleType = new ArticleType($article->getType());

        $tmpAuthor = new Author();
        $articleAuthors = ArticleAuthor::GetAuthorsByArticle($article->getArticleNumber(), $article->getLanguageId());
        foreach ((array) $articleAuthors as $author) {
            if (strtolower($author->getAuthorType()->getName()) == 'author') {
                $tmpAuthor = $author;
                break;
            }
        }
        if (!$tmpAuthor->exists() && isset($articleAuthors[0])) {
            $tmpAuthor = $articleAuthors[0];
        }

        $onFrontPage = $article->onFrontPage() ? $translator->trans('Yes') : $translator->trans('No');
        $onSectionPage = $article->onSectionPage() ? $translator->trans('Yes') : $translator->trans('No');

        $imagesNo = (int) ArticleImage::GetImagesByArticleNumber($article->getArticleNumber(), true);
        $topicsNo = (int) ArticleTopic::GetArticleTopics($article->getArticleNumber(), true);
        $commentsNo = '';
        if ($article->commentsEnabled()) {
            global $controller;
            $repositoryComments = $controller->getHelper('entity')->getRepository('Newscoop\Entity\Comment');
            $filter = array( 'thread' => $article->getArticleNumber(), 'language' => $article->getLanguageId());
            $params = array( 'sFilter' => $filter);
            $commentsNo = $repositoryComments->getCount($params);
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
            $article->isLocked() ? '<span class="ui-icon ui-icon-locked'.(!$lockHighlight ? ' current-user' : '').'" title="'.$lockInfo.'"></span>' : '',
            $articleLink,
            $translator->trans('Edit'), htmlspecialchars($article->getName()." ({$article->getLanguageName()})"),
            htmlspecialchars($article->getName().(empty($_REQUEST['language']) ? " ({$language->getCode()})" : ''))), // /sprintf
            htmlspecialchars($article->getSection()->getName()),
            $article->getWebcode(),
            htmlspecialchars($tmpArticleType->getDisplayName()),
            $tmpUser->getRealName() ? sprintf('%s %s', $tmpUser->getRealName(), $tmpUser->getLastName()). ' (<a style="color:#007fb3;" href="'.\Zend_Registry::get('container')->get('zend_router')->assemble(array(
                'module' => 'admin',
                'controller' => 'user',
                'action' => 'edit',
                'user' => $tmpUser->getUserId(),
            ), 'default', true).'">'.$tmpUser->getUserName().'</a>)' : $translator->trans('N/A'),
            htmlspecialchars($tmpAuthor->getName()),
            $article->getWorkflowStatus(),
            $onFrontPage,
            $onSectionPage,
            $imagesNo,
            $topicsNo,
            $commentsNo,
            (int) $article->getReads(),
            Geo_Map::GetArticleMapId($article) != null ? $translator->trans('Yes') : $translator->trans('No'),
            (int) sizeof(Geo_Map::GetLocationsByArticle($article)),
            $article->getCreationDate(),
            $article->getPublishDate(),
            $article->getLastModified(),
            $htmlPreviewLink,
            $htmlTranslateLink,
        );
    }

    /**
     * Handle data
     */
    public function doData()
    {
        global $ADMIN_DIR, $g_user;
        foreach ($_REQUEST['args'] as $arg) {
            $_REQUEST[$arg['name']] = $arg['value'];
        }

        return require_once dirname(__FILE__).'/do_data.php';
    }

    public function getFilterIssues()
    {
        global $ADMIN_DIR, $g_user;
        require_once $GLOBALS['g_campsiteDir'].'/classes/Publication.php';
        require_once $GLOBALS['g_campsiteDir'].'/classes/Issue.php';
        require_once $GLOBALS['g_campsiteDir'].'/classes/Section.php';
        require_once $GLOBALS['g_campsiteDir'].'/classes/Author.php';
        $translator = \Zend_Registry::get('container')->getService('translator');

        foreach ($_REQUEST['args'] as $arg) {
            $_REQUEST[$arg['name']] = $arg['value'];
        }

        if ($_REQUEST['publication'] > 0) {
            $publication = $_REQUEST['publication'];
        } else {
            $publication = null;
        }

        if ($_REQUEST['language'] > 0) {
            $language = $_REQUEST['language'];
        } else {
            $language = null;
        }

        $newIssues = array();
        $issues = Issue::GetIssues($publication, $language);
        $issuesNo = is_array($issues) ? sizeof($issues) : 0;
        $menuIssueTitle = $issuesNo > 0 ? $translator->trans('All Issues', array(), 'library') : $translator->trans('No issues found', array(), 'library');
        foreach ($issues as $issue) {
            $newIssues[] = array('val' => $issue->getPublicationId().'_'.$issue->getIssueNumber().'_'.$issue->getLanguageId() , 'name' => $issue->getName());
        }
        $returns = array();
        $returns['items'] = $newIssues;
        $returns['itemsNo'] = $issuesNo;
        $returns['menuItemTitle'] = $menuIssueTitle;

        return json_encode($returns);
    }

    public function getFilterSections()
    {
        $translator = \Zend_Registry::get('container')->getService('translator');
        global $ADMIN_DIR, $g_user;
        require_once $GLOBALS['g_campsiteDir'].'/classes/Publication.php';
        require_once $GLOBALS['g_campsiteDir'].'/classes/Issue.php';
        require_once $GLOBALS['g_campsiteDir'].'/classes/Section.php';
        require_once $GLOBALS['g_campsiteDir'].'/classes/Author.php';

        foreach ($_REQUEST['args'] as $arg) {
            $_REQUEST[$arg['name']] = $arg['value'];
        }

        if ($_REQUEST['publication'] > 0) {
            $publication = $_REQUEST['publication'];
        } else {
            $publication = null;
        }

        $language = null;
        if ($_REQUEST['issue'] > 0) {
            $issueArray = explode("_", $_REQUEST['issue']);
            $issue = $issueArray[1];
            if (isset($issueArray[2])) {
                $language = $issueArray[2];
            }
        } else {
            $issue = null;
        }

        if ($_REQUEST['language'] > 0) {
            $language = $_REQUEST['language'];
        }

        // get sections
        $sections = array();

        $section_objects = Section::GetSections($publication, $issue, $language);

        foreach ($section_objects as $section) {
            if (!isset($sections[$section->getSectionNumber()])) {
                $sections[$section->getSectionNumber()] = $section;
            }
        }
        $newSections = array();
        foreach ($sections as $section) {
            $newSections[] = array('val' => $section->getPublicationId().'_'.$section->getIssueNumber().'_'.$section->getLanguageId().'_'.$section->getSectionNumber(), 'name' => $section->getName());
        }
        $sectionsNo = is_array($newSections) ? sizeof($newSections) : 0;
        $menuSectionTitle = $sectionsNo > 0 ? $translator->trans('All Sections', array(), 'library') : $translator->trans('No sections found', array(), 'library');

        $returns = array();
        $returns['items'] = $newSections;
        $returns['itemsNo'] = $sectionsNo;
        $returns['menuItemTitle'] = $menuSectionTitle;

        return json_encode($returns);
    }

    /**
     * Handle action
     * @param  string $f_action
     * @param  array  $f_items
     * @param  array  $f_params
     * @return void
     */
    public static function doAction($f_action, $f_items, $f_params = array())
    {
        global $ADMIN_DIR, $g_user, $Campsite, $ADMIN;

        // this is used for some actions, but some do not have it defined at all
        if (empty($f_target)) {
            $f_target = '';
        }

        return require_once dirname(__FILE__).'/do_action.php';
    }

    /**
     * Handle order
     * @param  array $f_order
     * @param  int   $f_language
     * @return void
     */
    public static function doOrder($f_order, $f_language)
    {
        global $ADMIN_DIR;

        return require_once dirname(__FILE__).'/do_order.php';
    }

    /**
     * Get column keys
     *
     * @return array
     */
    public function getColumnKeys()
    {
        return array_keys($this->cols);
    }
}
