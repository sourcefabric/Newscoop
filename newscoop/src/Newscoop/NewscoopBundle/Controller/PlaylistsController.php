<?php
/**
 * @package Newscoop\NewscoopBundle
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */
namespace Newscoop\NewscoopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Newscoop\Entity\Article;
use Doctrine\ORM\EntityNotFoundException;

/**
 * Playlists controller.
 */
class PlaylistsController extends Controller
{
    /**
     * @Route("/admin/playlists/")
     * @Route("/admin/playlists/{articleNumber}/{language}/editor-view/",
     * 		options={"expose"=true},
     * 		name="newscoop_newscoop_playlists_editor"
     * )
     */
    public function indexAction(Request $request, $articleNumber = null, $language = null)
    {
        $preferencesService = $this->get('preferences');
        $em = $this->get('em');
        $user = $this->get('user')->getCurrentUser();
        if (!$user->hasPermission('ManagePlaylist')) {
            throw new AccessDeniedException();
        }

        $clientName = 'newscoop_'.$preferencesService->SiteSecretKey;
        $client = $em->getRepository('\Newscoop\GimmeBundle\Entity\Client')->findOneByName($clientName);

        $editorView = false;
        if ($request->get('_route') === "newscoop_newscoop_playlists_editor") {
            $editorView = true;
        }

        return $this->render('NewscoopNewscoopBundle:Playlists:index.html.twig', array(
            'clientId' => $client ? $client->getPublicId() : '',
            'redirectUris' => $client->getRedirectUrisString(),
            'editorView' => $editorView,
            'articleNumber' => $articleNumber,
            'language' => $language,
        ));
    }

    /**
     * @Route("/admin/playlist/article-preview", options={"expose"=true})
     */
    public function articlePreviewAction(Request $request)
    {
        $em = $this->get('em');
        $router = $this->get('router');
        $number = $request->get('id');
        $language = $request->get('lang');
        $article = $em->getRepository('Newscoop\Entity\Article')->getArticle($number, $language)->getOneOrNullResult();

        if (!$article) {
            throw new EntityNotFoundException();
        }

        return new RedirectResponse($this->createArticleLegacyPreviewUrl($article));
    }

    private function createArticleLegacyPreviewUrl(Article $article)
    {
        $params = array(
            'f_publication_id'      => $article->getPublicationId(),
            'f_issue_number'        => $article->getIssueId(),
            'f_section_number'      => $article->getSectionId(),
            'f_article_number'      => $article->getNumber(),
            'f_language_id'         => $article->getLanguageId(),
            'f_language_selected'   => $article->getLanguageId(),
        );

        return "/admin/articles/get.php?".http_build_query($params);
    }
}
