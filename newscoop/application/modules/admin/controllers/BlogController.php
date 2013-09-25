<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Annotations\Acl;

require_once APPLICATION_PATH . '/../admin-files/libs/ArticleList/ArticleList.php';

/**
 * Blog controller
 *
 * @Acl(ignore=1)
 */
class Admin_BlogController extends Zend_Controller_Action
{
    /** @var Newscoop\Services\BlogService */
    private $blogService;

    /** @var Newscoop\Entity\User */
    private $user;

    public function init()
    {
        $this->blogService = $this->_helper->service('blog');
        $this->user = $this->_helper->service('user')->getCurrentUser();
    }

    public function preDispatch()
    {
        if (empty($this->user) || !$this->blogService->isBlogger($this->user)) {
            $this->_helper->flashMessenger(array('error', "You're not a blogger"));
            $this->_helper->redirector('index', 'index', 'admin');
        }
    }

    public function indexAction()
    {
        $form = new Admin_Form_Blog();
        $section = $this->blogService->getSection($this->user);

        if (empty($section)) {
            $this->_forward('nosection');
            return;
        }

        $request = $this->getRequest();
        if ($request->isPost() && $form->isValid($request->getPost())) {
            $blog = $this->blogService->createBlog($form->title->getValue(), $section);
            $this->_helper->flashMessenger("Article created");
            $this->_helper->redirector->gotoUrl($this->_helper->article->getEditLink($blog));
        }

        $blogConfig = \Zend_Registry::get('container')->getParameter('blog');
        $list = new ArticleList();
        $list->setPublication($section->getPublicationId());
        $list->setIssue($section->getIssueNumber());
        $list->setSection($section->getSectionNumber());
        $list->setLanguage($section->getLanguageId());
        $list->setType($blogConfig['article_type']);

        $this->view->list = $list;
        $this->view->form = $form;
    }

    public function nosectionAction()
    {
    }
}
