<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Feedback controller
 */
 
//use Newscoop\Entity\Feedback;

require_once($GLOBALS['g_campsiteDir'].'/include/captcha/php-captcha.inc.php');
 
class FeedbackController extends Zend_Controller_Action
{
    public function init()
    {
		$this->getHelper('contextSwitch')->addActionContext('save', 'json')->initContext();
    }

    public function saveAction()
    {
		global $_SERVER;
		
		$this->_helper->layout->disableLayout();
		$this->view->params = $this->getRequest()->getParams();
		
		$errors = array();
		
		$auth = Zend_Auth::getInstance();
		
		$publication = new Publication($this->view->params['f_publication_id']);
		
		if (!$auth->getIdentity()) {
			$errors[] = getGS('You are not logged in.');
		}
		if (!array_key_exists('f_feedback_content', $this->view->params) || empty($this->view->params['f_feedback_content'])) {
			$errors[] = getGS('The feedback content was not filled in.');
		}
		
		if ($publication->isCaptchaEnabled()) {
			if (!PhpCaptcha::Validate($this->view->params['f_captcha'], true)) {
				$errors[] = getGS('The code you entered is not the same with the one shown in the image.');
			}
		}
		
		if (empty($errors)) {
			/*
			$commentRepository = $this->getHelper('entity')->getRepository('Newscoop\Entity\Comment');
			$comment = new Comment();
			
			$values = array(
				'user' => $auth->getIdentity(),
				'name' => $this->view->params['f_comment_nickname'],
				'subject' => $this->view->params['f_comment_subject'],
				'message' => $this->view->params['f_comment_content'],
				'language' => $this->view->params['f_language'],
				'thread' => $this->view->params['f_article_number'],
				'ip' => $_SERVER['REMOTE_ADDR'],
				'status' => 'approved',
				'time_created' => new DateTime()
			);
			
			$commentRepository->save($comment, $values);
			$commentRepository->flush();
			*/
			
			$this->view->response = getGS('Your message is sent.');
		}
		else {
			$errors = implode('<br>', $errors);
			$errors = getGS('Following errors have been found:') . '<br>' . $errors;
			$this->view->response = $errors;
		}
    }
    
    public function indexAction()
    {
		$this->view->param = $this->_getParam('switch');
	}
}
