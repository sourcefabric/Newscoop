<?php
/**
 * @package Newscoop\Gimme
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\GimmeBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Newscoop\Entity\Article as ArticleEntity;
use Newscoop\NewscoopException;
use Newscoop\GimmeBundle\Form\Type\ArticleType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Form\Exception\InvalidArgumentException;
use Newscoop\Exception\ResourceNotModifiedException;

/**
 * Articles controller
 */
class ArticlesController extends FOSRestController
{
	public static function CleanMagicQuotes($p_array)
	{
	   $gpcList = array();

	   foreach ($p_array as $key => $value) {
	       $decodedKey = stripslashes($key);
	       if (is_array($value)) {
	           $decodedValue = self::CleanMagicQuotes($value);
	       } else {
	           $decodedValue = stripslashes($value);
	       }
	       $gpcList[$decodedKey] = $decodedValue;
	   }
	   return $gpcList;
	} // fn CleanMagicQuotes

	/**
 *
 * inputObject the array or string to search in
 * variableName variable to look for
 * variableType type the variable should have (string, int, array, boolean)
 * defaultValue default value for the variable if it doesn't exist
 * checkIfExists check if the variable exists (for if statements, only returns BOOL)
 * ignoreErrors ignore errors, return empty
 */

	public static function getVar(array $parameters = array())
	{
	    $requiredParams = array('inputObject', 'variableName');
	    $defaultParams = array(
	                            'variableType' => 'string',
	                            'defaultValue' => null,
	                            'checkIfExists' => false,
	                            'ignoreErrors' => false
	                        );
	    foreach ($requiredParams as $requiredParam) {
	        if (!array_key_exists($requiredParam, $parameters)) {
	            throw new \InvalidArgumentException(__METHOD__.': Parameter '.$requiredParam.' is required.');
	        }
	    }

	    foreach ($defaultParams as $defaultParam => $defaultValue) {
	        if (!array_key_exists($defaultParam, $parameters)) {
	            $parameters[$defaultParam] = $defaultValue;
	        }
	    }

	    // allow the GetVar to also use strings
	    if (!is_array($parameters['inputObject'])) {
	        $parameters['inputObject'] = array($parameters['variableName'] => $parameters['inputObject']);
	    }

	    $parameters['variableType'] = strtolower($parameters['variableType']);

	    if (!isset($parameters['inputObject'][$parameters['variableName']])) {
	        if ($parameters['checkIfExists']) {
	            return false;
	        } else {
	            return true;
	        }
	        if (!$parameters['ignoreErrors']) {
	            throw new InvalidArgumentException('"'.$parameters['variableName'].'" is not set');
	        }
	        return $parameters['defaultValue'];
	    }
	    // Clean the slashes
	    if (get_magic_quotes_gpc()) {
	        if (is_array($parameters['inputObject'][$parameters['variableName']])) {
	            $parameters['inputObject'][$parameters['variableName']] = self::CleanMagicQuotes($parameters['inputObject'][$parameters['variableName']]);
	        } else {
	            $parameters['inputObject'][$parameters['variableName']] = stripslashes($parameters['inputObject'][$parameters['variableName']]);
	        }
	    }
	    switch ($parameters['variableType']) {
	        case 'boolean':
	            $value = strtolower($parameters['inputObject'][$parameters['variableName']]);
	            if ( ($value == "true") || (is_numeric($value) && ($value > 0)) ) {
	                return true;
	            } else {
	                return false;
	            }
	            break;
	        case 'int':
	            if (!is_numeric($parameters['inputObject'][$parameters['variableName']])) {
	                if (!$parameters['ignoreErrors']) {
	                    throw new InvalidArgumentException('"'.$parameters['variableName'].'" Incorrect type. Expected type: "'.$parameters['variableType'].'" got "'.gettype($parameters['inputObject'][$parameters['variableName']]).'" ("'.$parameters['inputObject'][$parameters['variableName']].'") instead.');
	                }
	                return (int)$parameters['defaultValue'];
	            }
	            break;
	        case 'string':
	            if (!is_string($parameters['inputObject'][$parameters['variableName']])) {
	                if (!$parameters['ignoreErrors']) {
	                    throw new InvalidArgumentException('"'.$parameters['variableName'].'" Incorrect type. Expected type: "'.$parameters['variableType'].'" got "'.gettype($parameters['inputObject'][$parameters['variableName']]).'" ("'.$parameters['inputObject'][$parameters['variableName']].'") instead.');
	                }
	                return $parameters['defaultValue'];
	            }
	            break;
	        case 'array':
	            if (!is_array($parameters['inputObject'][$parameters['variableName']])) {
	                // Create an array if it isnt one already.
	                // Arrays are used with checkboxes and radio buttons.
	                // The problem with them is that if there is only one
	                // checkbox, the given value will not be an array.  So
	                // we make it easy for the programmer by always returning
	                // an array.
	                $newArray = array();
	                $newArray[] = $parameters['inputObject'][$parameters['variableName']];
	                return $newArray;
	            }
	            break;
	        default:
	            throw new \InvalidArgumentException(__METHOD__.': Variable type '.$parameters['variableType'].' is not supported.');
	            break;
	        }
	    return $parameters['inputObject'][$parameters['variableName']];
	} // fn get

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
	    // // print ladybug_dump($content);
	    if (!empty($content))
	    {
	        $params = json_decode($content, true); // 2nd param to get as array
	    }
	    /* else {
	    	throw new ResourceNotModifiedException('Body is empty.');
	    }

	    if (count($params) == 0) {
	    	throw new ResourceNotModifiedException('Body contains no fields.');
	    }*/

