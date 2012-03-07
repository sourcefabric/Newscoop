<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Image\Rendition;

require_once($GLOBALS['g_campsiteDir']. '/classes/Plupload.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/ImageSearch.php');

/**
 * @Acl(ignore=True)
 */
class Admin_ImageController extends Zend_Controller_Action
{
    const LIMIT = 7;
    
    protected $renditions = array();

    public function init()
    {
        camp_load_translation_strings('article_images');

        $this->renditions = $this->_helper->service('image.rendition')->getRenditions();
        
        $this->_helper->contextSwitch()
            ->addActionContext('edit', 'json')
            ->addActionContext('set-rendition', 'json')
            ->addActionContext('remove-rendition', 'json')
            ->addActionContext('article-attach', 'json')
            ->addActionContext('set-attach', 'json')
            ->addActionContext('set-detach', 'json')
            ->addActionContext('upload', 'json')
            ->addActionContext('edit-image-data', 'json')
            ->initContext();

        $this->view->previewWidth = 150;
        $this->view->previewHeight = 150;
    }

    public function articleAction()
    {
        $this->_helper->layout->setLayout('iframe');
        $this->view->renditions = $this->renditions;
        $this->view->images = $this->_helper->service('image')->findByArticle($this->_getParam('article_number'));
        $this->view->articleRenditions = $this->_helper->service('image.rendition')->getArticleRenditions($this->_getParam('article_number'));
    }
    
    public function articleAttachAction()
    {
        $this->_helper->layout->setLayout('iframe');
        
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('paginator-hash.phtml');
        
        $page = $this->_getParam('page', 1);
        $count = $this->_helper->service('image')->getCountBy(array());
        $paginator = Zend_Paginator::factory($count);
        $paginator->setItemCountPerPage(self::LIMIT);
        $paginator->setCurrentPageNumber($page);
        $paginator->setView($this->view);
        $paginator->setDefaultScrollingStyle('Sliding');
        
        $this->view->paginator = $paginator;
        $this->view->article = $this->_getParam('article_number');
        
        $this->view->languageId = $this->_getParam('language_id');
        
        $this->view->articleImages = $this->_helper->service('image')->findByArticle($this->_getParam('article_number'));
        
        $this->view->q = '';
        if ($this->_getParam('q', false)) {
            $this->view->images = $this->_helper->service('image.search')->find($this->_getParam('q'));
            $this->view->q = $this->_getParam('q');
        }
        else {
            $this->view->images = $this->_helper->service('image')->findBy(array(), array('id' => 'desc'), self::LIMIT, ($paginator->getCurrentPageNumber() - 1) * self::LIMIT);
        }
    }
    
    public function setAttachAction()
    {        
        $this->_helper->layout->disableLayout();
        
        try {
            $articleNumber = $this->_getParam('article_number');
            $imageId = $this->_getParam('image_id');
            
            //$image = $this->_helper->service('image')->find($imageId);
            //$articleImage = $this->_helper->service('image')->addArticleImage($articleNumber, $image);
            
            ArticleImage::AddImageToArticle($imageId, $articleNumber);
            
            $this->view->articleImages = $this->_helper->service('image')->findByArticle($this->_getParam('article_number'));
        } catch (\InvalidArgumentException $e) {
            $this->view->exception= $e->getMessage();
        }
    }
    
    public function setDetachAction()
    {        
        $this->_helper->layout->disableLayout();
        
        try {
            $articleNumber = $this->_getParam('article_number');
            $imageId = $this->_getParam('image_id');
            $languageId = $this->_getParam('image_id');
            
            $article = new Article($languageId, $articleNumber);
            $image = new Image($imageId);
            $articleImage = new ArticleImage($articleNumber, $imageId);
            $articleImage->delete();
        } catch (\InvalidArgumentException $e) {
            $this->view->exception= $e->getMessage();
        }
    }
    
    public function uploadAction()
    {        
        $this->_helper->layout->disableLayout();
        
        global $Campsite;
        
        $files = Plupload::OnMultiFileUpload($Campsite['IMAGE_DIRECTORY']);
        //var_dump($files);
        die;
    }
    
