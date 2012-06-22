<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Entity\Feedback;

require_once($GLOBALS['g_campsiteDir']. '/classes/Plupload.php');
require_once($GLOBALS['g_campsiteDir'].'/include/get_ip.php');

/**
 * Feedback resource controller
 */
class Api_FeedbackController extends Zend_Controller_Action
{
    const PUBLICATION = 5;
    const LANGUAGE = 5;
    
    /** @var Zend_Controller_Request_Http */
    private $request;

    /**
     *
     */
    public function init()
    {
        global $Campsite;

        $this->_helper->layout->disableLayout();
        $this->request = $this->getRequest();
        $this->url = $Campsite['WEBSITE_URL'];
    }

    /**
     *
     */
    public function indexAction()
    {
        global $Campsite;
        
        $this->getHelper('contextSwitch')->addActionContext('list', 'json')->initContext('json');
        $acceptanceRepository = $this->getHelper('entity')->getRepository('Newscoop\Entity\Comment\Acceptance');

        $params = $this->request->getPost();
        if (!isset($params['username']) || !isset($params['password'])) {
            $this->getResponse()->setHttpResponseCode(401);
        }
        else {
            $user = $this->_helper->service('user')->findOneBy(array('username' => $params['username']));
            if (!$user || !$user->checkPassword($params['password'])) {
                $this->getResponse()->setHttpResponseCode(401);
            }
            else {
                $userId = $user->getId();
                $user = new User($userId);
                $userIp = getIp();
                if ($acceptanceRepository->checkParamsBanned($user->m_data['Name'], $user->m_data['EMail'], $userIp, self::PUBLICATION)) {
                    $this->getResponse()->setHttpResponseCode(401);
                }
                else {
                    if (!array_key_exists('message', $params) || empty($params['message'])) {
                        $this->getResponse()->setHttpResponseCode(500);
                    }
                    else {
                        $feedbackRepository = $this->getHelper('entity')->getRepository('Newscoop\Entity\Feedback');
                        $feedback = new Feedback();

                        $values = array(
                            'user' => $userId,
                            'publication' => self::PUBLICATION,
                            'section' => '',
                            'article' => '',
                            'subject' => $params['subject'],
                            'message' => $params['message'],
                            'url' => 'API',
                            'time_created' => new DateTime(),
                            'language' => self::LANGUAGE,
                            'status' => 'pending',
                            'attachment_type' => 'none',
                            'attachment_id' => 0
                        );
                        
                        
                        if (!empty($_FILES)) {
                            $_FILES['image_data']['name'] = preg_replace('/[^\w\._]+/', '', $_FILES['image_data']['name']);
                            
                            $file = Plupload::OnMultiFileUploadCustom($Campsite['IMAGE_DIRECTORY']);
                            $image = Image::ProcessFile($_FILES['image_data']['name'], $_FILES['image_data']['name'], $userId, array('Source' => 'feedback', 'Status' => 'Unapproved'));
                            
                            $values['attachment_type'] = 'image';
                            $values['attachment_id'] = $image->getImageId();
                        }
                        
                        $feedbackRepository->save($feedback, $values);
                        $feedbackRepository->flush();
                        
                        $this->sendMail($values);

                        $this->getResponse()->setHttpResponseCode(200);
                        die;
                    }
                }
            }
        }
    }

    /**
     * @param array $values
     */
    public function sendMail($values)
    {
        $toEmail = 'dienstpult@tageswoche.ch';
        
        $userRepository = $this->getHelper('entity')->getRepository('Newscoop\Entity\User');
        $user = $userRepository->find($values['user']);

        $fromEmail = $user->getEmail();
        
        $message = $values['message'];
        $message = $message.'<br>Von <a href="http://www.tageswoche.ch/user/profile/'.$user->getUsername().'">'.$user->getUsername().'</a> ('.$user->getRealName().')';
        $message = $message.'<br>Gesendet von: <a href="'.$values['url'].'">'.$values['url'].'</a>';
        
        $mail = new Zend_Mail('utf-8');
        
        $mail->setSubject('Leserfeedback: '.$values['subject']);
        $mail->setBodyHtml($message);
        $mail->setFrom($fromEmail);
        $mail->addTo($toEmail);
        
        if ($values['attachment_type'] == 'image') {
            $item = new Image($values['attachment_id']);
            $location = $item->getImageStorageLocation();
            $contents = file_get_contents($location);
            
            $filename = $item->getImageFileName();
            $tempFilename = explode('.', $filename);
            $extension = $tempFilename[count($tempFilename) - 1];
            
            $at = new Zend_Mime_Part($contents);
            if ($extension == 'gif') $at->type = 'image/gif';
            if ($extension == 'jpg' || $extension == 'jpeg') $at->type = 'image/jpeg';
            if ($extension == 'png') $at->type = 'image/png';
            $at->disposition = Zend_Mime::DISPOSITION_INLINE;
            $at->encoding    = Zend_Mime::ENCODING_BASE64;
            $at->filename    = $filename;
             
            $mail->addAttachment($at);
        }
        else if ($values['attachment_type'] == 'document') {
            $item = new Attachment($values['attachment_id']);
            $location = $item->getStorageLocation();
            $contents = file_get_contents($location);
            
            $filename = $item->getFileName();
            
            $at = new Zend_Mime_Part($contents);
            $at->type = 'application/pdf';
            $at->disposition = Zend_Mime::DISPOSITION_INLINE;
            $at->encoding    = Zend_Mime::ENCODING_BASE64;
            $at->filename    = $filename;
             
            $mail->addAttachment($at);
        }
        
        try {
			$mail->send();
		}
		catch (Exception $e) {
		}
        echo(' ');
    }
}