	    // print ladybug_dump($params);

	    // $authors = array();
	    // foreach ($params['authors'] as $key => $value) {
	    // 	$authors[] = $value['name'];
	    // }

	    // // print ladybug_dump($authors);

	    // $f_publication_id = Input::Get('f_publication_id', 'int', 0, true);                      // does not exist in JSON
		// $f_issue_number = Input::Get('f_issue_number', 'int', 0, true);                          // does not exist in JSON
		// $f_section_number = Input::Get('f_section_number', 'int', 0, true);                      // does not exist in JSON
		// $f_language_id = Input::Get('f_language_id', 'int', 0, true);                            // does not exist in JSON
		// the Code is provided but not as an ID, should be fetched
		// $f_language_selected = Input::Get('f_language_selected', 'int', 0);                      // does not exist in JSON

		// $clean['f_article_number'] = $params['number'];
		// $clean['f_article_number'] = self::getVar($params, 'number', 'int');
		$clean['f_article_number'] = self::getVar(array('inputObject' => $number, 'variableName' => 'number', 'variableType' => 'int'));
		$clean['f_language_code'] = self::getVar(array('inputObject' => $language, 'variableName' => 'language'));

		$authors = array();
		foreach ($params['authors'] as $key => $value) {
		    $authors[] = self::getVar(array('inputObject' => $value, 'variableName' => 'name'));
		}

		$clean['f_article_author'] = $authors;
		// $f_article_author_type = Input::Get('f_article_author_type', 'array', array(), true);    // does not exist in JSON
		if (self::getVar(array('inputObject' => $params, 'variableName' => 'title', 'checkIfExists' => true))) {
			$clean['f_article_title'] = self::getVar(array('inputObject' => $params, 'variableName' => 'title'));
		}
		if (self::getVar(array('inputObject' => $params, 'variableName' => 'created', 'checkIfExists' => true))) {
			$clean['f_creation_date'] = date_format(date_create_from_format(DATE_ATOM, self::getVar(array('inputObject' => $params, 'variableName' => 'created'))), 'Y-m-d H:i:s');
		}
		if (self::getVar(array('inputObject' => $params, 'variableName' => 'published', 'checkIfExists' => true))) {
			$clean['f_publish_date'] = date_format(date_create_from_format(DATE_ATOM, self::getVar(array('inputObject' => $params, 'variableName' => 'published'))), 'Y-m-d H:i:s');
		}

		
		// $f_comment_status = Input::Get('f_comment_status', 'string', '', true);
		$notBodyFields = array('deck');
		foreach ($params['fields'] as $key => $value) {
			if (!in_array($key, $notBodyFields)) {
			    $clean['F'.$key.'_'.$clean['f_article_number']] = $value;
			} else {
				$clean['F'.$key] = $value;
			}
		}

