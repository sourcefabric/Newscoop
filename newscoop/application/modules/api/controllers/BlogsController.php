<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

require_once __DIR__ . '/AbstractController.php';

/**
 */
class Api_BlogsController extends AbstractController
{
    const PUBLICATION = 5;
    const LANGUAGE = 5;
    
    /** @var Zend_Controller_Request_Http */
    private $request;

    /**
     * @var array
     */
    private $commentStats = array();
    
    /**
     * Init controller.
     */
    public function init()
    {
        $this->_helper->layout->disableLayout();
        $this->request = $this->getRequest();
    }

    /**
     * Default action controller.
     */
    public function indexAction()
    {
        $this->_forward('list');
    }

    /**
     * Return list of articles.
     *
     * @return json
     */
    public function listAction()
    {
        $this->getHelper('contextSwitch')->addActionContext('list', 'json')->initContext();
        
        $response = array();
        
        $sections = $this->_helper->service('section')->findBy(array('publication' => self::PUBLICATION));
        $publication = new Publication(self::PUBLICATION);
        $alias = new Alias($publication->getDefaultAliasId());
        
        $rank = 1;
        foreach ($sections as $section) {
            $articles = $this->_helper->service('article')->findBy(array('section' => $section->getNumber(), 'type' => 'bloginfo'));
            if ($articles[0]) {
                $blogInfo = new ArticleData('bloginfo', $articles[0]->getNumber(), self::LANGUAGE);
                $posts = $this->_helper->service('article')->findBy(array('section' => $section->getNumber(), 'type' => 'blog'), array('published' => 'desc'));
                $lastModified = '0000-00-00 00:00:00';
                $articleImages = ArticleImage::GetImagesByArticleNumber($articles[0]->getNumber());
                $imageUrl = $articleImages[0]->getImage()->getImageUrl();

                $response[] = array(
                    'blog_id' => $section->getNumber(),
                    'url' => $this->view->serverUrl($this->view->url(array(
                        'module' => 'api',
                        'controller' => 'blogs',
                        'action' => 'posts',
                    ), 'default') . sprintf('?blog_id=%d&version=%s', $section->getNumber(), $this->_getParam('version', '1.0'))),
                    'title' => $articles[0]->getTitle(),
                    'description' => trim(strip_tags($blogInfo->getFieldValue('infolong'))),
                    'rank' => $rank,
                    'image_url' => $imageUrl,
                    'published' => $articles[0]->getPublishDate(),
                );
                
                $rank++;
            }
        }

        $this->_helper->json($response);
    }
    
    /**
     * Routes to PostsList or PostsItem
     */
    public function postsAction()
    {
        $params = $this->request->getParams();
        if (in_array('post_id', array_keys($params))) {
            $this->_forward('posts-item');
        } else {
            $this->_forward('posts-list');
        }
    }
    
