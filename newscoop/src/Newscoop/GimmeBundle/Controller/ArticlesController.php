<?php
/**
 * @package Newscoop\Gimme
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @author Yorick Terweijden <yorick.terweijden@sourcefabric.org>
 * @copyright 2014 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\GimmeBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\View as FOSView;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Newscoop\NewscoopException;
use Newscoop\Exception\InvalidParametersException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Doctrine\ORM\EntityNotFoundException;
use Newscoop\GimmeBundle\Form\Type\ArticleType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Articles controller
 */
class ArticlesController extends FOSRestController
{
    /**
     * Create Article
     *
     * @ApiDoc(
     *     statusCodes={
     *         201="Returned when Article is created",
     *         404="Returned when one of required relations is not found"
     *     },
     *     input="\Newscoop\GimmeBundle\Form\Type\ArticleType"
     * )
     *
     * @Route("/articles/create.{_format}", defaults={"_format"="json"}, options={"expose"=true}, name="newscoop_gimme_articles_createarticle")
     * @Route("/articles/.{_format}", defaults={"_format"="json"}, options={"expose"=true}, name="newscoop_gimme_articles_createarticle_clear")
     * @Method("POST")
     */
    public function createArticleAction(Request $request)
    {
        $user = $this->container->get('user')->getCurrentUser();
        if (!$user->hasPermission('AddArticle')) {
            throw new AccessDeniedException("You do not have the right to add articles.");
        }

        $form = $this->createForm(new ArticleType(), array());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->container->get('em');
            $articleService = $this->container->get('newscoop_newscoop.article_service');

            $attributes = $form->getData();
            $language = $em->getRepository('Newscoop\Entity\Language')->findOneBy(array('id' => $attributes['language']));
            if (!$language) {
                throw new EntityNotFoundException("Language was not found");
            }

            $articleType = $em->getRepository('Newscoop\Entity\ArticleType')->findOneBy(array('name' => $attributes['type']));
            if (!$articleType) {
                throw new EntityNotFoundException("Article type was not found");
            }

            $publication = $em->getRepository('Newscoop\Entity\Publication')->findOneBy(array('id' => $attributes['publication']));
            if (!$publication) {
                throw new EntityNotFoundException("Publication was not found");
            }

            $issue = $em->getRepository('Newscoop\Entity\Issue')
                ->findOneBy(array('publication' => $publication, 'id' => $attributes['issue']));

            $section = $em->getRepository('Newscoop\Entity\Section')
                ->findOneBy(array('publication' => $publication, 'issue' => $issue, 'id' => $attributes['section']));

            $article = $articleService->createArticle($articleType, $language, $user, $publication, $attributes, $issue, $section);

            if (!$user->getAuthor()) {
                $author = new \Newscoop\Entity\Author($user->getFirstName(), $user->getLastName());
                $em->persist($author);
                $user->setAuthor($author);
            }

            $authorType = $em->getRepository('Newscoop\Entity\AuthorType')->findOneBy(array(
                'type' => 'Journalist'
            ));
            if (!$authorType) {
                $authorType = new \Newscoop\Entity\AuthorType('Journalist');
                $em->persist($authorType);
            }
            $em->flush();

            $authorService = $this->container->get('author');
            $authorService->addAuthorToArticle($article, $user->getAuthor(), $authorType);

            $this->postAddUpdate($article);

            $view = FOSView\View::create($article, 201);
            $view->setHeader('X-Location', $this->generateUrl('newscoop_gimme_articles_getarticle', array(
                'number' => $article->getId(),
            ), true));

            return $view;
        }

