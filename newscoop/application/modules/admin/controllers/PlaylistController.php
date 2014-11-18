<?php
/**
 * @package Newscoop
 * @subpackage Subscriptions
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Annotations\Acl;
use Newscoop\Entity\Playlist;
use Newscoop\EventDispatcher\Events\GenericEvent;
use Newscoop\Utils\Exception;

/**
 * PlaylistController
 * @Acl(resource="playlist", action="manage")
 */
class Admin_PlaylistController extends Zend_Controller_Action
{
    /**
     * @var Newscoop\Entity\Repository\PlaylistRepository
     */
    private $playlistRepository = NULL;

    /**
     * @var Newscoop\Entity\Repository\PlaylistArticleRepository
     */
    private $playlistArticleRepository = NULL;

    /**
     * @var Newscoop\Services\Resource\ResourceId
     */

    public function init()
    {

        $this->playlistRepository = $this->_helper->entity->getRepository('Newscoop\Entity\Playlist');
        $this->playlistArticleRepository = $this->_helper->entity->getRepository('Newscoop\Entity\PlaylistArticle');
        $this->_helper->contextSwitch
            ->addActionContext( 'list-data', 'json' )
            ->addActionContext( 'save-data', 'json' )
            ->addActionContext( 'delete', 'json' )
            ->initContext();
    }

    /**
     * Playlist admin landing screen
     */
    public function indexAction()
    {
        $this->view->playlists = $this->playlistRepository->findAll();
    }

    public function popupAction()
    {
        $this->_helper->layout->setLayout('iframe');

        // TODO make a service
        $playlistId = $this->_request->getParam('id', null);
        $playlist = null;

        if (is_numeric($playlistId)) {
            $playlist = $this->playlistRepository->find($playlistId);
        } else {
            $playlist = new Playlist();
        }

        if ($playlist instanceof \Newscoop\Entity\Playlist) {
            $this->view->playlistName = $playlist->getName();
            $this->view->playlistId = $playlist->getId();
        }
    }

    /**
     * @Acl(resource="playlist", action="manage")
     */
    public function articleAction()
    {
        $articleRepo = $this->_helper->entity->getRepository('Newscoop\Entity\Article');
        $this->view->article = current( $articleRepo->findBy( array( "number" => $this->_getParam('id')) ) );
        $this->view->playlists = $this->playlistRepository->findAll();
        $this->_helper->layout->setLayout('iframe');
    }

    public function deleteAction()
    {
        $id = $this->_request->getParam('id');
        $this->view->id = $id;
        $this->playlistRepository->delete($this->playlistRepository->find($id));
        $this->_helper->service->getService('dispatcher')
            ->dispatch('playlist.delete', new GenericEvent($this, array(
                'id' => $id
            )));
    }

    public function listDataAction()
    {
        $playlist = new Playlist();
        $playlistId = $this->_request->getParam('id', null);
        if (is_numeric($playlistId)) {
            $playlist->setId($playlistId);
            $this->view->items = $this->playlistRepository->articles($playlist, null, false, null, null, false);
            $this->view->code = 200;
        }
    }

    public function saveDataAction()
    {   
        $translator = \Zend_Registry::get('container')->getService('translator');
        $playlistId = $this->_request->getParam('id', null);
        $playlist = null;
        $playlistName = $this->_request->getParam('name', '');
        // TODO make a service
        if (is_numeric($playlistId)) {
            $playlist = $this->playlistRepository->find($playlistId);
            if (!is_null($playlist) && trim($playlistName)!='') {
                $playlist->setName($playlistName);
            }
        } else {
            $playlist = new Playlist();
            $playlist->setName(trim($playlistName)!='' ? $playlistName:$translator->trans('Playlist', array(), 'articles').strftime('%F') );
        }

        $playlist = $this->playlistRepository->save($playlist, $this->_request->getParam('articles'));
        if (!($playlist instanceof \Exception)) {
            $this->_helper->service->getService('dispatcher')
                ->dispatch('playlist.save', new GenericEvent($this, array(
                    'id' => $playlist->getId()
                )));

            $this->view->playlistId = $playlist->getId();
            $this->view->playlistName = $playlist->getName();

            $cacheService = \Zend_Registry::get('container')->getService('newscoop.cache');
            $cacheService->clearNamespace('boxarticles');

        } else {
            $this->view->error = $playlist->getFile().":".$playlist->getLine()." ".$playlist->getMessage();
        }
    }

    public function articlePreviewAction()
    {
        $articleId = $this->_getParam('id');
        $languageId = $this->_getParam('lang');
        $article = new Article($languageId, $articleId);
        $this->_helper->redirector->gotoUrl(
            $this->view->baseUrl("admin/articles/get.php?") . $this->view->linkArticleObj($article),
            array('prependBase' => false)
        );
        
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout(true);
    }
}
