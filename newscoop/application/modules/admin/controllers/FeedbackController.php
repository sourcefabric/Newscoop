<?php
/**
 * @package Newscoop
 * @subpackage Feedback 
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 *
 *
 */
use Newscoop\Entity\Feedback;

/**
 * @Acl(resource="feedback", action="view")
 */
class Admin_FeedbackController extends Zend_Controller_Action
{

    /** @var Newscoop\Entity\Repository\FeedbackRepository */
    private $feedbackRepository;

    /** @var Admin_Form_Feedback */
    //private $form;

    /** @var Admin_Form_Feedback_EditForm */
    //private $editForm;
    
    public function init()
    {
        // get feedback repository
        $this->feedbackRepository = $this->_helper->entity->getRepository('Newscoop\Entity\Feedback');

        //$this->form = new Admin_Form_Comment;
        //$this->editForm = new Admin_Form_Comment_EditForm;

        return $this;
    }

    public function indexAction()
    {
        $this->_forward('table');
    }

    /**
     * Action to make the table
     */
    public function tableAction()
    {
        $view = $this->view;
        $table = $this->getHelper('datatable');
        /* @var $table Action_Helper_Datatable */
        $table->setDataSource($this->feedbackRepository);
        $table->setOption('oLanguage', array('sSearch' => ''));
        $table->setCols(
            array(
                'index' => $view->toggleCheckbox(), 'user' => getGS('User'),
                'message' => getGS('Date') . ' / ' . getGS('Message'),
                'url' => getGS('Coming from')
            ),
            array('index' => false)
        );
        
        $index = 1;
        $acceptanceRepository = $this->_helper->entity->getRepository('Newscoop\Entity\Comment\Acceptance');
        $table->setHandle(function($feedback) use ($view, &$index, $acceptanceRepository)
            {
                $user = $feedback->getUser();
                $url = $feedback->getUrl();
                $message = $feedback->getMessage();
                $publication = $feedback->getPublication();
                $section = $feedback->getSection();
                $article = $feedback->getArticle();
                
                $banned = $acceptanceRepository->checkBanned(array('name' => $user->getName()), $publication);
                if ($banned['name'] == true) {
					$banned = true;
				} else {
					$banned = false;
				}
                
                $result = array(
                    'index' => $index++,
                    'user' => array(
                        'username' => $user->getUsername(),
                        'name' => $user->getFirstName(),
                        'email' => $user->getEmail(),
                        'avatar' => (string)$view->getAvatar($user->getEmail(), array('img_size' => 50, 'default_img' => 'wavatar')),
                        'banurl' => $view->url(array(
							'controller' => 'user',
							'action' => 'ban',
							'user' => $user->getId(),
							'publication' => $publication->getId()
						)),
						'unbanurl' => $view->url(array(
							'controller' => 'user',
							'action' => 'unban',
							'user' => $user->getId(),
							'publication' => $publication->getId()
						)),
						'is_banned' => $banned
                    ),
                    'message' => array(
                        'id' => $feedback->getId(),
                        'created' => array(
                            'date' => $feedback->getTimeCreated()->format('Y.m.d'),
                            'time' => $feedback->getTimeCreated()->format('H:i:s')
                        ),
                        'message' => $feedback->getMessage(),
                        'subject' => $feedback->getSubject(),
                        'status' => $feedback->getStatus(),
                        'action' => array(
                            'reply' => $view->url(array(
                                'action' => 'reply',
                                'format' => 'json'
                            ))
                        ),
                        'url' => $url,
                        'publication' => $publication->getName(),
                        'section' => ($section) ? $section->getName() : getGS('None'),
                        'article' => array(
							'name' => ($article) ? $article->getName() : getGS('None'),
							'url' => ($article) ? $view->baseUrl("admin/articles/get.php?") . $view->linkArticle($article) : $view->baseUrl("admin/feedback")
						)
                    )
                );
                return($result);
            });

        $table->setOption('fnDrawCallback', 'datatableCallback.draw')
                ->setOption('fnRowCallback', 'datatableCallback.row')
                ->setOption('fnServerData', 'datatableCallback.addServerData')
                ->setOption('fnInitComplete', 'datatableCallback.init')
                ->setOption('sDom','<"top">lf<"#actionExtender">rit<"bottom"ip>')
                ->setStripClasses()
                ->toggleAutomaticWidth(false)
                ->setDataProp(array('index' => null, 'user' => null, 'message' => null, 'url' => null))
                ->setClasses(array('index' => 'commentId', 'user' => 'commentUser', 'message' => 'commentTimeCreated', 'url' => 'commentThread'));
        $table->dispatch();
        //$this->editForm->setSimpleDecorate()->setAction($this->_helper->url('update'));
        //$this->view->editForm = $this->editForm;
    }

