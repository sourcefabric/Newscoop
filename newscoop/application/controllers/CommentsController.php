<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Comments controller
 */
 
use Newscoop\Entity\Comment;

require_once($GLOBALS['g_campsiteDir'].'/include/captcha/php-captcha.inc.php');
 
class CommentsController extends Zend_Controller_Action
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
		
		$article = new Article($this->view->params['f_language'], $this->view->params['f_article_number']);
		$publication = new Publication($article->getPublicationId());
		
		if (!$auth->getIdentity()) {
			$errors[] = getGS('You are not logged in.');
		}
		if (!array_key_exists('f_comment_subject', $this->view->params) || empty($this->view->params['f_comment_subject'])) {
			$errors[] = getGS('The comment subject was not filled in.');
		}
		if (!array_key_exists('f_comment_content', $this->view->params) || empty($this->view->params['f_comment_content'])) {
			$errors[] = getGS('The comment content was not filled in.');
		}
		
		if ($publication->isCaptchaEnabled()) {
			if (!PhpCaptcha::Validate($this->view->params['f_captcha'], true)) {
				$errors[] = getGS('The code you entered is not the same with the one shown in the image.');
			}
		}
		
		if (empty($errors)) {
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
			
			$this->view->response = getGS('Comment added.');
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
