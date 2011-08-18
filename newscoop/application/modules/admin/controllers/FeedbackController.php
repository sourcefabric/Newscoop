<?php

/**
 * @package Newscoop
 * @subpackage Subscriptions
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
    private $form;

    /** @var Admin_Form_Feedback_EditForm */
    private $editForm;
    
    public function init()
    {
        // get feedback repository
        $this->feedbackRepository = $this->_helper->entity->getRepository('Newscoop\Entity\Feedback');

        $this->form = new Admin_Form_Comment;

        $this->editForm = new Admin_Form_Comment_EditForm;

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
        $table->setOption('oLanguage',array('sSearch'=>''));
        $table->setCols(array('index' => $view->toggleCheckbox(), 'user' => getGS('Author'),
                             'message' => getGS('Date') . ' / ' . getGS('Message'), 'url' => getGS('URL')),
                              array('index' => false));
        $index = 1;
        $table->setHandle(function($feedback) use ($view, &$index)
            {
                /* var Newscoop\Entity\Comment\Commenter */
                $user = $feedback->getSubscriber();
                $url = $feedback->getUrl();
                $message = $feedback->getMessage();
                $result = array(
					'index' => $index++,
                    'user' => array(
						'username' => $user->getUsername(),
						'name' => $user->getName(),
						'email' => $user->getEmail(),
						'avatar' => (string)$view->getAvatar($user->getEmail(), array('img_size' => 50, 'default_img' => 'wavatar'))
					),
                    'message' => array(
						'id' => $feedback->getId(),
						'created' => array(
							'date' => $feedback->getTimeCreated()->format('Y.i.d'),
                            'time' => $feedback->getTimeCreated()->format('H:i:s')),
                            'message' => $comment->getMessage(),
                            'action' => array(
								'update' => $view->url(array('action' => 'update', 'format' => 'json')),
								'reply' => $view->url(array('action' => 'reply', 'format' => 'json')))
                    )
				);
                                               
                return($result);
            });

        $table->setOption('fnDrawCallback', 'datatableCallback.draw')
                ->setOption('fnRowCallback', 'datatableCallback.row')
                ->setOption('fnServerData', 'datatableCallback.addServerData')
                ->setOption('fnInitComplete', 'datatableCallback.init')
                ->setOption('sDom','<"top">f<"#actionExtender">lrt<"bottom"ip>')
                ->setStripClasses()
                ->toggleAutomaticWidth(false)
                ->setClasses(array('index' => 'commentId', 'commenter' => 'commentUser', 'comment' => 'commentTimeCreated', 'thread' => 'commentThread'));
        $table->dispatch();
        $this->editForm->setSimpleDecorate()->setAction($this->_helper->url('update'));
        $this->view->editForm = $this->editForm;
    }
}