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

	public static function GetVar($inputArray, $p_varName, $p_type = 'string', $p_defaultValue = null, $p_errorsOk = false)
	{
		global $g_inputErrors;
	    $p_type = strtolower($p_type);

	    if ($p_type == 'checkbox') {
	        if (strtolower($p_defaultValue) != 'numeric') {
	            return isset($inputArray[$p_varName]);
	        } else {
	            return isset($inputArray[$p_varName]) ? '1' : '0';
	        }
	    }
		if (!isset($inputArray[$p_varName])) {
			if (!$p_errorsOk) {
				$g_inputErrors[$p_varName] = 'not set';
			}
			return $p_defaultValue;
		}
		// Clean the slashes
		if (get_magic_quotes_gpc()) {
			if (is_array($inputArray[$p_varName])) {
				$inputArray[$p_varName] = self::CleanMagicQuotes($inputArray[$p_varName]);
			} else {
				$inputArray[$p_varName] = stripslashes($inputArray[$p_varName]);
			}
		}
		switch ($p_type) {
		case 'boolean':
			$value = strtolower($inputArray[$p_varName]);
			if ( ($value == "true") || (is_numeric($value) && ($value > 0)) ) {
				return true;
			} else {
				return false;
			}
			break;
		case 'int':
			if (!is_numeric($inputArray[$p_varName])) {
				if (!$p_errorsOk) {
					$g_inputErrors[$p_varName] = 'Incorrect type.  Expected type '.$p_type
						.', but received type '.gettype($inputArray[$p_varName]).'.'
						.' Value is "'.$inputArray[$p_varName].'".';
				}
				return (int)$p_defaultValue;
			}
			break;
		case 'string':
			if (!is_string($inputArray[$p_varName])) {
				if (!$p_errorsOk) {
					$g_inputErrors[$p_varName] = 'Incorrect type.  Expected type '.$p_type
						.', but received type '.gettype($inputArray[$p_varName]).'.'
						.' Value is "'.$inputArray[$p_varName].'".';
				}
				return $p_defaultValue;
			}
			break;
		case 'array':
			if (!is_array($inputArray[$p_varName])) {
				// Create an array if it isnt one already.
				// Arrays are used with checkboxes and radio buttons.
				// The problem with them is that if there is only one
				// checkbox, the given value will not be an array.  So
				// we make it easy for the programmer by always returning
				// an array.
				$newArray = array();
				$newArray[] = $inputArray[$p_varName];
				return $newArray;
			}
		}
		return $inputArray[$p_varName];
	} // fn get

	/**
	 * Write Article
	 *
	 * @ApiDoc(
     *     statusCodes={
     *         201="Returned when Article is created"
     *     },
     *     parameters={
     *         {"name"="access_token", "dataType"="string", "required"=true, "description"="oAuth Access Token"}
     *     }
     * )
     *
     * @Route("/articles.{_format}", defaults={"_format"="json"})
     * @Method("POST")
	 */
	public function postArticleAction(Request $request)
    {
    	$params = array();
	    $content = $this->get("request")->getContent();
	    // print ladybug_dump($content);
	    if (!empty($content))
	    {
	        $params = json_decode($content, true); // 2nd param to get as array
	    }

	    print ladybug_dump($params);

	    // $authors = array();
	    // foreach ($params['authors'] as $key => $value) {
	    // 	$authors[] = $value['name'];
	    // }

	    // print ladybug_dump($authors);

	    // $f_publication_id = Input::Get('f_publication_id', 'int', 0, true);                      // does not exist in JSON
		// $f_issue_number = Input::Get('f_issue_number', 'int', 0, true);                          // does not exist in JSON
		// $f_section_number = Input::Get('f_section_number', 'int', 0, true);                      // does not exist in JSON
		// $f_language_id = Input::Get('f_language_id', 'int', 0, true);                            // does not exist in JSON
		// the Code is provided but not as an ID, should be fetched
		// $f_language_selected = Input::Get('f_language_selected', 'int', 0);                      // does not exist in JSON

		$clean['f_article_number'] = $params['number'];

		$authors = array();
		foreach ($params['authors'] as $key => $value) {
		    $authors[] = $value['name'];
		}

		$clean['f_article_author'] = $authors;
		// $f_article_author_type = Input::Get('f_article_author_type', 'array', array(), true);    // does not exist in JSON
		$clean['f_article_title'] = $params['title'];
		// $f_message = Input::Get('f_message', 'string', '', true);                                // does not exist in JSON
		// $f_creation_date = Input::Get('f_creation_date');                                        // does not exist in JSON
		$clean['f_publish_date'] = date_format(date_create_from_format(DATE_ATOM, $params['published']), 'Y-m-d H:i:s');
		$clean['f_creation_date'] = date_format(date_create_from_format(DATE_ATOM, $params['published']), 'Y-m-d H:i:s');
		// $f_comment_status = Input::Get('f_comment_status', 'string', '', true);
		$notBodyFields = array('deck');
		foreach ($params['fields'] as $key => $value) {
			if (!in_array($key, $notBodyFields)) {
			    $clean['F'.$key.'_'.$clean['f_article_number']] = $value;
			} else {
				$clean['F'.$key] = $value;
			}
		}

		print ladybug_dump($clean);

		$g_user = $this->getUser();

	    /*
	     * This is copied from admin-files/articles/post.php
	     */

	    require_once($GLOBALS['g_campsiteDir'].'/classes/Article.php');

	    $translator = \Zend_Registry::get('container')->getService('translator');

	    // Fetch article
		$articleObj = new \Article(1, $params['number']);
		print ladybug_dump($articleObj);
		if (!$articleObj->exists()) {
			camp_html_display_error($translator->trans('No such article.', array(), 'articles'), $BackLink);
			exit;
		}

		$articleTypeObj = $articleObj->getArticleData();
		$dbColumns = $articleTypeObj->getUserDefinedColumns(false, true);

		$articleFields = array();
		foreach ($dbColumns as $dbColumn) {
		    if ($dbColumn->getType() == \ArticleTypeField::TYPE_BODY) {
		        $dbColumnParam = $dbColumn->getName() . '_' . $clean['f_article_number'];
		        print ladybug_dump('body: '. $dbColumnParam);
		    } else {
		        $dbColumnParam = $dbColumn->getName();
		        print ladybug_dump('not body: '. $dbColumnParam);
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

		if (!empty($f_message)) {
			camp_html_add_msg($f_message, "ok");
		}

		// if (!$articleObj->userCanModify($g_user)) {
		// 	camp_html_add_msg($translator->trans("You do not have the right to change this article.  You may only edit your own articles and once submitted an article can only be changed by authorized users.", array(), 'articles'));
		// 	camp_html_goto_page($BackLink);
		// 	exit;
		// }
		// Only users with a lock on the article can change it.
		// if ($articleObj->isLocked() && ($g_user->getUserId() != $articleObj->getLockedByUser())) {
		// 	$diffSeconds = time() - strtotime($articleObj->getLockTime());
		// 	$hours = floor($diffSeconds/3600);
		// 	$diffSeconds -= $hours * 3600;
		// 	$minutes = floor($diffSeconds/60);
		// 	$lockUser = new User($articleObj->getLockedByUser());
		// 	camp_html_add_msg($translator->trans('Could not save the article. It has been locked by $1 $2 hours and $3 minutes ago.', array('$1' => $lockUser->getRealName(), '$2' => $hours, '$3' => $minutes), 'articles'));
		// 	camp_html_goto_page($BackLink);
		// 	exit;
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

		print ladybug_dump($articleObj);
		print ladybug_dump($articleTypeObj);



		/* this was an experiment */
    	// error_log(__METHOD__);
    	// error_log('testing');
    	// error_log(print_r($request, true));

    	// print ladybug_dump($request);
    	// // $cookies = $request->cookies->all();
    	// // print ladybug_dump($cookies);
    	// $headers = $request->headers->all();
    	// print ladybug_dump($headers);
    	// $cookie = $request->headers->get('cookie');
    	// print ladybug_dump($cookie);
    	// $host = $request->headers->get('host');
    	// print ladybug_dump($host);


    	// $path_to_legacy_code = $this->container->getParameter('kernel.root_dir').'/../admin-files/articles/post.php';
    	// // print ladybug_dump($path_to_legacy_code);
    	// $url = $path_to_legacy_code;
    	// // $url = 'file:///home/twisted/www/newscoop/admin-files/articles/post.php';
    	// $url = 'http://www.newscooptest.com/admin/articles/post.php';
    	// print ladybug_dump($url);

    	// //open connection
     //    $ch = curl_init();
     //    curl_setopt($ch, CURLOPT_URL, $url);
     //    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
     //    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
     //    curl_setopt($ch, CURLOPT_HEADER, 0);
     //    curl_setopt($ch, CURLOPT_VERBOSE, 1);

        // logs the connection (optional)
        // $stderr = fopen("{$this->container->getParameter('kernel.root_dir')}/../log/curl.txt", "w");
        // print ladybug_dump($this->container->getParameter('kernel.root_dir'));

        // $browser = new \Buzz\Browser();
        // $response = $browser->get('http://www.newscooptest.com/admin/articles/post.php');
//        $request = new \Buzz\Message\Request('GET', '/admin/articles/post.php', 'http://'.$host);
        // $request = new \Buzz\Message\Request('GET', '/../admin-files/articles/post.php', $this->container->getParameter('kernel.root_dir'));
        // $request->addHeader('Cookie: PHPSESSID=usp8eaek3p1n3r3mocfh2ej2j7');
        // foreach ($cookies as $key => $value) {
    		// $request->addHeader('Cookie: '.$key.'='.$value);
    	// }




		// $request->addHeader('cookie: '.$cookie);

		// print ladybug_dump($request);

		// $response = new \Buzz\Message\Response();

		// $client = new \Buzz\Client\FileGetContents();
		// $client->send($request, $response);

		// print ladybug_dump($request);
		// print ladybug_dump($response);

		// $postParameters['foo'] = 'bar';







        // $postParametersString = '';
        // foreach ($postParameters as $key => $value) {
        //     $postParametersString .= $key . '=' . $value . '&';
        // }
        // rtrim($postParametersString, '&');

  //       curl_setopt($ch, CURLOPT_POST, count($postParameters));
  //       curl_setopt($ch, CURLOPT_POSTFIELDS, $postParametersString);

  //       curl_setopt($ch, CURLOPT_COOKIESESSION, 0);
		// curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');
		// curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies.txt');

        // print ladybug_dump(curl_getinfo($ch));
        // $result = curl_exec($ch);
        // print ladybug_dump(curl_getinfo($ch));
        // print ladybug_dump(curl_error($ch));
        // curl_close($ch);

        // print ladybug_dump($result);

    	
    	// print ladybug_dump($request->request->all());
    	// return print_r($request, true);
    	// print ladybug_dump($request);
    	// return json_encode($request->request);
        // $em = $this->container->get('em');
        // $publication = $this->get('newscoop_newscoop.publication_service')->getPublication();

        // $article = $em->getRepository('Newscoop\Entity\Article')
        //     ->getArticle($number, $request->get('language', $publication->getLanguage()->getCode()))
        //     ->getOneOrNullResult();

        // return $article;
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
