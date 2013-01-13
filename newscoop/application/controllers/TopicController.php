<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Topics controller
 */

class TopicController extends Zend_Controller_Action
{
    const LIMIT = 10;

    private $container;

    /** @var int */
    private $page;

    public function init()
    {
        $this->container = \Zend_Registry::get('container');
        $this->page = $this->_getParam('page', 1);

        if ($this->page < 1) {
            $this->page = 1;
        }
    }

    public function articlesAction()
    {
        $em = $this->container->get('em');
        $topicId = $this->_getParam('id');

        $language = $em->getRepository('Newscoop\Entity\Language')->findOneByCode($this->_getParam('language'));
        $topic = $em->getRepository('Newscoop\Entity\Topic')
            ->findOneBy(array(
                'id' => $topicId, 
                'language' => $language->getId()
            ));
    
        $articles = $em->getRepository('Newscoop\Entity\Article')
            ->getArticlesForTopic(null, $topicId, $topic->getLanguageId(), true);

        $this->setViewPaginator($articles['count'], self::LIMIT);

        $this->view->topic = $topic;
        $this->view->articles = $articles['result'];
    }

    /**
     * Set view paginator
     *
     * @param int $count
     * @param int $perPage
     * @return void
     */
    private function setViewPaginator($count, $perPage)
    {
        $adapter = new Zend_Paginator_Adapter_Null($count);
        $paginator = new Zend_Paginator($adapter);
        Zend_Paginator::setDefaultScrollingStyle('Sliding');
        $paginator->setItemCountPerPage($perPage);
        $paginator->setCurrentPageNumber($this->page);
        $this->view->paginator = $paginator->getPages();
    }
}
