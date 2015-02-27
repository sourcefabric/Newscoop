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
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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
            'editorView' => $editorView,
            'articleNumber' => $articleNumber,
            'language' => $language,
        ));
    }
}
