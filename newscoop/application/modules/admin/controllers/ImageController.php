<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Annotations\Acl;

require_once($GLOBALS['g_campsiteDir']. '/classes/Plupload.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/ImageSearch.php');

/**
 * @Acl(ignore=True)
 */
class Admin_ImageController extends Zend_Controller_Action
{
    const LIMIT = 24;

    protected $renditions = array();

    public function init()
    {
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

        $page = (int) $this->_getParam('page', 1);
        if (1 > $page) {
            $page = 1;
        }

        $search_paging = array(
            'length' => self::LIMIT,
            'offset' => ($page - 1) * self::LIMIT,
        );

        $term = $this->_getParam('term');
        $em = \Zend_Registry::get('container')->getService('em');
        if (!empty($term)) {
            $qb = $em->getRepository('Newscoop\Image\LocalImage')
                ->createQueryBuilder('i');

            $users = $qb->select('i.id', 'u.id', 'u.username')
                ->leftJoin('i.user', 'u')
                ->where($qb->expr()->like('u.username', $qb->expr()->literal('%'.$term.'%')))
                ->groupBy('u.id')
                ->setMaxResults(25)
                ->getQuery()
                ->getArrayResult();

            echo json_encode(array('items' => $users));die;
        }

        $source_criteria = array('source' => array('local', 'feedback'));
        $uploaderId = $this->_getParam('uploader', '');
        $newsfeed = $this->_getParam('newsfeed', 0);

        if ($newsfeed) {
            $source_criteria = array();
        }

        $count = $this->_helper->service('image')->getCountBy(array());
        $this->view->q = $this->_getParam('search', '');
        if (is_array($this->view->q)) {
            $this->view->q = $this->view->q[0];
        }

        $paginator = Zend_Paginator::factory($count);
        $paginator->setItemCountPerPage(self::LIMIT);
        $paginator->setCurrentPageNumber($page);
        $paginator->setView($this->view);
        $paginator->setDefaultScrollingStyle('Sliding');
        $this->view->paginator = $paginator;

        if (!empty($this->view->q)) {
            $search_count = 0;
            $this->view->images = $this->_helper->service('image.search')->find($this->view->q, $source_criteria, array('id' => 'desc'), $search_paging, $search_count);
            $count = $search_count;
        } else if (!empty($uploaderId)) {
            $this->view->images = $this->_helper->service('image.search')->find($uploaderId, $source_criteria, array('id' => 'desc'), $search_paging);
        } else {
            $count = $this->_helper->service('image')->getCountBy($source_criteria);
            $this->view->images = $this->_helper->service('image')->findBy($source_criteria, array('id' => 'desc'), self::LIMIT, ($paginator->getCurrentPageNumber() - 1) * self::LIMIT);
        }

        $this->view->newsfeed = false;
        if ($newsfeed) {
            $this->view->newsfeed = true;
        }

        $this->view->article = (int) $this->_getParam('article_number', 0);
        $this->view->languageId = (int) $this->_getParam('language_id', 0);
        $this->view->articleImages = $this->_helper->service('image')->findByArticle((int) $this->_getParam('article_number', 0));
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
                        if ($values['date'] && $values['date'] != '0000-00-00') {
                            $image->setDate($values['date']);
                        }
                        else {
                            $image->setDate(date('Y-m-d'));
                        }
                    }
                }
                $this->_helper->entity->flushManager();
            }
            $parameters = $this->getRequest()->getParams();
            $next = $parameters['next'];
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
            unset($exifDate);
            unset($iptcDate);
            unset($iptcPlace);
            unset($iptcPhotographer);
            unset($iptcDescription);

            $image = $articleImage->getImage();
            $allowedExtensions = array('jpg', 'jpeg', 'tiff', 'tif');
            $imagePathParts = explode('.', $image->getPath());
            $imageExtension = strtolower($imagePathParts[count($imagePathParts) - 1]);

            if (in_array($imageExtension, $allowedExtensions)) {
                $exif = @exif_read_data($image->getPath());
                if (isset($exif['DateTime'])) {
                    $exifDate = date('Y-m-d', strtotime($exif['DateTime']));
                }

                $size = getimagesize($image->getPath(), $info);
                $iptc = array();
                foreach ($info as $key => $value) {
                    $iptc[$key] = iptcparse($value);
                }
                if (isset($iptc['APP13'])) {
                    $iptc = $iptc['APP13'];
                }
                if (isset($iptc['2#055'])) {
                    $iptcDate = $iptc['2#055'][0];
                    $iptcDate = date('Y-m-d', strtotime($iptcDate));
                }
                if (isset($iptc['2#080'])) {
                    $iptcPhotographer = $iptc['2#080'][0];
                }
                if (isset($iptc['2#120'])) {
                    $iptcDescription = $iptc['2#120'][0];
                }
                if (isset($iptc['2#090']) || isset($iptc['2#092']) || isset($iptc['2#101'])) {
                    $iptcPlace = array();
                    if (isset($iptc['2#101'])) {
                        $iptcPlace[] = $iptc['2#101'][0];
                    }
                    if (isset($iptc['2#090'])) {
                        $iptcPlace[] = $iptc['2#090'][0];
                    }
                    if (isset($iptc['2#092'])) {
                        $iptcPlace[] = $iptc['2#092'][0];
                    }
                    $iptcPlace = implode(', ', $iptcPlace);
                }
            }

            if ($image->getDescription() == '') {
                if (isset($iptcPhotographer)) {
                    $image->setPhotographer($iptcPhotographer);
                }
                if (isset($iptcDescription)) {
                    $image->setDescription($iptcDescription);
                }
                if (isset($iptcPlace)) {
                    $image->setPlace($iptcPlace);
                }
                if (isset($exifDate)) {
                    $image->setDate($exifDate);
                }
                if (isset($iptcDate)) {
                    $image->setDate($iptcDate);
                }

                $images[] = $image;
            } elseif ($this->_getParam('force_edit')) {

                $images[] = $image;
            }
        }


        $this->view->images = $images;
    }

    public function setRenditionAction()
    {
        $translator = \Zend_Registry::get('container')->getService('translator');
        $this->_helper->layout->disableLayout();

        try {
            $rendition = $this->_helper->service('image.rendition')->getRendition($this->_getParam('rendition'));
            $image = $this->_helper->service('image')->getArticleImage($this->_getParam('article_number'), array_pop(explode('-', $this->_getParam('image'))));
            $articleRendition = $this->_helper->service('image.rendition')->setArticleRendition($this->_getParam('article_number'), $rendition, $image->getImage());
            $this->view->rendition = $this->view->rendition($rendition, $this->view->previewWidth, $this->view->previewHeight, $articleRendition);
        } catch (\InvalidArgumentException $e) {
            $this->view->exception= sprintf($translator->trans('Sorry that image is too small. Image needs to be at least %dx%d.', array(), 'article_images'), $rendition->getWidth(), $rendition->getHeight());
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
