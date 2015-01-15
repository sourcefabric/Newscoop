<?php
/**
 * @package Newscoop
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Topics controller
 */

class TopicController extends Zend_Controller_Action
{
    public function articlesAction()
    {
        $em = \Zend_Registry::get('container')->get('em');
        $topicId = $this->_getParam('id');
        $gimme = CampTemplate::singleton()->context();

        $language = $em->getRepository('Newscoop\Entity\Language')
            ->findOneByCode($this->_getParam('language'));
        $query = $em->getRepository('Newscoop\NewscoopBundle\Entity\Topic')
            ->getSingleTopicQuery($topicId, $language->getCode());

        $topic = $query->getOneOrNullResult();

        if (!$topic) {
            throw new \Exception('We can\'t find that topic');
        }

        $gimme->topic = new \MetaTopic($topic->getId());
        $this->view->topic = $gimme->topic;
    }
}