    public function editImageDataAction()
    {
        if ($this->getRequest()->isPost()) {
            $data = $this->_getParam('data');
            
            if (is_array($data)) {
                foreach ($data as $id => $values) {
                    if (!empty($values['description']) || !empty($values['place']) || !empty($values['photographer'])) {
                        $image = $this->_helper->service('image')->find($id);
                
                        $image->setDescription($values['description']);
                        $image->setPlace($values['place']);
                        $image->setPhotographer($values['photographer']);
                        $image->setDate(date('Y-m-d'));
                    }
                }
                $this->_helper->entity->flushManager();
            }
            $next = $this->_getParam('edit_image_data_next');
            if ($next == 1) {
                $this->_helper->redirector('article', 'image', 'admin', array(
                    'article_number' => $this->_getParam('article_number')
                ));
            }
        }
        
        $this->view->article = $this->_getParam('article_number');
        $this->view->languageId = $this->_getParam('language_id');
        
        $this->_helper->layout->setLayout('iframe');
        
        $images = array();
        $articleImages = $this->_helper->service('image')->findByArticle($this->_getParam('article_number'));
        foreach ($articleImages as $k => $articleImage) {
			$image = $articleImage->getImage();
			if ($image->getDate() == '0000-00-00') {
				$images[] = $image;
			}
		}
        
        $this->view->images = $images;
    }

    public function setRenditionAction()
    {
        $this->_helper->layout->disableLayout();

        try {
            $rendition = $this->_helper->service('image.rendition')->getRendition($this->_getParam('rendition'));
            $image = $this->_helper->service('image')->getArticleImage($this->_getParam('article_number'), array_pop(explode('-', $this->_getParam('image'))));
            $articleRendition = $this->_helper->service('image.rendition')->setArticleRendition($this->_getParam('article_number'), $rendition, $image->getImage());
            $this->view->rendition = $this->view->rendition($rendition, $this->view->previewWidth, $this->view->previewHeight, $articleRendition);
        } catch (\InvalidArgumentException $e) {
            $this->view->exception= $e->getMessage();
        }
    }

    public function removeRenditionAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->service('image.rendition')->unsetArticleRendition($this->_getParam('article_number'), $this->_getParam('rendition'));
        $rendition = $this->renditions[$this->_getParam('rendition')];
        $renditions = $this->_helper->service('image.rendition')->getArticleRenditions($this->_getParam('article_number'));
        $this->view->rendition = $this->view->rendition($rendition, $this->view->previewWidth, $this->view->previewHeight, $renditions[$rendition]);
    }

    public function editAction()
    {
        $this->_helper->layout->setLayout('iframe');
        $rendition = $this->_helper->service('image.rendition')->getRendition($this->_getParam('rendition'));
        $renditions = $this->_helper->service('image.rendition')->getArticleRenditions($this->_getParam('article_number'));
        $image = $renditions[$rendition]->getImage();

        if ($this->getRequest()->isPost()) {
            $this->_helper->service('image.rendition')
                ->setArticleRendition($this->_getParam('article_number'), $rendition, $image, $this->getRequest()->getPost('coords'));
            $this->_helper->redirector('edit', 'image', 'admin', array(
                'article_number' => $this->_getParam('article_number'),
                'rendition' => $this->_getParam('rendition'),
            ));
        }

        $this->view->rendition = $renditions[$rendition]->getRendition();
        $this->view->image = $renditions[$rendition]->getImage();
        $this->view->renditions = $this->renditions;
        $this->view->articleRenditions = $this->_helper->service('image.rendition')->getArticleRenditions($this->_getParam('article_number'));
    }

    public function setDefaultImageAction()
    {
        $image = $this->_helper->service('image')->getArticleImage($this->_getParam('article_number'), $this->_getParam('default-image'));
        $this->_helper->service('image')->setDefaultArticleImage($this->_getParam('article_number'), $image);
        $this->_helper->redirector('article', 'image', 'admin', array(
            'article_number' => $this->_getParam('article_number'),
        ));
    }
}