    /**
     * Return list of posts.
     *
     * @return json
     */
    public function postsListAction()
    {
        $this->getHelper('contextSwitch')->addActionContext('posts-list', 'json')->initContext();

        $response = array();
        $params = $this->request->getParams();
        if (isset($params['blog_id'])) {
            $sections = $this->_helper->service('section')->findBy(array('publication' => self::PUBLICATION, 'number' => $params['blog_id']));
            if (empty($sections)) {
                $this->sendError('Blog not found', 404);
            }

            $section = $sections[0]->getNumber();
            $blogInfo = $this->getBlogInfo($section);
            if (!$blogInfo['active']) {
                $this->sendError('Blog not found', 404);
            }

            $posts = $this->_helper->service('article')->findBy(
                array('section' => $section, 'type' => 'blog'),
                array('published' => 'desc'),
                30);
        } else {
            $posts = $this->_helper->service('article')->findBy(
                array('type' => 'blog'),
                array('published' => 'desc'),
                30);
        }

        $this->commentStats = $this->_helper->service('comment')->getArticleStats(array_map(function($post) {
            return $post->getNumber();
        }, $posts));

        $rank = 1;
        foreach ($posts as $post) {
            if (!isset($section)) {
                $blogInfo = $this->getBlogInfo($post->getSectionNumber());
                if (!$blogInfo['active']) {
                    continue;
                }
            }

            $postData = new ArticleData('blog', $post->getNumber(), self::LANGUAGE);
            $postImages = ArticleImage::GetImagesByArticleNumber($post->getNumber());
            $imageUrl = isset($postImages[0]) ? $postImages[0]->getImage()->getImageUrl() : null;
            $shortName = $postData->getFieldValue('short_name');
            if (empty($shortName)) {
                $shortName = $post->getTitle();
            }

            $teaser = trim(strip_tags($postData->getFieldValue('lede')));
            if (empty($teaser)) {
                $teaser = null;
            }

            $authors = array();
            foreach($post->getAuthors() as $author) {
                $authors[] = $author->getFullName();
            }

            $normalComments = isset($this->commentStats[$post->getNumber()]) ? $this->commentStats[$post->getNumber()]['normal'] : 0;
            $recommendedComments = isset($this->commentStats[$post->getNumber()]) ? $this->commentStats[$post->getNumber()]['recommended'] : 0;

            $response[] = array( // add url
                'blogpost_id' => $post->getNumber(),
                'url' => $this->view->serverUrl($this->view->url(array(
                    'module' => 'api',
                    'controller' => 'blogs',
                    'action' => 'posts',
                ), 'default') . sprintf('?post_id=%d&version=%s', $post->getNumber(), $this->_getParam('version', '1.0'))),
                'short_name' => $shortName,
                'author_name' => implode(',', $authors),
                'teaser' => $teaser,
                'image_url' => $imageUrl,
                'published' => $post->getPublishDate(),
                'comment_count' => $normalComments + $recommendedComments,
                'recommended_comment_count' => $recommendedComments,
                'comment_url' => $this->view->serverUrl($this->view->url(array(
                    'module' => 'api',
                    'controller' => 'comments',
                    'action' => 'list',
                ), 'default') . sprintf('?article_id=%d&version=%s', $post->getNumber(), $this->_getParam('version', '1.0'))),
                'blog_id' => $blogInfo['id'],
                'blog_name' => $blogInfo['name'],
                'blog_image_url' => $blogInfo['image_url'],
                'rank' => $rank++,
            );
        }

        $this->_helper->json($response);
    }
    
    /**
     * Return post.
     *
     * @return json
     */
    public function postsItemAction()
    {
        $this->getHelper('contextSwitch')->addActionContext('posts-item', 'json')->initContext();
        
        $parameters = $this->request->getParams();
        $postId = $parameters['post_id'];
        $response = array();
        
        $posts = $this->_helper->service('article')->findBy(array('number' => $postId, 'type' => 'blog'));
        if ($posts[0]) {
            $post = $posts[0];
            $postData = new ArticleData('blog', $post->getNumber(), self::LANGUAGE);
            $postImages = ArticleImage::GetImagesByArticleNumber($post->getNumber());
            if ($postImages[0]) {
                $imageUrl = $postImages[0]->getImage()->getImageUrl();
            }
            else {
                $imageUrl = '';
            }
            
            $response = array(
                'title' => $post->getTitle(),
                'short_name' => $postData->getFieldValue('short_name'),
                'lede' => $postData->getFieldValue('lede'),
                'vimeo_url' => $postData->getFieldValue('vimeo_url'),
                'youtube_shortcode' => $postData->getFieldValue('youtube_shortcode'),
                'body' => $postData->getFieldValue('body'),
                'image_url' => $imageUrl,
                'last_modified' => $post->getPublishDate()
            );
        }
        
        $this->_helper->json($response);
    }

    /**
     * Return the response data for the requested bloginfo
     *
     * @param int $blogId
     * @return array
     */
    private function getBlogInfo($blogId)
    {
        $blogInfos = $this->_helper->service('article')->findBy(array('section' => $blogId, 'type' => 'bloginfo'));
        if (empty($blogInfos)) {
            $this->sendError('Blog not found', 404);
        }

        $blogInfoData = new ArticleData('bloginfo', $blogInfos[0]->getNumber(), self::LANGUAGE);
        $blogInfoImages = ArticleImage::GetImagesByArticleNumber($blogInfos[0]->getNumber());
        $blogInfoImageUrl = isset($blogInfoImages[0]) ? $blogInfoImages[0]->getImage()->getImageUrl() : null;

        return array(
            'id' => $blogInfos[0]->getNumber(),
            'name' => $blogInfos[0]->getName(),
            'image_url' => $blogInfoImageUrl,
            'active' => $blogInfoData->getFieldValue('active') ? true : false,
        );
    }
}
