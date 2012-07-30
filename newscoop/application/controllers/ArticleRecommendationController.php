<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

class ArticleRecommendationController extends Zend_Controller_Action
{
    public function init()
    {
        
    }

    public function indexAction()
    {
        $this->view->articleNumber = $this->_getParam('article_number');
        
        $user = Zend_Registry::get('container')->getService('user')->getCurrentUser();
        if ($user) {
            $this->view->user = $user;
            $this->view->userRealName = $user->getRealName();
            $this->view->userName = $user->getUsername();
        }
    }
    
    public function sendAction()
    {
        $request = $this->getRequest();
        
        if ($request->isPost()) {
            $parameters = $request->getPost();
            $user = Zend_Registry::get('container')->getService('user')->getCurrentUser();
            
            if ($user) {
                $parameters['sender_email'] = $user->getEmail();
                $parameters['sender_name'] = $user->getRealName();
            }
            
            $articles = $this->_helper->service('article')->findBy(array('number' => $parameters['article_number']));
            $article = $articles[0];
            $link = $this->_helper->service('link')->getLinkCanonical($article);

            $body = '';
            $body .= $parameters['message']."<br><br>";
            $body .= $article->getName()."<br>";
            try {
                $body .= $article->getType() === 'newswire' ? $article->getData('DataLead') : $article->getData('lede');
                $body .= '<br /><br />';
            } catch (\Exception $e) { // no lead for given article type
            }

            $body .= "Lesen: <a href='".$link."'>".$link."</a>"."<br><br>";
            $body .= "Diese Email wurde von ".$parameters['sender_name']." via tageswoche.ch versandt.<br>";
            
            try {
                $mail = new \Zend_Mail('utf-8');
                $mail->setSubject('Lesetipp: '.$article->getName());
                $mail->setBodyHtml($body);
                $mail->setFrom($parameters['sender_email']);
                $mail->addTo($parameters['recipient_email']);
                $mail->send();
            } catch (\Exception $e) {
                $this->view->error = 'Email invalid.';
                $this->_helper->redirector('index');
            }
        }
    }
}