		// print ladybug_dump($clean);

		$g_user = $this->getUser();
		// print ladybug_dump($g_user);

	    /*
	     * This is copied from admin-files/articles/post.php
	     */

	    require_once($GLOBALS['g_campsiteDir'].'/classes/Article.php');

	    $translator = \Zend_Registry::get('container')->getService('translator');

	    // Fetch article
		$articleObj = new \Article(1, $clean['f_article_number']);

		if (!$articleObj->exists()) {
			throw new NewscoopException('Article does not exist');
		}

		if (!$articleObj->userCanModify($g_user)) {
			throw new AccessDeniedException('User cannot modify article.');
		}

		// Only users with a lock on the article can change it.
		if ($articleObj->isLocked() && ($g_user->getUserId() != $articleObj->getLockedByUser())) {
			$lockTime = new \DateTime($articleObj->getLockTime());
			$now = new \DateTime("now");
			$difference = $now->diff($lockTime);
			$ago = $difference->format("%R%H:%I:%S");
			$lockUser = new \User($articleObj->getLockedByUser());
			throw new NewscoopException(sprintf('Article locked by %s (%s ago)', $lockUser->getRealName(), $ago));
		}

		$articleTypeObj = $articleObj->getArticleData();
		$dbColumns = $articleTypeObj->getUserDefinedColumns(false, true);

