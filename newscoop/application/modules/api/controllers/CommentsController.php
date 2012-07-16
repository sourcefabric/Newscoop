<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Entity\Comment;

require_once __DIR__ . '/AbstractController.php';
require_once __DIR__ . '/../../../../include/get_ip.php';

/**
 */
class Api_CommentsController extends AbstractController
{
    const LANGUAGE = 5;
    const LIST_LIMIT = 20;

    /** @var Zend_Controller_Request_Http */
    private $request;

    /** @var Newscoop\Services\CommentService */
    private $service;

    /** @var integer */
    private $rank = 1;

    /**
     */
    public function init()
    {
        $this->_helper->layout->disableLayout();
        $this->request = $this->getRequest();
        $this->service = $this->_helper->service('comment');
    }

    /**
     * Routes depending on request type.
     */
    public function indexAction()
    {
        $action = $this->request->isPost() ? 'compose' : 'list';
        $this->_forward($action);
    }

    /**
     * Lists comments.
     *
     * If article identifier given, returns all comments posted for the article,
     * otherwise returns the latest 20 posted comments.
     */
    public function listAction()
    {
        $this->getHelper('contextSwitch')->addActionContext('list', 'json')->initContext();

        $id = $this->request->getParam('article_id');
        if (is_null($id)) {
            $comments = $this->service->findBy(array('status' => 0), array('time_created' => 'desc'), self::LIST_LIMIT);
        } else {
            $comments = $this->service->findBy(array('article_num' => $id, 'status' => 0), array('time_created' => 'desc'));
        }

        $response = array();
        foreach($comments as $comment) {
            $created = $comment->getTimeCreated()->format('Y-m-d H:i:s');
            $modified = $created;
            if ($comment->getTimeUpdated()->getTimestamp() !== false && $comment->getTimeCreated()->getTimestamp() < $comment->getTimeUpdated()->getTimestamp()) {
                $modified = $comment->getTimeUpdated()->format('Y-m-d H:i:s');
            }

            $profileImage = $comment->getCommenter()->getUser()->getImage();
            if (!empty($profileImage)) {
                $profileImage = $this->view->serverUrl($this->view->url(array(
                    'src' => $this->getHelper('service')->getService('image')->getSrc('images/' . $profileImage, 125, 125, 'fit'),
                ), 'image', false, false));
            }

            $response[] = array(
                'author_name' => $comment->getCommenterName(),
                'author_image_url' => $profileImage,
                'public_profile_url' => $this->view->serverUrl($this->view->url(array(
                    'module' => 'api',
                    'controller' => 'profile',
                    'action' => 'public',
                ), 'default') . $this->getApiQueryString(array(
                   'user_id' => $comment->getCommenter()->getUser()->getId(),
                ))),
                'subject' => $comment->getSubject(),
                'message'=> $comment->getMessage(),
                'recommended' => $comment->getRecommended() ? true : false,
                'created_time' => $created,
                'last_modified' => $modified,
                'rank' => $this->rank++,
            );
        }

        $this->_helper->json($response);
    }
    
    /**
     */
    public function composeAction()
    {
        $this->getHelper('contextSwitch')->addActionContext('list', 'json')->initContext('json');

        $parameters = $this->getRequest()->getPost();
        if (isset($parameters['username']) && isset($parameters['password'])) {
            $user = $this->_helper->service('user')->findOneBy(array('username' => $parameters['username']));
            if ($user->checkPassword($parameters['password'])) {
                if (isset($parameters['article_id']) && isset($parameters['message'])) {
                    $acceptanceRepository = $this->getHelper('entity')->getRepository('Newscoop\Entity\Comment\Acceptance');
                    $userIp = getIp();
                    
                    $article = new Article(self::LANGUAGE, $parameters['article_id']);
                    
                    if (!$acceptanceRepository->checkParamsBanned($user->getName(), $user->getEmail(), $userIp, $article->getPublicationId())) {
                        $commentRepository = $this->getHelper('entity')->getRepository('Newscoop\Entity\Comment');
                        $comment = new Comment();
                        
                        $subject = '';
                        if (isset($parameters['subject'])) {
                            $subject = $parameters['subject'];
                        }
                        
                        $values = array(
                            'user' => $user->getId(),
                            'name' => '',
                            'subject' => $subject,
                            'message' => $parameters['message'],
                            'language' => self::LANGUAGE,
                            'thread' => $parameters['article_id'],
                            'ip' => $_SERVER['REMOTE_ADDR'],
                            'status' => 'approved',
                            'time_created' => new DateTime(),
                            'recommended' => '0'
                        );
                        
                        $commentRepository->save($comment, $values);
                        $commentRepository->flush();
                        
                        //echo('comment posted');
                        $this->getResponse()->setHttpResponseCode(201);
                    }
                    else {
                        //echo('not allowed to comment');
                        $this->getResponse()->setHttpResponseCode(500);
                    }
                }
                else {
                    //echo('no article and message');
                    $this->getResponse()->setHttpResponseCode(500);
                }
            }
            else {
                //echo('username password wrong');
                $this->getResponse()->setHttpResponseCode(401);
            }
        }
        else {
            //echo('no username password');
            $this->getResponse()->setHttpResponseCode(401);
        }
        
        $this->_helper->json(array());
    }
}
