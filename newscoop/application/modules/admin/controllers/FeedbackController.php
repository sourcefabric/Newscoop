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
        $table->setCols(array(
            'index' => $view->toggleCheckbox(), 'user' => getGS('Author'),
            'message' => getGS('Date') . ' / ' . getGS('Message'), 'url' => getGS('URL')),
            array('index' => false)
        );
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
                        'name' => $user->getFirstName(),
                        'email' => $user->getEmail(),
                        'avatar' => (string)$view->getAvatar($user->getEmail(), array('img_size' => 50, 'default_img' => 'wavatar'))
                    ),
                    'message' => array(
                        'id' => $feedback->getId(),
                        'created' => array(
                            'date' => $feedback->getTimeCreated()->format('Y.m.d'),
                            'time' => $feedback->getTimeCreated()->format('H:i:s')
                        ),
                        'message' => $feedback->getMessage(),
                        'subject' => $feedback->getSubject()
                    ),
                    'url' => $url,
                    /*
                    array(
                       'url' => $url,
                       'link' => array('source' => $view->baseUrl("admin/articles/edit.php?") . $view->linkArticle($thread)),
                       'section' => array('name' => ($section) ? $section->getName() : null))
                    ); */
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
        $this->editForm->setSimpleDecorate()->setAction($this->_helper->url('update'));
        $this->view->editForm = $this->editForm;
    }
}
