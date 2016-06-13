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
require_once WWW_DIR . '/classes/Article.php';

/**
 * Article list component
 */
class ContextList extends BaseList
{
    /** @var int */
    protected $publication = 0;

    /** @var int */
    protected $issue = 0;

    /** @var int */
    protected $section = null;

    /** @var int */
    protected $language = null;

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
            'Number' => NULL,
            'Language' => $translator->trans('Language'),
            'Order' => $translator->trans('Order'),
        );
    }

    /**
     * Set publication.
     * @param  int         $publication
     * @return ArticleList
     */
    public function setPublication($publication)
    {
        $this->publication = empty($publication) ? NULL : (int) $publication;

        return $this;
    }

    /**
     * Set issue.
     * @param  int         $issue
     * @return ArticleList
     */
    public function setIssue($issue)
    {
        $this->issue = empty($issue) ? NULL : (int) $issue;

        return $this;
    }

    /**
     * Set section.
     * @param  int         $section
     * @return ArticleList
     */
    public function setSection($section)
    {
        $this->section = empty($section) ? NULL : (int) $section;

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
     * @param  Article $article
     * @return array
     */
    public function processItem($article)
    {
        global $g_user, $Campsite;
        $translator = \Zend_Registry::get('container')->getService('translator');

        return array(
            $article->getArticleNumber(),
            $article->getLanguageId(),
            sprintf('
                <div class="context-item" langid="%s">
                    <div class="context-drag-topics"><a href="#" title="drag to sort"></a></div>
                    <div class="context-item-header">
                        <div class="context-item-date">%s (%s) (%s)</div>
                        <a href="#" class="view-article" onClick="viewArticle($(this).parent(\'div\').parent(\'div\').parent(\'td\').parent(\'tr\').attr(\'id\'), $(this).parents(\'.context-item:eq(0)\').attr(\'langid\'));">%s</a>
                    </div>
                    <a href="javascript:void(0)" class="corner-button" style="display: none" onClick="removeFromContext($(this).parent(\'div\').parent(\'td\').parent(\'tr\').attr(\'id\'));removeFromContext($(this).parents(\'.item:eq(0)\').attr(\'id\'));toggleDragZonePlaceHolder();"><span class="ui-icon ui-icon-closethick"></span></a>
                    <div class="context-item-summary">%s</div>
                    </div>
            ', $article->getLanguageId(), $article->getCreationDate(), $article->getWorkflowDisplayString(), $article->getType(), $translator->trans('View article', array(), 'library'), $article->getName()),
        );
    }

    /**
     * Handle data
     * @param array $f_request
     */

    public function doData($f_request = null)
    {
        global $ADMIN_DIR, $g_user;
        foreach ($_REQUEST['args'] as $arg) {
            $_REQUEST[$arg['name']] = $arg['value'];
        }

        return require_once dirname(__FILE__) . '/do_data.php';
    }

    public function getFilterIssues()
    {

        global $ADMIN_DIR, $g_user;
        require_once $GLOBALS['g_campsiteDir'] . '/classes/Publication.php';
        require_once $GLOBALS['g_campsiteDir'] . '/classes/Issue.php';
        require_once $GLOBALS['g_campsiteDir'] . '/classes/Section.php';
        require_once $GLOBALS['g_campsiteDir'] . '/classes/Author.php';

        $translator = \Zend_Registry::get('container')->getService('translator');

        foreach ($_REQUEST['args'] as $arg) {
            $_REQUEST[$arg['name']] = $arg['value'];
        }

        if ($_REQUEST['publication'] > 0) {
            $publication = $_REQUEST['publication'];
        } else {
            $publication = NULL;
        }

        if ($_REQUEST['language'] > 0) {
            $language = $_REQUEST['language'];
        } else {
            $language = NULL;
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
        global $ADMIN_DIR, $g_user;
        require_once $GLOBALS['g_campsiteDir'] . '/classes/Publication.php';
        require_once $GLOBALS['g_campsiteDir'] . '/classes/Issue.php';
        require_once $GLOBALS['g_campsiteDir'] . '/classes/Section.php';
        require_once $GLOBALS['g_campsiteDir'] . '/classes/Author.php';

        $translator = \Zend_Registry::get('container')->getService('translator');

        foreach ($_REQUEST['args'] as $arg) {
            $_REQUEST[$arg['name']] = $arg['value'];
        }

        if ($_REQUEST['publication'] > 0) {
            $publication = $_REQUEST['publication'];
        } else {
            $publication = NULL;
        }

        $language = NULL;
        if ($_REQUEST['issue'] > 0) {
            $issueArray = explode("_",$_REQUEST['issue']);
            $issue = $issueArray[1];
            if (isset($issueArray[2])) {
                $language = $issueArray[2];
            }
        } else {
            $issue = NULL;
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

        return require_once dirname(__FILE__) . '/do_action.php';
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

        return require_once dirname(__FILE__) . '/do_order.php';
    }

    public function doPreview($f_language_id = 0, $_article_no = 0)
    {
    }
}