    /**
     * Status action
     */
    public function setStatusAction()
    {
        $this->getHelper('contextSwitch')->addActionContext('set-status', 'json')->initContext();
        if (!SecurityToken::isValid()) {
            $this->view->status = 401;
            $this->view->message = getGS('Invalid security token!');
            return;
        }

        $status = $this->getRequest()->getParam('status');
        $feedbacks = $this->getRequest()->getParam('feedback');
        if (!is_array($feedbacks)) {
            $feedbacks = array($feedbacks);
        }

        if ($status == 'deleted') {
            //$messages = array_unique(array_merge($messages, $this->getAllReplies($messages)));
        }

        try {
            foreach ($feedbacks as $id) {
                $feedback = $this->feedbackRepository->find($id);

                if ($status == 'deleted') {
                    /*
                    $msg = getGS('Comment delete by $1 from the article $2 ($3)', Zend_Registry::get('user')->getName(),
                                 $comment->getThread()->getName(), $comment->getLanguage()->getCode());

                    $this->_helper->log($msg);
                    $this->_helper->flashMessenger($msg);
                    */
                } else {
                    /*
                    $msg = getGS('Message $4 by $1 in the article $2 ($3)', Zend_Registry::get('user')->getName(),
                                 $comment->getThread()->getName(), $comment->getLanguage()->getCode(), $status);
                    $this->_helper->log($msg);
                    $this->_helper->flashMessenger($msg);
                    */
                }
            }
            $this->feedbackRepository->setStatus($feedbacks, $status);
            $this->feedbackRepository->flush();
        } catch (Exception $e) {
            $this->view->status = $e->getCode();
            $this->view->message = $e->getMessage();
            return;
        }
        $this->view->status = 200;
        $this->view->message = 'succcesful';
    }
    
    /**
     * Reply action
     */
    public function replyAction()
    {
		$this->getHelper('contextSwitch')->addActionContext('reply', 'json')->initContext();
		
		$auth = Zend_Auth::getInstance();
		$user = new User($auth->getIdentity());
		$fromEmail = $user->getEmail();
		
		$feedbackId = $this->getRequest()->getParam('parent');
		$subject = $this->getRequest()->getParam('subject');
		$message = $this->getRequest()->getParam('message');
		
		$feedback = $this->feedbackRepository->find($feedbackId);
		$user = $feedback->getUser();
		$toEmail = $user->getEmail();
		
		/*
		$configMail = array( 'auth' => 'login',
							 'username' => 'user@gmail.com',
							 'password' => 'password',
							 'ssl' => 'ssl',
							 'port' => 465
		);
		$mailTransport = new Zend_Mail_Transport_Smtp('smtp.gmail.com',$configMail);
		*/
		$mail = new Zend_Mail();
		$mail->setSubject($subject);
		$mail->setBodyText($message);
		$mail->setFrom($fromEmail);
		$mail->addTo($toEmail);
		try {
			$mail->send();
			$this->view->status = 200;
			$this->view->message = 'succcesful';
		}
		catch (Exception $e) {
			$this->view->status = 200;
			$this->view->message = 'succcesful?';
		}
	}
}
