<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Feedback controller
 */
 
use Newscoop\Entity\Feedback;

require_once($GLOBALS['g_campsiteDir'].'/include/captcha/php-captcha.inc.php');
require_once($GLOBALS['g_campsiteDir'].'/include/get_ip.php');
require_once($GLOBALS['g_campsiteDir']. '/classes/Plupload.php');

class FeedbackController extends Zend_Controller_Action
{
    public function init()
    {
		$this->getHelper('contextSwitch')->addActionContext('save', 'json')->initContext();
		$this->getHelper('contextSwitch')->addActionContext('upload', 'json')->initContext();
    }

    public function saveAction()
    {
		global $_SERVER;
		
		$this->_helper->layout->disableLayout();
		$parameters = $this->getRequest()->getParams();
		
		$errors = array();
		
		$auth = Zend_Auth::getInstance();
		
		$publication = new Publication($parameters['f_publication']);
		
		if ($auth->getIdentity()) {
			$acceptanceRepository = $this->getHelper('entity')->getRepository('Newscoop\Entity\Comment\Acceptance');
			$user = new User($auth->getIdentity());
			
			$userIp = getIp();
			if ($acceptanceRepository->checkParamsBanned($user->m_data['Name'], $user->m_data['EMail'], $userIp, $parameters['f_publication'])) {
				$errors[] = getGS('You have been banned from writing feedbacks.');
			}
		}
		else {
			$errors[] = getGS('You are not logged in.');
		}
		
		if (!array_key_exists('f_feedback_content', $parameters) || empty($parameters['f_feedback_content'])) {
			$errors[] = getGS('The feedback content was not filled in.');
		}
		
		if ($publication->isCaptchaEnabled()) {
			if (!PhpCaptcha::Validate($parameters['f_captcha'], true)) {
				$errors[] = getGS('The code you entered is not the same with the one shown in the image.');
			}
		}
		
		if (empty($errors)) {
			$feedbackRepository = $this->getHelper('entity')->getRepository('Newscoop\Entity\Feedback');
			$feedback = new Feedback();
			
			$values = array(
				'user' => $auth->getIdentity(),
				'publication' => $parameters['f_publication'],
				'section' => $parameters['f_section'],
				'article' => $parameters['f_article'],
				'subject' => $parameters['f_feedback_subject'],
				'message' => $parameters['f_feedback_content'],
				'url' => $parameters['f_feedback_url'],
				'time_created' => new DateTime(),
				'language' => $parameters['f_language'],
				'status' => 'pending'
			);
			
			$feedbackRepository->save($feedback, $values);
			$feedbackRepository->flush();
			
			if ($parameters['upload'] == 1) {
				$this->view->response = getGS('File is uploaded and your message is sent.');
			}
			else {
				$this->view->response = getGS('Your message is sent.');
			}
		}
		else {
			$errors = implode('<br>', $errors);
			$errors = getGS('Following errors have been found:') . '<br>' . $errors;
			$this->view->response = $errors;
		}
    }
    
    public function uploadAction()
    {
		global $Campsite;
		
		$auth = Zend_Auth::getInstance();
		$userId = $auth->getIdentity();
		
		$mimeType = $_FILES['file']['type'];
		echo($mimeType);die;
		$type = explode('/', $mimeType);
		$type = $type[0];
		
		if ($type == 'image') {
			$file = Plupload::OnMultiFileUploadCustom($Campsite['IMAGE_DIRECTORY']);
			$image = Image::ProcessFile($_FILES['file']['name'], $_FILES['file']['name'], $userId, array('Source' => 'feedback', 'Status' => 'Unpublished'));
			$this->view->response = 'OK';
		}
	}
    
    public function indexAction()
    {
		$this->view->param = $this->_getParam('switch');
	}
}
