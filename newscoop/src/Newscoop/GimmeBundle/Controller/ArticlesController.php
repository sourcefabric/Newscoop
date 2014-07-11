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
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Newscoop\NewscoopException;
use Newscoop\Exception\InvalidParametersException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
     * Write Article
     *
     * @ApiDoc(
     *     statusCodes={
     *         201="Returned when Article is created"
     *     },
     *     parameters={
     *         {"name"="number", "dataType"="integer", "required"=true, "description"="Article number"},
     *         {"name"="language", "dataType"="string", "required"=false, "description"="Language code"}
     *     }
     * )
     *
     * @Route("/articles/{number}/{language}.{_format}", defaults={"_format"="json", "language"="en"})
     * @Method("PATCH")
     */
    public function patchArticleAction(Request $request, $number, $language)
    {
        $params = array();
        $content = $this->get("request")->getContent();
        if (!empty($content)) {
            $params = json_decode($content, true); // 2nd param to get as array
        }

        $em = $this->container->get('em');
        $inputManipulator = $this->get('newscoop.input_manipulator');

        if (array_key_exists('authors', $params)) {
            $authors = array();
            foreach ($params['authors'] as $key => $value) {
                $authors[] = $inputManipulator::getVar(array('inputObject' => $value, 'variableName' => 'name'));
            }

            $clean['articleAuthor'] = $authors;
        }

        if ($inputManipulator::getVar(array('inputObject' => $params, 'variableName' => 'title', 'checkIfExists' => true))) {
            $clean['articleTitle'] = $inputManipulator::getVar(array('inputObject' => $params, 'variableName' => 'title'));
        }
        if ($inputManipulator::getVar(array('inputObject' => $params, 'variableName' => 'created', 'checkIfExists' => true))) {
            $clean['creationDate'] = date_format(date_create_from_format(DATE_ATOM, $inputManipulator::getVar(array('inputObject' => $params, 'variableName' => 'created'))), 'Y-m-d H:i:s');
        }
        if ($inputManipulator::getVar(array('inputObject' => $params, 'variableName' => 'published', 'checkIfExists' => true))) {
            $clean['publishDate'] = date_format(date_create_from_format(DATE_ATOM, $inputManipulator::getVar(array('inputObject' => $params, 'variableName' => 'published'))), 'Y-m-d H:i:s');
        }

        $commentStatus = null;

        if ($inputManipulator::getVar(array('inputObject' => $params, 'variableType' => 'int', 'variableName' => 'comments_locked', 'checkIfExists' => true))) {
            if ($inputManipulator::getVar(array('inputObject' => $params, 'variableType' => 'int', 'variableName' => 'comments_locked')) == 1) {
                $commentStatus = 'locked';
            }
        }

        if ($commentStatus != 'locked') {
            if ($inputManipulator::getVar(array('inputObject' => $params, 'variableType' => 'int', 'variableName' => 'comments_enabled', 'checkIfExists' => true))) {
                $commentsEnabled = $inputManipulator::getVar(array('inputObject' => $params, 'variableType' => 'int', 'variableName' => 'comments_enabled'));
                if ($commentsEnabled == 0) {
                    $commentStatus = 'disabled';
                } elseif ($commentsEnabled == 1) {
                    $commentStatus = 'enabled';
                }
            }
        }

        $user = $this->getUser();
        $translator = \Zend_Registry::get('container')->getService('translator');

        // Fetch article
        $articleObj = $this->getArticle($number, $language);

        $articleTypeObj = $articleObj->getArticleData();
        $articleType = new \ArticleType($articleTypeObj->m_articleTypeName);
        $fields = $articleType->getUserDefinedColumns(null, true, true);

        $notBodyFields = array();
        foreach ($fields as $field) {
            if (!$field->isContent()) {
                $notBodyFields[] = $field->getPrintName();
            }
        }

        if (array_key_exists('fields', $params)) {
            foreach ($params['fields'] as $key => $value) {
                if (!in_array($key, $notBodyFields)) {
                    $clean['F'.$key.'_'.$number] = $value;
                } else {
                    $clean['F'.$key] = $value;
                }
            }
        }

        $dbColumns = $articleTypeObj->getUserDefinedColumns(false, true);

        $articleFields = array();
        foreach ($dbColumns as $dbColumn) {
            if ($dbColumn->getType() == \ArticleTypeField::TYPE_BODY) {
                $dbColumnParam = $dbColumn->getName() . '_' . $number;
            } else {
                $dbColumnParam = $dbColumn->getName();
            }

            if (isset($clean[$dbColumnParam])) {
                if ($dbColumn->getType() == \ArticleTypeField::TYPE_TEXT
                    && $dbColumn->getMaxSize()!=0
                    && $dbColumn->getMaxSize()!='') {
                        $fieldValue = trim($clean[$dbColumnParam]);
                        $articleFields[$dbColumn->getName()] = mb_strlen($fieldValue, 'utf8') > $dbColumn->getMaxSize()
                            ? substr($fieldValue, 0, $dbColumn->getMaxSize())
                            : $fieldValue;
                } else {
                    $articleFields[$dbColumn->getName()] = trim($clean[$dbColumnParam]);
                }
            } else {
                unset($articleFields[$dbColumn->getName()]); // ignore if not set
            }
        }

        /**
         * TODO
         * This still has to be placed back in
         */
        // Update the article author
            // $blogService = \Zend_Registry::get('container')->getService('blog');
            // $blogInfo = $blogService->getBlogInfo($user);
            // if (!empty($articleAuthor)) {
            //     ArticleAuthor::OnArticleLanguageDelete($articleObj->getArticleNumber(), $articleObj->getLanguageId());
            //     $i = 0;
            //     forclean['each'] ($articleAuthor as $author) {
            //         $authorObj = new Author($author);
            //         if (!$authorObj->exists() && strlen(trim($author)) > 0) {
            //             if ($blogService->isBlogger($user)) { // blogger can't create authors
            //                 continue;
            //             }

            //             $authorData = Author::ReadName($author);
            //             $authorObj->create($authorData);
            //         } elseif ($blogService->isBlogger($user)) { // test if using authors from blog
            //             if (!$blogService->isBlogAuthor($authorObj, $blogInfo)) {
            //                 continue;
            //             }
            //         }

            //         // Sets the author type selected
            //         $author_type = $articleAuthor_type[$i];
            //         $authorObj->setType($author_type);
            //         // Links the author to the article
            //         if ($authorObj->getId() != 0) {
            //             $articleAuthorObj = new ArticleAuthor($articleObj->getArticleNumber(),
            //                                               $articleObj->getLanguageId(),
            //                                               $authorObj->getId(), $author_type, $i + 1);
            //         }

            //         if (isset($articleAuthorObj) && !$articleAuthorObj->exists()) {
            //             $articleAuthorObj->create();
            //         }

            //         $i++;
            //     }
            // }

        // Update the article.
        if (array_key_exists('articleTitle', $clean)) {
            $articleObj->setTitle($clean['articleTitle']);
        }
        $articleObj->setIsIndexed(false);

        if (!empty($commentStatus)) {
            if ($commentStatus == "enabled" || $commentStatus == "locked") {
                $commentsEnabled = true;
            } else {
                $commentsEnabled = false;
            }
            // If status has changed, then you need to show/hide all the comments
            // as appropriate.
            if ($articleObj->commentsEnabled() != $commentsEnabled) {
                $articleObj->setCommentsEnabled($commentsEnabled);
                $repository = $em->getRepository('Newscoop\Entity\Comment');
                $repository->setArticleStatus($number, $clean['languageId'], $commentsEnabled?STATUS_APPROVED:STATUS_HIDDEN);
                $repository->flush();
            }
            $articleObj->setCommentsLocked($commentStatus == "locked");
        }

        // Make sure that the time stamp is updated.
        $articleObj->setProperty('time_updated', 'NOW()', true, true);

        if (array_key_exists('creationDate', $clean) && preg_match("/\d{4}-\d{2}-\d{2}/", $clean['creationDate'])) {
            $articleObj->setCreationDate($clean['creationDate']);
        }

        // Verify publish date is in the correct format.
        // If not, dont change it.
        if (array_key_exists('publishDate', $clean) && preg_match("/\d{4}-\d{2}-\d{2}/", $clean['publishDate'])) {
            $articleObj->setPublishDate($clean['publishDate']);
        }

        foreach ($articleFields as $dbColumnName => $text) {
            $articleTypeObj->setProperty($dbColumnName, $text);
        }
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
     * @Method("LINK")
     * @View(statusCode=201)
     *
     * @return Form
     */
    public function linkArticleAction(Request $request, $number, $language)
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
        }


        if ($matched === false) {
            throw new InvalidParametersException('Any supported link object not found');
        }
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
     * @Method("UNLINK")
     * @View(statusCode=204)
     *
     * @return Form
     */
    public function unlinkArticleAction(Request $request, $number)
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
        }

        if ($matched === false) {
            throw new InvalidParametersException('Any supported unlink object not found');
        }
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

    private function getArticle($number, $language) {
        $em = $this->container->get('em');
        $languageObject = $em->getRepository('Newscoop\Entity\Language')->findOneByCode($language);

        $user = $this->getUser();
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
}