        return $form;
    }

    /**
     * Update Article
     *
     * Additional form aparameters:
     *
     *  fields: array with article type fields and ther values.
     *
     *     article[fields][lead] = 'new lead'
     *
     * @ApiDoc(
     *     statusCodes={
     *         201="Returned when Article is updated"
     *     },
     *     parameters={
     *         {"name"="number", "dataType"="integer", "required"=true, "description"="Article number"},
     *         {"name"="language", "dataType"="string", "required"=false, "description"="Language code"}
     *     },
     *     input="\Newscoop\GimmeBundle\Form\Type\ArticleType"
     * )
     *
     * @Route("/articles/{number}/{language}.{_format}", defaults={"_format"="json", "language"="en"}, options={"expose"=true}, name="newscoop_gimme_articles_patcharticle")
     * @Method("PATCH|POST")
     */
    public function patchArticleAction(Request $request, $number, $language)
    {
        $em = $this->container->get('em');
        $user = $this->container->get('user')->getCurrentUser();
        if (!$user->hasPermission('AddArticle')) {
            throw new AccessDeniedException("You do not have the right to add articles.");
        }

        $article = $em->getRepository('Newscoop\Entity\Article')
            ->getArticle($number, $request->get('language'))
            ->getOneOrNullResult();

        if (!$article) {
            throw new NotFoundHttpException('Article was not found');
        }

        $form = $this->createForm(new ArticleType($article->getData(null)), array(), array(
            'method' => $request->getMethod()
        ));
        $form->handleRequest($request);
        if ($form->isValid()) {
            $articleService = $this->container->get('newscoop_newscoop.article_service');

            $attributes = $form->getData();

            $article = $articleService->updateArticle($article, $attributes);

            $this->postAddUpdate($article);

            return new FOSView\View($article, 200, array(
                'X-Location' => $this->generateUrl('newscoop_gimme_articles_getarticle', array(
                    'number' => $article->getId(),
                ), true))
            );
        } else {
           // TODO add support for global for errors handler
        }

        return new FOSView\View($form, 400);
    }

    private function postAddUpdate($article)
    {
        $cacheService = $this->container->get('newscoop.cache');
        $cacheService->clearNamespace('article');
        $cacheService->clearNamespace('article_type');
        $cacheService->clearNamespace('boxarticles');
    }

    /**
     * Get Articles
     *
     * @ApiDoc(
     *     statusCodes={
     *         200="Returned when articles found",
     *         404={
     *           "Returned when articles are not found",
     *         }
     *     }
     * )
     *
     * @Route("/articles.{_format}", defaults={"_format"="json"}, options={"expose"=true}, name="newscoop_gimme_articles_getarticles")
     * @Method("GET")
     * @View(serializerGroups={"list"})
     *
     * @return array
     */
    public function getArticlesAction(Request $request)
    {
        $em = $this->container->get('em');
        $publication = $this->get('newscoop_newscoop.publication_service')->getPublication()->getId();

        $articles = $em->getRepository('Newscoop\Entity\Article')
            ->getArticles($publication, $request->get('type', null), $request->get('language', null));

        $paginator = $this->get('newscoop.paginator.paginator_service');
        $articles = $paginator->paginate($articles, array(
            'distinct' => false
        ));

        return $articles;
    }

    /**
     * Search for articles
     *
     * Parameter 'query' contains keywords seperated with ",". Example: test,article,keyword3 
     *
     * @ApiDoc(
     *     statusCodes={
     *         200="Returned when successful",
     *         404={
     *           "Returned when the articles are not found"
     *         }
     *     },
     *     parameters={
     *         {"name"="query", "dataType"="string", "required"=true, "description"="article serach query"},
     *         {"name"="publication", "dataType"="string", "required"=false, "description"="Filter by publication"},
     *         {"name"="issue", "dataType"="string", "required"=false, "description"="Filter by issue"},
     *         {"name"="section", "dataType"="string", "required"=false, "description"="Filter by section"}
     *     }
     * )
     *
     * @Route("/search/articles.{_format}", defaults={"_format"="json"}, options={"expose"=true})
     * @Method("GET")
     * @View(serializerGroups={"list"})
     *
     * @return array
     */
    public function searchArticlesAction(Request $request)
    {
        $articleSearch = $this->container->get('search.article');
        $publication = $this->get('newscoop.publication_service')->getPublication();
        $onlyPublished = true;

        try {
            $user = $this->container->get('user')->getCurrentUser();
            if ($user && $user->isAdmin()) {
                $onlyPublished = false;
            }
        } catch (\Newscoop\NewscoopException $e) {}

        $articles = $articleSearch->searchArticles(
            $request->get('language', $publication->getLanguage()->getCode()),
            $request->query->get('query', null),
            $request->get('publication', false),
            $request->get('issue', false),
            $request->get('section', false),
            $onlyPublished
        );

        $paginator = $this->get('newscoop.paginator.paginator_service');
        $articles = $paginator->paginate($articles, array(
            'distinct' => false
        ));

        return $articles;
    }

    /**
     * Get related articles
     *
     * @ApiDoc(
     *     statusCodes={
     *         200="Returned when successful",
     *         404={
     *           "Returned when the articles are not found"
     *         }
     *     }
     * )
     *
     * @Route("/articles/{number}/related.{_format}", defaults={"_format"="json"}, options={"expose"=true}, name="newscoop_gimme_articles_related_default_lang")
     * @Route("/articles/{number}/{language}/related.{_format}", defaults={"_format"="json"}, options={"expose"=true}, name="newscoop_gimme_articles_related")
     * @Method("GET")
     * @View(serializerGroups={"list"})
     *
     * @return array
     */
    public function relatedArticlesAction(Request $request, $number, $language = null)
    {
        $em = $this->container->get('em');
        $publication = $this->get('newscoop.publication_service')->getPublication();
        $relatedArticlesService = $this->get('related_articles');

        $article = $em->getRepository('Newscoop\Entity\Article')
            ->getArticle($number, $request->get('language', $publication->getLanguage()->getCode()))
            ->getOneOrNullResult();

        if (!$article) {
            throw new NotFoundHttpException('Article was not found');
        }

        $onlyPublished = true;
        try {
            $user = $this->container->get('user')->getCurrentUser();
            if ($user && $user->isAdmin()) {
                $onlyPublished = false;
            }
        } catch (\Newscoop\NewscoopException $e) {}

        $relatedArticles = $relatedArticlesService
            ->getRelatedArticles($article);

        $ids = array();
        foreach ($relatedArticles as $relatedArticle) {
            $ids[]  = $relatedArticle->getArticleNumber();
        }

        $articles = $em->getRepository('Newscoop\Entity\Article')
            ->getArticlesByIds(
                $article->getLanguage()->getCode(),
                $ids,
                $onlyPublished
            );

        $paginator = $this->get('newscoop.paginator.paginator_service');
        $paginator->setUsedRouteParams(array('number' => $number, 'language' => $article->getLanguage()->getCode()));
        $articles = $paginator->paginate($articles, array(
            'distinct' => false
        ));

        return $articles;
    }

    /**
     * Get article
     *
     * @ApiDoc(
     *     statusCodes={
     *         200="Returned when successful",
     *         404={
     *           "Returned when the article is not found",
     *         }
     *     },
     *     filters={
     *          {"name"="language", "dataType"="string", "description"="Language code"}
     *     },
     *     output="\Newscoop\Entity\Article"
     * )
     *
     * @Route("/articles/{number}.{_format}", defaults={"_format"="json"}, options={"expose"=true}, name="newscoop_gimme_articles_getarticle")
     * @Method("GET")
     * @View(serializerGroups={"details"})
     *
     * @return Form
     */
    public function getArticleAction(Request $request, $number)
    {
        $em = $this->container->get('em');
        $publication = $this->get('newscoop_newscoop.publication_service')->getPublication();

        $article = $em->getRepository('Newscoop\Entity\Article')
            ->getArticle($number, $request->get('language', $publication->getLanguage()->getCode()))
            ->getOneOrNullResult();

        if (!$article) {
            throw new NotFoundHttpException('Article was not found');
        }

        return $article;
    }

    /**
     * Link resource with Article entity
     *
     * **article authors headers**:
     *
     *     header name: "link"
     *     header value: "</api/authors/7; rel="author">,</api/authors/types/4; rel="author-type">"
     *
     * **attachments headers**:
     *
     *     header name: "link"
     *     header value: "</api/attachments/1; rel="attachment">"
     *
     * **images headers**:
     *
     *     header name: "link"
     *     header value: "</api/images/1; rel="image">"
     *
     * **topics headers**:
     *
     *     header name: "link"
     *     header value: "</api/topics/1; rel="topic">"
     *
     * **related articles headers**:
     *
     *     header name: "link"
     *     header value: "</api/article/1; rel="article">"
     * or with specific language
     *
     *     header value: "</api/article/1?language=en; rel="article">"
     * you can also specify position on list
     *
     *     header value: "</api/article/1?language=en; rel="article">,<1; rel="article-position">"
     *
     * @ApiDoc(
     *     statusCodes={
     *         201="Returned when successful",
     *         404="Returned when resource not found",
     *         409={
     *           "Returned when the link already exists",
     *         }
     *     },
     *     parameters={
     *         {"name"="number", "dataType"="integer", "required"=true, "description"="Article number"},
     *         {"name"="language", "dataType"="string", "required"=true, "description"="Language string"}
     *     }
     * )
     *
     * @Route("/articles/{number}/{language}.{_format}", defaults={"_format"="json"}, options={"expose"=true}, name="newscoop_gimme_articles_linkarticle")
     * @Route("/articles/{number}.{_format}", defaults={"_format"="json"}, options={"expose"=true}, name="newscoop_gimme_articles_linkarticle_default_lang")
     * @Method("LINK")
     * @View(statusCode=201)
     *
     * @return Article
     */
    public function linkArticleAction(Request $request, $number, $language = null)
    {
        $em = $this->container->get('em');
        $publication = $this->get('newscoop.publication_service')->getPublication();

        $article = $em->getRepository('Newscoop\Entity\Article')
            ->getArticle($number, $request->get('language', $publication->getLanguage()->getCode()))
            ->getOneOrNullResult();

        if (!$article) {
            throw new NotFoundHttpException('Article was not found');
        }

        $matched = false;
        foreach ($request->attributes->get('links', array()) as $key => $objectArray) {
            if (!is_array($objectArray)) {
                return true;
            }

            $resourceType = $objectArray['resourceType'];
            $object = $objectArray['object'];

            if ($object instanceof \Exception) {
                throw $object;
            }

            if ($object instanceof \Newscoop\Image\LocalImage) {
                $imagesService = $this->get('image');
                $imagesService->addArticleImage($article->getNumber(), $object);

                $matched = true;

                continue;
            }

            if ($object instanceof \Newscoop\Entity\Attachment) {
                $attachmentService = $this->get('attachment');
                $attachmentService->addAttachmentToArticle($article, $object);

                $matched = true;

                continue;
            }

            if ($object instanceof \Newscoop\Entity\Author) {
                $authorService = $this->get('author');
                $authorType = false;
                foreach ($request->attributes->get('links') as $key => $tempObjectArray) {
                    if ($tempObjectArray['object'] instanceof \Newscoop\Entity\AuthorType) {
                        $authorType = $tempObjectArray['object'];
                    }
                }

                if ($authorType) {
                    $authorService->addAuthorToArticle($article, $object, $authorType);

                    $matched = true;
                }

                continue;
            }

            if ($object instanceof \Newscoop\Entity\Snippet) {
                $snippetRepo = $em->getRepository('Newscoop\Entity\Snippet');
                $snippetRepo->addSnippetToArticle($object, $article);

                $matched = true;

                continue;
            }

            if ($object instanceof \Newscoop\NewscoopBundle\Entity\Topic) {
                $topicService = $this->get('newscoop_newscoop.topic_service');
                $topicService->addTopicToArticle($object, $article);

                $matched = true;

                continue;
            }

            if ($object instanceof \Newscoop\Entity\Article) {
                $relatedArticlesService = $this->get('related_articles');

                $position = false;
                if (count($notConvertedLinks = $this->getNotConvertedLinks($request)) > 0) {
                    foreach ($notConvertedLinks as $link) {
                        if (isset($link['resourceType']) && $link['resourceType'] == 'article-position') {
                            $position = $link['resource'];
                        }
                    }
                }

                $relatedArticlesService->addArticle($article, $object, $position);

                $matched = true;

                continue;
            }
        }

        if ($matched === false) {
            throw new InvalidParametersException('Any supported link object not found');
        }

        return $article;
    }

    /**
     * Unlink resource from Article
     *
     * **article authors headers**:
     *
     *     header name: "link"
     *     header value: "</api/authors/7; rel="author">,</api/authors/types/4; rel="author-type">"
     *
     * **attachments headers**:
     *
     *     header name: "link"
     *     header value: "</api/attachments/1; rel="attachment">"
     *
     * **images headers**:
     *
     *     header name: "link"
     *     header value: "</api/images/1; rel="image">"
     *
     * **topics headers**:
     *
     *     header name: "link"
     *     header value: "</api/topics/1; rel="topic">"
     *
     * **related articles headers**:
     *
     *     header name: "link"
     *     header value: "</api/article/1; rel="topic">"
     * or with specific language
     *
     *     header value: "</api/article/1?language=en; rel="article">"
     *
     * @ApiDoc(
     *     statusCodes={
     *         204="Returned when successful",
     *         404="Returned when resource not found"
     *     },
     *     parameters={
     *         {"name"="number", "dataType"="integer", "required"=true, "description"="Article number"}
     *     }
     * )
     *
     * @Route("/articles/{number}/{language}.{_format}", defaults={"_format"="json"}, options={"expose"=true}, name="newscoop_gimme_articles_unlinkarticle")
     * @Route("/articles/{number}.{_format}", defaults={"_format"="json"}, options={"expose"=true}, name="newscoop_gimme_articles_unlinkarticle_default_lang")
     * @Method("UNLINK")
     * @View(statusCode=204)
     *
     * @return Article
     */
    public function unlinkArticleAction(Request $request, $number, $language = null)
    {
        $em = $this->container->get('em');
        $publication = $this->get('newscoop.publication_service')->getPublication();

        $article = $em->getRepository('Newscoop\Entity\Article')
            ->getArticle($number, $request->get('language', $publication->getLanguage()->getCode()))
            ->getOneOrNullResult();

        if (!$article) {
            throw new NotFoundHttpException('Article was not found');
        }

        $matched = false;
        foreach ($request->attributes->get('links', array()) as $key => $objectArray) {
            $resourceType = $objectArray['resourceType'];
            $object = $objectArray['object'];

            if ($object instanceof \Exception) {
                throw $object;
            }

            if ($object instanceof \Newscoop\Image\LocalImage) {
                $imagesService = $this->get('image');
                $articleImage = $em->getRepository('Newscoop\Image\ArticleImage')
                    ->getArticleImage($article->getNumber(), $object)
                    ->getOneOrNullResult();

                if ($articleImage) {
                    $imagesService->removeArticleImage($articleImage);
                } else {
                    throw new InvalidParametersException('Image is not linked to article');
                }

                $matched = true;

                continue;
            }

            if ($object instanceof \Newscoop\Entity\Attachment) {
                $attachmentService = $this->get('attachment');
                $attachmentService->removeAttachmentFormArticle($article, $object);

                $matched = true;

                continue;
            }

            if ($object instanceof \Newscoop\Entity\Author) {
                $authorService = $this->get('author');
                $authorType = false;
                foreach ($request->attributes->get('links') as $key => $tempObjectArray) {
                    if ($tempObjectArray['object'] instanceof \Newscoop\Entity\AuthorType) {
                        $authorType = $tempObjectArray['object'];
                    }
                }

                if ($authorType) {
                    $authorService->removeAuthorFromArticle($article, $object, $authorType);

                    $matched = true;
                }

                continue;
            }

            if ($object instanceof \Newscoop\Entity\Snippet) {
                $snippetRepo = $em->getRepository('Newscoop\Entity\Snippet');
                $snippetRepo->removeSnippetFromArticle($object, $article);

                $matched = true;

                continue;
            }

            if ($object instanceof \Newscoop\NewscoopBundle\Entity\Topic) {
                $topicService = $this->get('newscoop_newscoop.topic_service');
                $topicService->removeTopicFromArticle($object, $article);

                $matched = true;

                continue;
            }

            if ($object instanceof \Newscoop\Entity\Article) {
                $relatedArticlesService = $this->get('related_articles');
                $relatedArticlesService->removeRelatedArticle($article, $object);

                $matched = true;

                continue;
            }
        }

        if ($matched === false) {
            throw new InvalidParametersException('Any supported unlink object not found');
        }

        return $article;
    }

    /**
     * Change Article status
     *
     * @ApiDoc(
     *     statusCodes={
     *         201="Returned when Article Status changed successfully"
     *     },
     *     parameters={
     *         {"name"="number", "dataType"="integer", "required"=true, "description"="Article number"},
     *         {"name"="language", "dataType"="string", "required"=true, "description"="Language code"},
     *         {"name"="status", "dataType"="string", "required"=true, "description"="Status code: 'N','S','M','Y'"}
     *     }
     * )
     *
     * @Route("/articles/{number}/{language}/{status}.{_format}", defaults={"_format"="json", "language"="en"}, options={"expose"=true}, name="newscoop_gimme_articles_changearticlestatus")
     * @Method("PATCH")
     */
    public function changeArticleStatus(Request $request, $number, $language, $status)
    {
        $statuses = array('N','S','M','Y');
        if (!in_array($status, $statuses)) {
            throw new InvalidParametersException('The provided Status is not valid, available: N, S, M, Y.');
        }

        $articleObj = $this->getArticle($number, $language);
        $success = $articleObj->setWorkflowStatus($status);
        $response = new Response();
        if ($success) {
            $response->setStatusCode(201);
        } else {
            $response->setStatusCode(500);
            throw new \Exception('Setting status code failed');
        }

        return $response;
    }

    /**
     * Lock or unlock article
     *
     * @ApiDoc(
     *     statusCodes={
     *         200="Returned when article has been locked",
     *         204="Returned when article has been unlocked",
     *         403="Returned when trying to set the same status"
     *     },
     *     parameters={
     *         {"name"="number", "dataType"="integer", "required"=true, "description"="Article number"},
     *         {"name"="language", "dataType"="string", "required"=true, "description"="Language code"}
     *     }
     * )
     *
     * @Route("/articles/{number}/{language}/lock.{_format}", defaults={"_format"="json", "language"="en"}, options={"expose"=true}, name="newscoop_gimme_articles_changearticlelockstatus")
     * @Method("POST|DELETE")
     */
    public function lockUnlockArticle(Request $request, $number, $language)
    {
        $em = $this->container->get('em');
        $article = $em->getRepository('Newscoop\Entity\Article')
            ->getArticle($number, $language)
            ->getOneOrNullResult();

        if (!$article) {
            throw new NewscoopException('Article does not exist');
        }

        $response = new Response();
        $response->setStatusCode(403);
        if ($request->getMethod() === "POST") {
            if (!$article->isLocked()) {
                $article->setLockUser($this->getUser());
                $article->setLockTime(new \DateTime());
                $response->setStatusCode(200);
            }
        }

        if ($request->getMethod() === "DELETE") {
            if ($article->isLocked()) {
                $article->setLockUser();
                $article->setLockTime();
                $response->setStatusCode(204);
            }
        }

        $em->flush();

        return $response;
    }

    private function getNotConvertedLinks($request)
    {
        $links = array();
        foreach ($request->attributes->get('links') as $idx => $link) {
            if (is_string($link)) {
                $linkParams = explode(';', trim($link));
                $resourceType = null;
                if (count($linkParams) > 1) {
                    $resourceType = trim(preg_replace('/<|>/', '', $linkParams[1]));
                    $resourceType = str_replace("\"", "", str_replace("rel=", "", $resourceType));
                }
                $resource   = array_shift($linkParams);
                $resource   = preg_replace('/<|>/', '', $resource);

                $links[] = array(
                    'resource' => $resource,
                    'resourceType' => $resourceType
                );
            }
        }

        return $links;
    }

    private function getArticle($number, $language, $user)
    {
        $em = $this->container->get('em');
        $languageObject = $em->getRepository('Newscoop\Entity\Language')->findOneByCode($language);

        // Fetch article
        $articleObj = new \Article($languageObject->getId(), $number);

        if (!$articleObj->exists()) {
            throw new NewscoopException('Article does not exist');
        }

        if (!$articleObj->userCanModify($user)) {
            throw new AccessDeniedException('User cannot modify article.');
        }

        // Only users with a lock on the article can change it.
        if ($articleObj->isLocked() && ($user->getUserId() != $articleObj->getLockedByUser())) {
            $lockTime = new \DateTime($articleObj->getLockTime());
            $now = new \DateTime("now");
            $difference = $now->diff($lockTime);
            $ago = $difference->format("%R%H:%I:%S");
            $lockUser = new \User($articleObj->getLockedByUser());

            throw new NewscoopException(sprintf('Article locked by %s (%s ago)', $lockUser->getRealName(), $ago));
        }

        return $articleObj;
    }

    private function processForm($request, $number, $language)
    {
        $em = $this->container->get('em');
        $publication = $this->get('newscoop.publication_service')->getPublication();
        $article = null;
        $articles = $em->getRepository('Newscoop\Entity\Article')
            ->getArticle($number, $language)
            ->getResult();

        if (count($articles) > 0) {
            $article = $articles[0];
        }

        $statusCode = $article ? 200 : 201;

        $form = $this->createForm(new ArticleType(), $article);
        $form->bind($request);

        if ($form->isValid()) {
            if ($statusCode == 201) {
                $em->persist($article);
            }
            $em->flush();

            $response = new Response();
            $response->setStatusCode($statusCode);

            return $response;
        }

        return $form;
    }
}
