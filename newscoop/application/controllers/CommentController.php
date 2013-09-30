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
require_once($GLOBALS['g_campsiteDir'].'/include/get_ip.php');

class CommentController extends Zend_Controller_Action
{
    public function init()
    {
		$this->getHelper('contextSwitch')->addActionContext('save', 'json')->initContext();
    }

    public function saveAction()
    {
		global $_SERVER;

        $translator = Zend_Registry::get('container')->getService('translator');
		$this->_helper->layout->disableLayout();
		$parameters = $this->getRequest()->getParams();
        
        $errors = array();

		$auth = Zend_Auth::getInstance();

		$article = new Article($parameters['f_language'], $parameters['f_article_number']);
		$publication = new Publication($article->getPublicationId());

		if ($auth->getIdentity()) {
			$acceptanceRepository = $this->getHelper('entity')->getRepository('Newscoop\Entity\Comment\Acceptance');
            $userRepository = $this->getHelper('entity')->getRepository('Newscoop\Entity\User');
            
            $user = $userRepository->find($auth->getIdentity());

			$userIp = getIp();
            if ($acceptanceRepository->checkParamsBanned($user->getName(), $user->getEmail(), $userIp, $article->getPublicationId())) {
				$errors[] = $translator->trans('You have been banned from writing comments.');
			}
		}
		else {
			$errors[] = $translator->trans('You are not logged in.');
		}

		if (!array_key_exists('f_comment_subject', $parameters) || empty($parameters['f_comment_subject'])) {
			//$errors[] = $translator->trans('The comment subject was not filled in.');
			$errors[] = $translator->trans('The comment subject was not filled in.');
		}
		if (!array_key_exists('f_comment_content', $parameters) || empty($parameters['f_comment_content'])) {
			$errors[] = $translator->trans('The comment content was not filled in.');
		}

		if (empty($errors)) {
			$commentRepository = $this->getHelper('entity')->getRepository('Newscoop\Entity\Comment');
			$comment = new Comment();

			$values = array(
				'user' => $auth->getIdentity(),
				'name' => $parameters['f_comment_nickname'],
				'subject' => $parameters['f_comment_subject'],
				'message' => $parameters['f_comment_content'],
				'language' => $parameters['f_language'],
				'thread' => $parameters['f_article_number'],
				'ip' => $this->getRequest()->getClientIp(),
				'status' => 'approved',
				'time_created' => new DateTime(),
                'recommended' => '0'
			);

			$commentRepository->save($comment, $values);
            $commentRepository->flush();
            
            $this->view->response = 'OK';
		}
		else {
			$errors = implode('<br>', $errors);
			$errors = $translator->trans('Following errors have been found:') . '<br>' . $errors;
			$this->view->response = $errors;
		}
        
        $this->getHelper('contextSwitch')->addActionContext('save', 'json')->initContext();
    }

    public function indexAction()
    {
		$this->view->param = $this->_getParam('switch');
	}
}
