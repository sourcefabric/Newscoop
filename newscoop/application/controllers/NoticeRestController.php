<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */
use \Newscoop\Entity\Notice as Notice;

class NoticeRestController extends Zend_Controller_Action
{
    private $em;
    private $noticeRepo;

    public function init()
    {
        $this->em = $this->_helper->service('em');
        $this->noticeRepo = $this->em->getRepository('Newscoop\\Entity\\Notice');
    }

    public function headAction()
    {
    }

    public function deleteAction()
    {
    }

    public function indexAction()
    {
        $query = $this->getRequest()->getParam('q', null);

        if (isset($query) && !empty($query)) {
            $queryParts = explode('/', $query);
        } else {
            $queryParts = array();
        }

        $noticeCollection = $this->noticeRepo->getNotices(2, $queryParts);
        $result = array('status' => 'ok', 'data' => array('notices' => $noticeCollection));
        $this->_helper->json($result, true);
    }

    public function categoryAction()
    {
        $categoryRepo = $this->em->getRepository('Newscoop\Entity\NoticeCategory');
        $rootNodes = $categoryRepo->getRootNodes();

        $trees = array();
        foreach ($rootNodes as $node) {
            $leafNodes = $categoryRepo->getLeafs($node, 'lft');
            $children = array();
                foreach($leafNodes as $leaf){
                    $children[] = array('title' => $leaf->getTitle(),'id' => $leaf->getId());
                }
            $trees['categories'][] = array('title' => $node->getTitle(), 'children' => $children);
        }

        $this->_helper->json(array('status' => 'ok', 'data' => $trees), true);
    }

    public function putAction()
    {
    }

    public function postAction()
    {
    }
}