		$articleFields = array();
		foreach ($dbColumns as $dbColumn) {
		    if ($dbColumn->getType() == \ArticleTypeField::TYPE_BODY) {
		        $dbColumnParam = $dbColumn->getName() . '_' . $clean['f_article_number'];
		        // print ladybug_dump('body: '. $dbColumnParam);
		    } else {
		        $dbColumnParam = $dbColumn->getName();
		        // print ladybug_dump('not body: '. $dbColumnParam);
		    }
		    
		    if (isset($clean[$dbColumnParam])) {
		        if($dbColumn->getType() == \ArticleTypeField::TYPE_TEXT
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

		// if (!empty($f_message)) {
		// 	camp_html_add_msg($f_message, "ok");
		// }

		// Update the article author
		    // $blogService = \Zend_Registry::get('container')->getService('blog');
		    // $blogInfo = $blogService->getBlogInfo($g_user);
		    // if (!empty($f_article_author)) {
		    //     ArticleAuthor::OnArticleLanguageDelete($articleObj->getArticleNumber(), $articleObj->getLanguageId());
		    //     $i = 0;
		    //     forclean['each'] ($f_article_author as $author) {
		    //         $authorObj = new Author($author);
		    //         if (!$authorObj->exists() && strlen(trim($author)) > 0) {
		    //             if ($blogService->isBlogger($g_user)) { // blogger can't create authors
		    //                 continue;
		    //             }

		    //             $authorData = Author::ReadName($author);
		    //             $authorObj->create($authorData);
		    //         } elseif ($blogService->isBlogger($g_user)) { // test if using authors from blog
		    //             if (!$blogService->isBlogAuthor($authorObj, $blogInfo)) {
		    //                 continue;
		    //             }
		    //         }

		    //         // Sets the author type selected
		    //         $author_type = $f_article_author_type[$i];
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
		$articleObj->setTitle($clean['f_article_title']);
		$articleObj->setIsIndexed(false);

		if (!empty($f_comment_status)) {
		    if ($f_comment_status == "enabled" || $f_comment_status == "locked") {
		        $commentsEnabled = true;
		    } else {
		        $commentsEnabled = false;
		    }
		    // If status has changed, then you need to show/hide all the comments
		    // as appropriate.
		    if ($articleObj->commentsEnabled() != $commentsEnabled) {
			    $articleObj->setCommentsEnabled($commentsEnabled);
		        global $controller;
		        $repository = $controller->getHelper('entity')->getRepository('Newscoop\Entity\Comment');
			    $repository->setArticleStatus($clean['f_article_number'], $f_language_selected, $commentsEnabled?STATUS_APPROVED:STATUS_HIDDEN);
			    $repository->flush();
		    }
		    $articleObj->setCommentsLocked($f_comment_status == "locked");
		}

		// Make sure that the time stamp is updated.
		$articleObj->setProperty('time_updated', 'NOW()', true, true);

		// Verify creation date is in the correct format.
		// If not, dont change it.
		// if (preg_match("/\d{4}-\d{2}-\d{2}/", $f_creation_date)) {
		// 	$articleObj->setCreationDate($f_creation_date);
		// }

		// Verify publish date is in the correct format.
		// If not, dont change it.
		if (preg_match("/\d{4}-\d{2}-\d{2}/", $clean['f_publish_date'])) {
			$articleObj->setPublishDate($clean['f_publish_date']);
		}

		foreach ($articleFields as $dbColumnName => $text) {
		    $articleTypeObj->setProperty($dbColumnName, $text);
		}

		$this->get('ladybug')->log($articleObj);
		$this->get('ladybug')->log($articleTypeObj);
		// print ladybug_dump($articleObj);
		// print ladybug_dump($articleTypeObj);
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
     * @Route("/articles.{_format}", defaults={"_format"="json"})
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
     *
     * Get article
     *
     * @ApiDoc(
     *     statusCodes={
     *         200="Returned when successful",
     *         404={
     *           "Returned when the article is not found",
     *         }
     *     },
     *     parameters={
     *         {"name"="number", "dataType"="integer", "required"=true, "description"="Article number"}
     *     },
     *     filters={
     *          {"name"="language", "dataType"="string", "description"="Language code"}
     *     },
     *     output="\Newscoop\Entity\Article"
     * )
     *
     * @Route("/articles/{number}.{_format}", defaults={"_format"="json"})
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

        return $article;
    }

    /**
     * Link resource with Article entity
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
     * @Route("/articles/{number}/{language}.{_format}", defaults={"_format"="json"})
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
            throw NotFoundHttpException('Article was not found');
        }

        foreach ($request->attributes->get('links') as $key => $object) {
            if ($object instanceof \Exception) {
                throw $object;
            }

            if ($object instanceof \Newscoop\Image\LocalImage) {
                $imagesService = $this->get('image');
                $imagesService->addArticleImage($article->getNumber(), $object);

                continue;
            }

            if ($object instanceof \Newscoop\Entity\Attachment) {
                $attachmentService = $this->get('attachment');
                $attachmentService->addAttachmentToArticle($article, $object);

                continue;
            }
        }
    }

    /**
     * Unlink resource from Article
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
     * @Route("/articles/{number}.{_format}", defaults={"_format"="json"})
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
            throw NotFoundHttpException('Article was not found');
        }

        foreach ($request->attributes->get('links') as $key => $object) {
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
                }

                continue;
            }

            if ($object instanceof \Newscoop\Entity\Attachment) {
                $attachmentService = $this->get('attachment');
                $attachmentService->removeAttachmentFormArticle($article, $object);

                continue;
            }
        }
    }

    /**
     * @Route("/articles/{number}/{language}.{_format}", defaults={"_format"="json"})
     * @Method("PATCH")
     * @View()
     *
     * @return Form
     */
    public function setArticleAction(Request $request, $number, $language)
    {
        return $this->processForm($request, $number, $language);
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
