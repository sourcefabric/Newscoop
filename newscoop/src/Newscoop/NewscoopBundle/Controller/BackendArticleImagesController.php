<?php
/**
 * @package Newscoop\NewscoopBundle
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2014 Sourcefabric z.ú.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\NewscoopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Newscoop\NewscoopBundle\Form\Type\ArticleImageType;

class BackendArticleImagesController extends Controller
{
    /**
     * @Route("/admin/articles/{language}/{articleNumber}/images/{imageId}/edit", name="newscoop_newscoop_article_images_edit")
     * @Template()
     */
    public function editAction(Request $request, $language, $articleNumber, $imageId)
    {
        $em = $this->get('em');
        $imageService = $this->container->get('image');
        $articleImage = $imageService->getArticleImage($articleNumber, $imageId);
        $preferencesService = $this->container->get('preferences');

        $form = $this->container->get('form.factory')->create(new ArticleImageType(), array(
            'number' => $articleImage->getNumber(),
            'caption' => $articleImage->getCaption($language),
            'language' => $language,
            'status' => $articleImage->getImage()->getStatus(),
            'description' => $articleImage->getImage()->getDescription(),
            'photographer' => $articleImage->getImage()->getPhotographer(),
            'photographer_url' => $articleImage->getImage()->getPhotographerUrl(),
            'place' => $articleImage->getImage()->getPlace(),
            'date' => $articleImage->getImage()->getDate(),
        ), array(
            'action' => $this->generateUrl('newscoop_newscoop_article_images_edit', array(
                'language' => $language,
                'articleNumber' => $articleNumber,
                'imageId' => $imageId
            )),
            'method' => 'POST',
            'rich_text_caption' => $preferencesService->MediaRichTextCaptions,
        ));

        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $data = $form->getData();
                $data['user'] = $this->container->get('user')->getCurrentUser();
                $imageService->fillImage($articleImage->getImage(), $data);
                $imageService->saveArticleImage($articleImage, $data);

                return new RedirectResponse($this->generateUrl('newscoop_newscoop_article_images_edit', array(
                    'language' => $language,
                    'articleNumber' => $articleNumber,
                    'imageId' => $imageId
                )));
            }
        }

        $editor = '';
        if ($preferencesService->MediaRichTextCaptions == 'Y') {

            $editorLanguage = $request->getLocale();
            $editorOptions  = array(
                'max_chars' => $preferencesService->MediaCaptionLength,
                'toolbar_length' => 10,
            );

            $userService = $this->container->get('user');

            ob_start();
            require_once($GLOBALS['g_campsiteDir']."/admin-files/media-archive/editor_load_tinymce.php");
            editor_load_tinymce('article_image_caption, article_image_description', $userService->getCurrentUser(), $editorLanguage, $editorOptions);
            $editor = ob_get_contents();
            ob_end_clean();
        }

        return array(
            'form' => $form->createView(),
            'imageService' => $imageService,
            'articleImage' => $articleImage,
            'image' => $articleImage->getImage(),
            'caption' => $articleImage->getCaption($language),
            'captions' => $articleImage->getImage()->getCaptions(),
            'editor' => $editor,
            'rich_text_caption' => $preferencesService->MediaRichTextCaptions,
        );
    }
}
